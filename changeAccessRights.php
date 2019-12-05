<?php

//Файл производит изменение режима доступа к файлу или директории, добавление или удаление пользователя из списка разделяемого доступа,
//а также возвращает действующее значение права доступа к файлу или директории

require_once "JsHttpRequest/JsHttpRequest.php";
require_once "additionalFunctions.php";
require_once "dataBaseClass.php";

session_start();

$js = new JsHttpRequest("utf-8");
global $_RESULT;

//Создание объекта базы данных и проверка на успешность этого создания
$mysql = new dataBase();
if(!$mysql->isConnect()){
    $_RESULT["error"] = true;
    exit();
}

//Сбор полученных данных
foreach (array('action', 'newMod', 'filename', 'isRoot', 'username') as $parameterName) {
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

//Проверка права доступа пользователя к файлу или директории (только владелец может изменять режим доступа к файлу или директории)
$accessRigths = checkAccessRights($mysql, $file, $_SESSION['username']);
if ($accessRigths !== 0){
    $_RESULT["error"] = true;
    exit();
}

//Переобозначение режима доступа, необходимое для базы данных, в случае, если пользователь запросил изменение режима доступа
if ($newMod && $newMod == "Частный") {
    $newMod = 0;
}
elseif ($newMod && $newMod == "Разделяемый") {
    $newMod = 1;
}
elseif ($newMod && $newMod == "Общий") {
    $newMod = 2;
}

//Если пользователя запросил изменить режим доступа
if ($action && $action == "changeAccessRights"){
    $mysql->updateAccessRights($file, $newMod, "-1");
}

//Если пользователя добавляет или удаляет нового пользователя в список разделяемого доступа
if ($action && $action == "addToSharedAccess"){
    $sharedaccess = $mysql->getFileAccessInfo($file)['sharedaccess'];
    $id = $mysql->getUserId($username);
    if (preg_match("/\/$id\//", $sharedaccess)){
        $shArr = explode("/$id/", $sharedaccess, 2);
        $sharedaccess = $shArr[0]. $shArr[1];
    }
    else{
        $sharedaccess .= "/$id/";
    }
    $mysql->updateAccessRights($file, -1, $sharedaccess);
    exit();
}

//Получение режима доступа для файла или директории, а также подготовка результирующих данных в формате, удобном для пользователя
$mod = $mysql->getAccessmod($file);
if ($mod == 0){
    $_RESULT["mod_active"] = "Частный(Доступ есть только у вас)";
    $_RESULT["mod_firstVar"] = "Разделяемый";
    $_RESULT["mod_secondVar"] = "Общий";
}
elseif($mod == 1){
    $_RESULT["mod_active"] = "Разделяемый(Доступ есть у выделенной группы пользователей)";
    $_RESULT["mod_firstVar"] = "Частный";
    $_RESULT["mod_secondVar"] = "Общий";
}
else{
    $_RESULT["mod_active"] = "Общий(Доступ есть у всех пользователей)";
    $_RESULT["mod_firstVar"] = "Частный";
    $_RESULT["mod_secondVar"] = "Разделяемый";
}