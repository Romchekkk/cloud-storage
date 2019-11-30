<?php

require_once "JsHttpRequest/JsHttpRequest.php";
require_once "additionalFunctions.php";
require_once "dataBaseClass.php";

session_start();

$js = new JsHttpRequest("utf-8");

global $_RESULT;
$mysql = new dataBase();
if(!$mysql->isConnect()){
    $_RESULT["error"] = true;
    exit();
}

foreach (array('action', 'newMod', 'filename', 'isRoot', 'sharedaccess') as $parameterName) {
    $$parameterName = isset($_REQUEST[$parameterName])
        ? trim($_REQUEST[$parameterName])
        : "";
}

$file = $isRoot
    ? "localStorage/".$_SESSION['username']
    : $_SESSION['path']."/$filename";
if (!file_exists($file)){
    $_RESULT["error"] = true;
    exit();
}
$accessRigths = checkAccessRights($mysql, $file, $_SESSION['username']);
if ($accessRigths !== 0){
    $_RESULT["error"] = true;
    exit();
}

if ($newMod && $newMod == "Частный") {
    $newMod = 0;
}
elseif ($newMod && $newMod == "Разделяемый") {
    $newMod = 1;
}
elseif ($newMod && $newMod == "Общий") {
    $newMod = 2;
}
if ($action && $action == "changeAccessRights"){
    $mysql->updateAccessRights($file, $newMod, $sharedaccess);
}

$mod = $mysql->getAccessRights($file);
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