<?php

require_once("createNewWindow.php");

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

if ($action && $action == "Зарегистрироваться" && $username){
    if (preg_match("/^[a-z0-9]{3,25}$/uis", $username)){
        $ini = parse_ini_file("mysql.ini");
        $mysql = mysqli_connect($ini['host'], $ini['user'], $ini['password'], $ini['database']);

        if(!$mysql){
            print("Возникла ошибка во время выполнения. Попробуйте обновить страницу.");
            header("Location: ".$_SERVER["PHP_SELF"]);
            die();
        }
        if($stmt = mysqli_prepare($mysql, "SELECT `username` from `users`")){
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $user);
            while (mysqli_stmt_fetch($stmt)){
                if ($user == $username){
                    $_SESSION['error'] = "Имя пользователя занято!";
                    header("Location: ".$_SERVER["PHP_SELF"]);
                    die();
                }
            }
            mysqli_stmt_close($stmt);
        }
        else{
            print("Возникла ошибка во время выполнения. Попробуйте обновить страницу.");
            header("Location: ".$_SERVER["PHP_SELF"]);
            die();
        }

        if($stmt = mysqli_prepare($mysql, "SELECT `email` from `users`")){
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $user);
            while (mysqli_stmt_fetch($stmt)){
                if ($user == $email){
                    $_SESSION['error'] = "Логин занят!";
                    header("Location: ".$_SERVER["PHP_SELF"]);
                    die();
                }
            }
            mysqli_stmt_close($stmt);
        }
        else{
            print("Возникла ошибка во время выполнения. Попробуйте обновить страницу.");
            header("Location: ".$_SERVER["PHP_SELF"]);
            die();
        }

        $secretKey = uniqid();
        $password .= $secretKey;
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $insert = "INSERT INTO `users`(`username`, `email`, `password`, `secretkey`) VALUES ('$username', '$email', '$passwordHash', '$secretKey')";
        mysqli_query($mysql, $insert);
        mysqli_close($mysql);
        $_SESSION['username'] = $username;
        $_SESSION['path'] = "localStorage/".$username;
        $_SESSION['availableSpace'] = "104857600 Байт";
        mkdir($_SESSION['path']);
        $forcoockie = $secretKey.$_SERVER['REMOTE_ADDR'];
        setcookie("cloudStorage", $email.':'.password_hash($forcoockie, PASSWORD_DEFAULT), time()+60*60*24);
        header("Location: ".$_SERVER["PHP_SELF"]);
        die();
    }
}

if ($action && $action == "Войти"){
    $ini = parse_ini_file("mysql.ini");
    $mysql = mysqli_connect($ini['host'], $ini['user'], $ini['password'], $ini['database']);
    if(!$mysql){
        print("Возникла ошибка во время выполнения. Попробуйте обновить страницу.");
        header("Location: ".$_SERVER["PHP_SELF"]);
        die();
    }
    $result = mysqli_query($mysql, "SELECT * FROM `users` WHERE email='$email'");
    if($result){
        $userData = mysqli_fetch_array($result);
        $passwordCheck = $password.$userData['secretkey'];
        if (password_verify($passwordCheck, $userData["password"])){
            $secretKey = uniqid();
            $newPassword = $password.$secretKey;
            $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $update = mysqli_query($mysql, "UPDATE `users` SET `password`='$newPasswordHash', `secretKey`='$secretKey'  WHERE `email`='$email'");
            mysqli_close($mysql);
            if ($update){
                $_SESSION['username'] = $userData["username"];
                $_SESSION['path'] = "localStorage/".$_SESSION["username"];
                $_SESSION['availableSpace'] = $userData["availablespace"]." Байт";
                $forcoockie = $secretKey.$_SERVER['REMOTE_ADDR'];
                setcookie("cloudStorage", $email.':'.password_hash($forcoockie, PASSWORD_DEFAULT), time()+60*60*24);
                header ("Location: ".$_SERVER["PHP_SELF"]);
                die();
            }
            else{
                $update_error = TRUE;
            }
        }
        else{
            $error_pass = TRUE;
        }
    }
    else{
    $error_login = TRUE;
    }
}

if(!checkCoockie()){
    $html = file_get_contents("temp/reg.txt");
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
    $ini = parse_ini_file("mysql.ini");
    $mysql = mysqli_connect($ini['host'], $ini['user'], $ini['password'], $ini['database']);
    if(!$mysql){
        print("Возникла ошибка во время выполнения. Попробуйте обновить страницу.");
        die();
    }
    if ($stmt = mysqli_prepare($mysql, "SELECT `username` from `users`")){
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $user);
        while (mysqli_stmt_fetch($stmt)) {
            $usersArr[] = $user;
        }
        mysqli_stmt_close($stmt);
    }
    else{
        print("Возникла ошибка во время выполнения. Попробуйте обновить страницу.");
        die();
    }
    mysqli_close($mysql);
    asort($usersArr);
    $usernameHTML = $_SESSION['username'];
    $menuHTML = "<td>Доступно места: ".$_SESSION['availableSpace']."</td>
    <td><input type=\"button\" value=\"Создать директорию\" /></td>
    <td><input type=\"button\" value=\"Загрузить файл\" /></td>";
    $windowHTML = newWindow($_SESSION['path']);

    $usersHTML = "";
    foreach($usersArr as $user){
        $usersHTML .= "<li class=\"close\">$user</li>";
    }

    $html = file_get_contents("temp/html.txt");
    foreach(array('usernameHTML', 'menuHTML', 'windowHTML', 'usersHTML') as $value){
        $html = preg_replace("/$value/uis", $$value, $html);
    }    
}

print($html);

function checkCoockie(){
    if (isset($_COOKIE["cloudStorage"])){
        $coockieArr = explode(":",$_COOKIE["cloudStorage"]);
        $email = $coockieArr[0];
        $ini = parse_ini_file("mysql.ini");
        $mysql = mysqli_connect($ini['host'], $ini['user'], $ini['password'], $ini['database']);
        if(!$mysql){
            return checkCoockie();
        }
        $secretKey = "";
        $result = mysqli_query($mysql, "SELECT * FROM `users` WHERE email='$email'");
        if($result){
            $userData = mysqli_fetch_array($result);
            $secretKey = $userData['secretkey'];
            mysqli_close($mysql);
        }
        else{
            return false;
        }
        $coockieKeyHash = $coockieArr[1];
        $key = $secretKey.$_SERVER["REMOTE_ADDR"];
        if (password_verify($key, $coockieKeyHash)){
            return true;
        } 
    }
    else{
        return false;
    }
    return false;
}