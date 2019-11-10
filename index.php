<?php

require_once("createNewWindow.php");
require_once("dataBaseFunctions.php");
session_start();


foreach(array('action', 'username', 'email', 'password') as $parameterName){
    $$parameterName = isset($_POST[$parameterName])
    ? trim($_POST[$parameterName])
    : "";
}

if ($action && $action == "Выйти"){
    session_destroy();
    $_SESSION = array();
    setcookie("cloudStorage", "", time()-3600);
    header("Location: ".$_SERVER["PHP_SELF"]);
    die();
}

if ($action && $action == "Зарегистрироваться" && $username && $email && $password){
    if (preg_match("/^[a-z0-9]{3,25}$/uis", $username)){
        $ini = parse_ini_file("database/mysql.ini");
        $mysql = mysqli_connect($ini['host'], $ini['user'], $ini['password'], $ini['database']);
        if(!$mysql){
            print("Возникла ошибка во время выполнения. Попробуйте обновить страницу.");
            die();
        }
        $usersArr = getUsers($mysql);
        if(count($usersArr) == 0){
            print("Возникла ошибка во время выполнения. Попробуйте обновить страницу.");
            die();
        }
        foreach ($usersArr as $user) { 
            if ($user['username'] == $username){
                $_SESSION['error'] = "Имя пользователя занято!";
                header("Location: ".$_SERVER["PHP_SELF"]);
                die();
            }
            elseif ($user['email'] == $email){
                $_SESSION['error'] = "Такая почта уже зарегистрирована!";
                header("Location: ".$_SERVER["PHP_SELF"]);
                die();
            }
        }
        $secretKey = uniqid();
        $password .= $secretKey;
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        if (!mysqli_query($mysql, "INSERT INTO `users`(`username`, `email`, `password`, `secretkey`) VALUES ('$username', '$email', '$passwordHash', '$secretKey')")){
            print("Возникла ошибка во время выполнения. Попробуйте обновить страницу.");
            die();
        }
        mysqli_close($mysql);
        $_SESSION['username'] = $username;
        $_SESSION['path'] = "localStorage/".$username;
        $_SESSION['availablespace'] = 104857600;
        mkdir($_SESSION['path']);
        $forcoockie = $secretKey.$_SERVER['REMOTE_ADDR'];
        setcookie("cloudStorage", $email.':'.password_hash($forcoockie, PASSWORD_DEFAULT), time()+60*60*24);
        header("Location: ".$_SERVER["PHP_SELF"]);
        die();
    }
}

if ($action && $action == "Войти" && $email && $password){
    $ini = parse_ini_file("database/mysql.ini");
    $mysql = mysqli_connect($ini['host'], $ini['user'], $ini['password'], $ini['database']);
    if(!$mysql){
        print("Возникла ошибка во время выполнения. Попробуйте обновить страницу.");
        die();
    }
    $user = getConcreteUser($mysql, "email", $email);
    $passwordCheck = $password.$user['secretkey'];
    if (password_verify($passwordCheck, $user["password"])){
        $secretKey = uniqid();
        $newPassword = $password.$secretKey;
        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $update = mysqli_query($mysql, "UPDATE `users` SET `password`='$newPasswordHash', `secretKey`='$secretKey'  WHERE `email`='$email'");
        mysqli_close($mysql);
        if ($update){
            $_SESSION['username'] = $user["username"];
            $_SESSION['path'] = "localStorage/".$_SESSION["username"];
            $_SESSION['availablespace'] = $user["availablespace"];
            $forcoockie = $secretKey.$_SERVER['REMOTE_ADDR'];
            setcookie("cloudStorage", $email.':'.password_hash($forcoockie, PASSWORD_DEFAULT), time()+60*60*24);
            header ("Location: ".$_SERVER["PHP_SELF"]);
            die();
        }
        else{
            $_SESSION['error'] = "Произошла непредвиденная ошибка :(\nПопробуйте еще раз";
            header("Location: ".$_SERVER["PHP_SELF"]);
            die();
        }
    }
    else{
        $_SESSION['error'] = "Неверный логин или пароль!";
        header("Location: ".$_SERVER["PHP_SELF"]);
        die();
    }
}

