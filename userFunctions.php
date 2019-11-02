<?php

require_once "JsHttpRequest/JsHttpRequest.php";
require_once "checkAccessRights.php";
require_once "createNewWindow.php";
session_start();

$js = new JsHttpRequest("utf-8");

foreach(array('action', 'newMod', 'dirName', 'fileName') as $parameterName){
    $$parameterName = isset($_REQUEST[$parameterName])
    ? trim($_REQUEST[$parameterName])
    : "";
}

$path = $_SESSION['path'];

switch($action){
    case "changeMod":
        $action($path, $newMod);
        break;
    case "createDirectory":
    case "deleteDirectory":
        $action($path, $dirName);
        break;
    case "deleteFile":
        $action($path, $fileName);
        break;
    case "uploadFile":  
    case "downloadFile":
    case "goBack":
        $action($path);
        break;
    case "changeDirectory":
        $action($dirName);
    break;
    default:
        exit();
}

$path = $_SESSION['path'];

global $_RESULT;
$_RESULT = array(
    "window" => newWindow($path)
);

function createDirectory($path, $dirName){
    // if (!checkAccessRights($path, $_SESSION['user'])){
    //     return false;
    // }
    mkdir($path.'/'.$dirName);
    return true;
}

function deleteDirectory($path,$dirName){
    // if (!checkAccessRights($path, $_SESSION['user'])){
    //     return false;
    // }
    rmdir($path.'/'.$dirName);
    return true;
}

function uploadFile($path){
    // if (!checkAccessRights($path, $_SESSION['user'])){
    //     return false;
    // }
    if (move_uploaded_file($_FILES['file']['tmp_name'], $path."/".$_FILES['file']['name'])) {
        return true;
    }
    else{
        return false;
    }
}

function downloadFile($path){
    // if (!checkAccessRights($path, $_SESSION['user'])){
    //     return false;
    // }
    // Скачивание файла
    return true;
}

function deleteFile($path, $fileName){
    // if (!checkAccessRights($path, $_SESSION['user'])){
    //     return false;
    // }
    unlink($path."/".$fileName);
    return true;
}

function changeMod($path, $newMod){
    // if (!checkAccessRights($path, $_SESSION['user'])){
    //     return false;
    // }
    // Изменение прав доступа
    return true;
}

function changeDirectory($dirName){
    // if (!checkAccessRights($path, $_SESSION['user'])){
    //     return false;
    // }
    $_SESSION['path'] .= '/'.$dirName;
    return true;
}

function goBack($path){
    if (preg_match_all("/\//uis", $path) == 1){
        return false;
    }
    preg_match("/(?<newPath>.*)\/.*?$/uis", $path, $arr);
    $_SESSION['path'] = $arr['newPath'];
    return true;
}