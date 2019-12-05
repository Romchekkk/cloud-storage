<?php

//Файл выполняет поиск пользователей и генерацию списка для добавления их в список разделяемого доступа

require_once "JsHttpRequest/JsHttpRequest.php";
require_once "dataBaseClass.php";

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
foreach (array('forSearch', 'filename', 'isRoot') as $parameterName) {
    $$parameterName = isset($_REQUEST[$parameterName])
        ? trim($_REQUEST[$parameterName])
        : "";
}

//Создание пути к необходимому файлу или необходимой директории
$file = $isRoot
    ? "localStorage/".$_SESSION['username']
    : $_SESSION['path']."/$filename";

//Проверка наличия этого файла или этой директории
if (!file_exists($file)){
    $_RESULT["error"] = true;
    exit();
}

//Генерация списка пользователей
$isRoot = $isRoot ? "true" : "false";
$usersList = "<table><tbody>";
$usersArr = $mysql->getUsersForSearch($forSearch);
$sharedaccess = $mysql->getFileAccessInfo($file)['sharedaccess'];
foreach($usersArr as $user){
    $id = $mysql->getUserId($user['username']);
    if (preg_match("/\/$id\//", $sharedaccess)){
        $usersList .= "<tr><td><input type=\"checkbox\" checked=\"checked\" value=\"".$user['username']."\" onclick=\"check(this.value, $isRoot)\" /></td><td><span>".$user['username']."</span></td></tr>\n";
    }
    else{
        $usersList .= "<tr><td><input type=\"checkbox\" value=\"".$user['username']."\" onclick=\"check(this.value, $isRoot)\" /></td><td><span>".$user['username']."</span></td></tr>\n";
    }
}

//Подготовка результирующих данных
$_RESULT["error"] = false;
$_RESULT['usersList'] = $usersList."</tbody></table>";