if(!checkCoockie()){
    $html = file_get_contents("html_patterns/reg.txt");
    $formHTML = "<div id=\"regForm\">
    <form action=\"\" method=\"post\">
        <p>Регистрация</p>
        <p id=\"error\">";
    $formHTML .= isset($_SESSION['error'])
        ? $_SESSION['error']
        : "";
    $formHTML .= "</p>
        <input class=\"reglog\" type=\"text\" name=\"username\" placeholder=\"Имя пользователя\" /><br />
        <input class=\"reglog\" required=\"required\" type=\"email\" name=\"email\" placeholder=\"Адрес электронной почты\" /><br />
        <input class=\"reglog\" required=\"required\" type=\"password\" name=\"password\" placeholder=\"Пароль\" /><br />
        <input type=\"submit\" name=\"action\" value=\"Зарегистрироваться\" /><input type=\"submit\" name=\"action\" value=\"Войти\" />
    </form>
</div>";
    $html = preg_replace("/formHTML/uis", $formHTML, $html);
}
else{
    $usersArr = array();
    $ini = parse_ini_file("database/mysql.ini");
    $mysql = mysqli_connect($ini['host'], $ini['user'], $ini['password'], $ini['database']);
    if(!$mysql){
        print("Возникла ошибка во время выполнения. Попробуйте обновить страницу.");
        die();
    }
    $usersArr = getUsers($mysql);
    if(count($usersArr) == 0){
        print("Возникла ошибка во время выполнения. Попробуйте обновить страницу.");
        die();
    }
    mysqli_close($mysql);
    $usernameHTML = $_SESSION['username'];
    $menuHTML = "<td>
Доступно места: <span id=\"availablespace\">".$_SESSION['availablespace']."</span> Байт<br />
<input type=\"button\" value=\"Назад\" onclick=\"goBack()\" />
</td>
<td>
    <input type=\"text\" id=\"dirName\" /><input type=\"button\" value=\"Создать директорию\" onclick=\"createDirectory()\" />
</td>
<td>
    <form method=\"post\" enctype=\"multipart/form-data\">
    <input type=\"file\" name=\"file\" /><input type=\"button\" value=\"Загрузить файл\" onclick=\"uploadFile(this.form.file)\" />
    </form>
</td>";
    $windowHTML = newWindow($_SESSION['path']);

    $usersHTML = "";
    foreach($usersArr as $user){
        $usersHTML .= "<li class=\"close\">".$user["username"]."</li>";
    }

    $html = file_get_contents("html_patterns/html.txt");
    foreach(array('usernameHTML', 'menuHTML', 'windowHTML', 'usersHTML') as $value){
        $html = preg_replace("/$value/uis", $$value, $html);
    }
}

print($html);

function checkCoockie(){
    if (isset($_COOKIE["cloudStorage"])){
        $coockieArr = explode(":",$_COOKIE["cloudStorage"]);
        $email = $coockieArr[0];
        $ini = parse_ini_file("database/mysql.ini");
        $mysql = mysqli_connect($ini['host'], $ini['user'], $ini['password'], $ini['database']);
        if(!$mysql){
            print("Возникла ошибка во время выполнения. Попробуйте обновить страницу.");
            die();
        }
        $secretKey = "";
        $user = getConcreteUser($mysql, "email", $email);
        if(count($user) != 0){
            $secretKey = $user['secretkey'];
        }
        else{
            mysqli_close($mysql);
            return false;
        }
        $coockieKeyHash = $coockieArr[1];
        $key = $secretKey.$_SERVER["REMOTE_ADDR"];
        if (password_verify($key, $coockieKeyHash)){
                $_SESSION['username'] = $user["username"];
                $_SESSION['path'] = "localStorage/".$_SESSION["username"];
                $_SESSION['availablespace'] = $user["availablespace"];
                mysqli_close($mysql);
        }
        else{
            mysqli_close($mysql);
            return checkCoockie();
        }
        return true;
    }
    else{
        return false;
    }
}