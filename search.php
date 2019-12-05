<?php

//Файл выполняет поиск пользователей и генерацию их спика для перехода на их директории

require_once "JsHttpRequest/JsHttpRequest.php";
require_once "dataBaseClass.php";
require_once "additionalFunctions.php";

session_start();

$js = new JsHttpRequest("utf-8");
global $_RESULT;

//Создание объекта базы данных и проверка на успешность этого создания
$mysql = new dataBase();
if(!$mysql->isConnect()){
    $_RESULT["error"] = true;
    die();
}

//Сбор полученных данных
$forSearch = isset($_REQUEST['forSearch'])
    ? trim($_REQUEST['forSearch'])
    : "";
    
//Получение списка пользователей
$usersArr = $mysql->getUsersForSearch($forSearch);

//Генерация списка полученных пользователей
$usersList = "";
foreach($usersArr as $user){
    $accessRights = checkAccessRights($mysql, "localStorage/".$user['username'], $_SESSION['username']);
    if($accessRights === 0 || $accessRights === 1 || $accessRights === 2){
        $usersList .= "<li class=\"open\"><input type=\"button\" value=\"".$user['username']."\" onclick=\"openUser(this.value)\" /></li>";
    }
    else{
        $usersList .= "<li class=\"close\">".$user['username']."</li>";
    }
}

//Подготовка результирующих данных
$_RESULT["error"] = false;
$_RESULT['usersList'] = $usersList;