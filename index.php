<?php

//require_once("createNewWindow.php");

session_start();

foreach(array('action', 'username', 'email', 'password') as $parameterName){
    $$parameterName = isset($_POST[$parameterName])
    ? trim($_POST[$parameterName])
    : "";
}

if($action && $action == "Регистрация"){
    $html = file_get_contents("temp/reg.txt");
    $formHTML = "<div id=\"regForm\">
    <form action=\"\" method=\"post\">
        <p>Регистрация</p>
        <p id=\"error\"></p>
        <input class=\"reglog\" type=\"text\" name=\"username\" placeholder=\"Имя пользователя\" /><br />
        <input class=\"reglog\" type=\"email\" name=\"email\" placeholder=\"Адрес электронной почты\" /><br />
        <input class=\"reglog\" type=\"password\" name=\"password\" placeholder=\"Пароль\" /><br />
        <input type=\"submit\" name=\"action\" value=\"Зарегистрироваться\" />
    </form>
</div>";
    $html = preg_replace("/formHTML/uis", $formHTML, $html);
    print $html;
    die();
}
if($action && $action == "Войти"){
    $html = file_get_contents("temp/reg.txt");
    $formHTML = "<div id=\"loginForm\">
    <form action=\"\" method=\"post\">
        <p>Авторизация</p>
        <p id=\"error\"></p>
        <input class=\"reglog\" type=\"email\" name=\"email\" placeholder=\"Адрес электронной почты\" /><br />
        <input class=\"reglog\" type=\"password\" name=\"password\" placeholder=\"Пароль\" /><br />
        <input type=\"submit\" name=\"action\" value=\"Авторизоваться\" />
    </form>
</div>";
    $html = preg_replace("/formHTML/uis", $formHTML, $html);
    print $html;
    die();
}


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
$loginHTML = "";
$menuHTML = "";
$windowHTML = "";
$usersHTML = "";

$html = file_get_contents("temp/html.txt");
if (isset($_SESSION['username'])){
    die();
}
else{
    $loginHTML = "<form action=\"\" method=\"post\"><input type=\"submit\" name=\"action\" value=\"Регистрация\" /> | <input type=\"submit\" name=\"action\" value=\"Войти\" /></form>";
    foreach($usersArr as $user){
        $usersHTML .= "<li class=\"close\">$user</li>";
    }
}

foreach(array('loginHTML', 'menuHTML', 'windowHTML', 'usersHTML') as $value){
    $html = preg_replace("/$value/uis", $$value, $html);
}

print($html);