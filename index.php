<?php

//Файл содержит генерацию всей страницы браузера

require_once "additionalFunctions.php";
require_once "dataBaseClass.php";

session_start();

//Создание объекта базы данных и проверка на успешность этого создания
$mysql = new dataBase();
if(!$mysql->isConnect()){
    die("Возникла ошибка во время выполнения. Попробуйте обновить страницу.");
}

//Сбор полученных данных
foreach(array('action', 'username', 'email', 'password') as $parameterName){
    $$parameterName = isset($_POST[$parameterName])
    ? trim($_POST[$parameterName])
    : "";
}

//Если пользователь хочет разлогиниться
if ($action && $action == "Выйти"){
    session_destroy();
    $_SESSION = array();
    setcookie("cloudStorage", "", time()-3600);
    header("Location: ".$_SERVER["PHP_SELF"]);
    die();
}

//Если пользователь хочет зарегистрироваться
if ($action && $action == "Зарегистрироваться" && $username && $email && $password){
    
    //Проверка имени пользователя на корректность
    if (preg_match("/^[a-z0-9]{3,25}$/uis", $username)){

        //Проверка на наличие уже зарегистрированных пользователей с указанными адресом почты или имени пользователя
        $usersArr = $mysql->getUsers();
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

        //Успешная регистрация
        $_SESSION['username'] = $username;
        $_SESSION['path'] = "localStorage/".$username;
        $_SESSION['availablespace'] = 104857600;
        mkdir($_SESSION["path"]);
        $secretKey = uniqid();
        $password .= $secretKey;
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $mysql->addUser($username, $email, $passwordHash, $secretKey);
        $mysql->addToAccessrights("localStorage/".$username, $username);
        $forcoockie = $secretKey.$_SERVER['REMOTE_ADDR'];
        setcookie("cloudStorage", $email.':'.password_hash($forcoockie, PASSWORD_DEFAULT), time()+60*60*24);
        header("Location: ".$_SERVER["PHP_SELF"]);
        die();
    }
}

//Если пользователь хочет авторизоваться
if ($action && $action == "Войти" && $email && $password){
    $user = $mysql->getParticularUser("email", $email);
    $passwordCheck = $password.$user['secretkey'];

    //Проверка правильности введенного пароля
    if (password_verify($passwordCheck, $user["password"])){
        $secretKey = uniqid();
        $newPassword = $password.$secretKey;
        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $mysql->updateRegistrationInfo($newPasswordHash, $secretKey, $email);
        $_SESSION["username"] = $user["username"];
        $_SESSION["path"] = "localStorage/".$_SESSION["username"];
        $_SESSION["availablespace"] = $user["availablespace"];
        $forcoockie = $secretKey.$_SERVER['REMOTE_ADDR'];
        setcookie("cloudStorage", $email.':'.password_hash($forcoockie, PASSWORD_DEFAULT), time()+60*60*24);
        header ("Location: ".$_SERVER["PHP_SELF"]);
        die();
    }
    else{
        $_SESSION['error'] = "Неверный логин или пароль!";
        header("Location: ".$_SERVER["PHP_SELF"]);
        die();
    }
}

//Проверка наличия куки и его корректности
if(!checkCoockie($mysql)){
    
    //Форма регистрации, если куки не прошло проверку
    $html = file_get_contents("html_patterns/reg.html");
    $formHTML = "<div id=\"regForm\">
    <form action=\"\" method=\"post\">
        <table>
            <tbody>
                <tr>
                    <td colspan=\"2\">
        <p id=\"error\">";
    $formHTML .= isset($_SESSION['error'])
        ? $_SESSION['error']
        : "";
    $formHTML .= "</p>
                        <input class=\"reglog\" required=\"required\" type=\"text\" name=\"username\" placeholder=\"Имя пользователя\" /><br />
                        <input class=\"reglog\" required=\"required\" type=\"email\" name=\"email\" placeholder=\"Адрес электронной почты\" /><br />
                        <input class=\"reglog\" required=\"required\" type=\"password\" name=\"password\" placeholder=\"Пароль\" /><br />
                        <input id=\"regAuth\" type=\"submit\" value=\"Зарегистрироваться\" name=\"action\" />
                    </td>
                </tr>
                <tr>
                    <td id=\"reg\" onclick=\"setReg()\">
                        <p>Регистрация</p>
                    </td>
                    <td id=\"auth\" onclick=\"setAuth()\">
                        <p>Авторизация</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</div>";
    $html = preg_replace("/formHTML/uis", $formHTML, $html);
}
else{

    //Функционал сайта, если куки прошло проверку
    $usersArr = array();
    $usersArr = $mysql->getUsers();
    $usernameHTML = $_SESSION['username'];
    $accessRootMod = $mysql->getAccessmod("localStorage/$usernameHTML");
    switch ($accessRootMod) {
        case 0:
            $accessRootMod = "Частный(Доступ есть только у вас)";
            break;
        case 1:
            $accessRootMod = "Разделяемый(Доступ есть у выделенной группы пользователей)";
            break;
        case 2:
            $accessRootMod = "Общий(Доступ есть у всех пользователей)";
            break;
    }
    $menuHTML = "<td>
    Доступно места: <span id=\"availablespace\">".$_SESSION['availablespace']."</span> байт<br />
    <input type=\"button\" value=\"Назад\" onclick=\"goBack()\" /><br />
    Путь: <span id=\"path\">".explode("/", $_SESSION['path'], 2)[1]."</span>
    </td>
    <td>
        Уровень доступа к вашей<br />
        корневой папке: <form><span id=\"accessRootMod\">$accessRootMod</span>
        <input type=\"button\" value=\"Изменить\" onclick=\"show('$usernameHTML', true)\"></form>
    </td>
    <td>
        <input type=\"text\" placeholder=\"Введите имя папки\" id=\"dirName\" /><input type=\"button\" value=\"Создать директорию\" onclick=\"createDirectory()\" />
    </td>
    <td>
        <form method=\"post\" enctype=\"multipart/form-data\">
        <input type=\"file\" name=\"file\" /><input type=\"button\" value=\"Загрузить файл\" onclick=\"uploadFile(this.form.file)\" />
        </form>
    </td>";
    $windowHTML = newWindow($mysql, $_SESSION['path'], $_SESSION['username']);
    $usersHTML = "";
    $i = 0;

    //Первый 15 пользователей из базы данных
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
    $html = file_get_contents("html_patterns/main.html");
    foreach(array('usernameHTML', 'menuHTML', 'windowHTML', 'usersHTML') as $value){
        $html = preg_replace("/$value/uis", $$value, $html);
    }
}

print($html);
