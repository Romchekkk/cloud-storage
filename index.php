<?php

require_once "additionalFunctions.php";
require_once "dataBaseFunctions.php";

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
        $_SESSION['username'] = $username;
        $_SESSION['path'] = "localStorage/".$username;
        $_SESSION['availablespace'] = 104857600;
        if (!addToAccessrights($mysql, "localStorage/".$username)){
            print("Возникла ошибка во время выполнения. Попробуйте обновить страницу.");
            die();
        }
        mysqli_close($mysql);
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
    $html = file_get_contents("html_patterns/reg.html");
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
    $usernameHTML = $_SESSION['username'];
    $result = mysqli_query($mysql, "SELECT * FROM `accessrights` WHERE path='localStorage/$usernameHTML'");
    if ($result){
        $user = mysqli_fetch_array($result);
        $rootAccessMod = $user[2];
    }
    else{
        // Обработка ошибки
    }
    $menuHTML = "<td>
    Доступно места: <span id=\"availablespace\">".$_SESSION['availablespace']."</span> Байт<br />
    <input type=\"button\" value=\"Назад\" onclick=\"goBack()\" /><br />
    Путь: <span id=\"path\">".explode("/", $_SESSION['path'], 2)[1]."</span>
    </td>
    <td>
        Уровень доступа к вашей<br />
        корневой папке: <form><input type=\"text\" id=\"accessRootMod\" disabled=\"disabled\" name=\"accessmod\" value=\"$rootAccessMod\" />
        <input type=\"button\" value=\"Изменить\" onclick=\"changeRootDirMod(this.form.accessmod.value)\"></form>
    </td>
    <td>
        <input type=\"text\" id=\"dirName\" /><input type=\"button\" value=\"Создать директорию\" onclick=\"createDirectory()\" />
    </td>
    <td>
        <form method=\"post\" enctype=\"multipart/form-data\">
        <input type=\"file\" name=\"file\" /><input type=\"button\" value=\"Загрузить файл\" onclick=\"uploadFile(this.form.file)\" />
        </form>
    </td>";
    $windowHTML = newWindow($_SESSION['path'], $_SESSION['username']);
    $usersHTML = "";
    $i = 0;
    foreach($usersArr as $user){
        if ($i == 15){
            break;
        }
        $accessRights = checkAccessRights($mysql, "localStorage/".$user['username'], $_SESSION['username']);
        if($accessRights === 0 || $accessRights === 1 || $accessRights === 2){
            $usersHTML .= "<li class=\"open\"><input type=\"button\" value=\"".$user['username']."\" onclick=\"openUser(this.value)\" /></li>";
        }
        else{
            $usersHTML .= "<li class=\"close\"><input type=\"button\" value=\"".$user['username']."\" /></li>";
        }
        $i++;
    }
    mysqli_close($mysql);
    $html = file_get_contents("html_patterns/main.html");
    foreach(array('usernameHTML', 'menuHTML', 'windowHTML', 'usersHTML') as $value){
        $html = preg_replace("/$value/uis", $$value, $html);
    }
}

print($html);
