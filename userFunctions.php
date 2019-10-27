<?php

require_once "../JsHttpRequest/JsHttpRequest.php";
require_once "checkAccessRights.php";

session_start();

$js = new JsHttpRequest("utf-8");

foreach(array('action', 'newMod', 'dirName') as $parameterName){
    $$parameterName = isset($_REQUEST[$parameterName])
    ? trim($_REQUEST[$parameterName])
    : "";
}

$path = $_SESSION['path'];

switch($action){
    case "changeMod":
        if ($action($path, $newMod)){
            exit();
        }
        break;
    case "createDirectory":
        if ($action($dirName, $path)){
            exit();
        }
        break;
    case "deleteDirectory":
    case "deleteFile":
    case "downloadFile":
    case "uploadFile":
        if ($action($path)){
            exit();
        }
        break;
    default:
        exit();
}

global $_RESULT;
$_RESULT = array(
    "window" => newWindow($path)
);

/**
 * Create directory on storage
 *
 * @param [string] $dirName
 * @param [string] $path
 * @return bool
 */
function createDirectory($dirName, $path){
    if (!checkAccessRights($path, $_SESSION['user'])){
        return false;
    }
    // Создание директории
    return true;
}

/**
 * Delete directory on storage
 *
 * @param [string] $path
 * @return bool
 */
function deleteDirectory($path){
    if (!checkAccessRights($path, $_SESSION['user'])){
        return false;
    }
    // Удаление директории
    return true;
}

/**
 * Upload file on storage
 *
 * @param [string] $path
 * @return bool
 */
function uploadFile($path){
    if (!checkAccessRights($path, $_SESSION['user'])){
        return false;
    }
    // Загрузка файла
    return true;
}

/**
 * Download file from storage
 *
 * @param [string] $path
 * @return bool
 */
function downloadFile($path){
    if (!checkAccessRights($path, $_SESSION['user'])){
        return false;
    }
    // Скачивание файла
    return true;
}

/**
 * Delete file from storage
 *
 * @param [string] $path
 * @return bool
 */
function deleteFile($path){
    if (!checkAccessRights($path, $_SESSION['user'])){
        return false;
    }
    // Удаление файла
    return true;
}

/**
 * Change access rights on file or directory on storage
 *
 * @param [string] $path
 * @param [string] $newMod
 * @return void
 */
function changeMod($path, $newMod){
    if (!checkAccessRights($path, $_SESSION['user'])){
        return false;
    }
    // Изменение прав доступа
    return true;
}