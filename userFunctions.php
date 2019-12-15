<?php

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
foreach (array('action', 'newMod', 'dirName', 'fileName', 'user') as $parameterName) {
    $$parameterName = isset($_REQUEST[$parameterName])
        ? trim($_REQUEST[$parameterName])
        : "";
}

//Обработка полученных данных
$path = $_SESSION['path'];
switch ($action) {
    case "changeMod":
        if ($action($mysql, $path, $fileName, $newMod, $isRoot, $usersArr)){
            $_RESULT['newMod'] = $newMod;
        }
        break;
    case "createDirectory":
    case "deleteDirectory":
        $action($mysql, $path, $dirName);
        break;
    case "deleteFile":
        $action($mysql, $path, $fileName);
        break;
    case "uploadFile":
        $action($mysql, $path);
        break;
    case "downloadFile":
        $action($mysql, $path, $fileName);
        $_RESULT["href"] = $path . '/' . $fileName;
        break;
    case "goBack":
        $action($path);
        $path = $_SESSION['path'];
        $_RESULT['path'] = explode("/", $path, 2)[1];
        break;
    case "changeDirectory":
        if ($action($mysql, $path, $dirName)){
            $path = $_SESSION['path'];
            $_RESULT['path'] = explode("/", $path, 2)[1];
        }
        else{
            $_RESULT['path'] = explode("/", $_SESSION['path'], 2)[1];
        }
        break;
    case "openUser":
        if ($action($mysql, $user)){
            $path = $_SESSION['path'];
            $_RESULT['path'] = explode("/", $path, 2)[1];
        }
        else{
            $_RESULT['path'] = explode("/", $_SESSION['path'], 2)[1];
        }
        break;
    default:
        exit();
}

//Подготовка результирующих данных
$_RESULT["window"] = newWindow($mysql, $path, $_SESSION['username']);
$_RESULT["space"] = $_SESSION['availablespace'];

/**
 * Создание директории.
 *
 * @param [dataBase] $mysql - объект базы данных.
 * @param [string] $path - путь к директории, в которой будет создана новая директория.
 * @param [string] $dirName - имя новой директории.
 * @return bool - true, если директория успешно создана;
 * - false, в противном случае.
 */
function createDirectory(&$mysql, $path, $dirName){
    $accessRigths = checkAccessRights($mysql, $path, $_SESSION['username']);
    if ($accessRigths !== 0){
        return false;
    }
    $dir = $path . '/' . $dirName;
    if(!file_exists($dir)) {
        if (mkdir($dir)) {
            $mysql->addToAccessrights($dir, $_SESSION['username']);
            return true;
        }
        else{
            return false;
        }
    }
    return false;
}

/**
 * Удаление директории.
 *
 * @param [dataBase] $mysql - объект базы данных.
 * @param [string] $path - путь к директории, в которой будет удалена указанная директория.
 * @param [string] $dirName - имя удаляемой директории.
 * @return bool - true, если директория удалена;
 * false, в противном случае.
 */
function deleteDirectory(&$mysql, $path, $dirName){
    $accessRigths = checkAccessRights($mysql, $path, $_SESSION['username']);
    if ($accessRigths !== 0){
        return false;
    }
    return removeDir("$path/$dirName", $mysql);
}

/**
 * Загрузка файла.
 *
 * @param [dataBase] $mysql - объект базы данных.
 * @param [string] $path - путь к директории, куда будет загружен файл.
 * @return bool - true, если файл успешно загружен;
 * - false, в противном случае.
 */
function uploadFile(&$mysql, $path){
    $accessRigths = checkAccessRights($mysql, $path, $_SESSION['username']);
    if ($accessRigths !== 0){
        return false;
    }
    $size = $_FILES['file']['size'];
    if ($size > $_SESSION['availablespace']){
        return false;
    }
    $filePath = $path."/".$_FILES['file']['name'];
    if (file_exists($filePath)) {
        return false;
    }
    elseif (move_uploaded_file($_FILES['file']['tmp_name'], $filePath)) {
        $mysql->addToAccessrights($filePath, $_SESSION['username']);
        newAvailableSpace($size, "-", $_SESSION['username'], $mysql);
        return true;
    }
    return false;
}

/**
 * Досту на скачивание файла.
 *
 * @param [dataBase] $mysql - объект базы данных.
 * @param [string] $path - путь к директории, в которой находится скачиваемый файл.
 * @param [string] $fileName - имя скачиваемого файла.
 * @return bool - true, если доступ на скачивание есть;
 * - false, в противном случае.
 */
function downloadFile(&$mysql, $path, $fileName){
    $file = "$path/$fileName";
    if (!file_exists($file)){
        return false;
    }
    $accessRigths = checkAccessRights($mysql, $file, $_SESSION['username']);
    if ($accessRigths === -1){
        return false;
    }
    return true;
}

/**
 * Удаление файла.
 *
 * @param [dataBase] $mysql - объект базы данных.
 * @param [string] $path - путь к директории, в которой будет удален файл.
 * @param [string] $fileName - имя удаляемого файла.
 * @return bool - true, если файл удален;
 * - false, в противном случае.
 */
function deleteFile(&$mysql, $path, $fileName){
    $file = "$path/$fileName";
    if (!file_exists($file)){
        return false;
    }
    $accessRigths = checkAccessRights($mysql, $path, $_SESSION['username']);
    if ($accessRigths !== 0){
        return false;
    }
    $size = filesize($file);
    if (unlink($file)){
        $mysql->removeFromAccessrights($file);
        newAvailableSpace($size, "+", $_SESSION['username'], $mysql);
        return true;
    }
    return false;
}

/**
 * Переход в директорию.
 *
 * @param [dataBase] $mysql - объект базы данных.
 * @param [string] $path - путь к директории, в которой находится директория, в которую собираются перейти.
 * @param [string] $dirName - имя директории, в которую собираются перейти.
 * @return bool - true, если переход успешно совершен;
 * - false, в противном случаеы.
 */
function changeDirectory(&$mysql, $path, $dirName){
    $dir = "$path/$dirName";
    if (!is_dir($dir)){
        return false;
    }
    $accessRigths = checkAccessRights($mysql, $dir, $_SESSION['username']);
    if ($accessRigths === -1){
        return false;
    }
    $_SESSION['path'] = $dir;
    return true;
}

/**
 * Возврат к предыдущей директории.
 *
 * @param [string] $path - путь к директории, из которой собираются вернуться.
 * @return bool - true, если переход успешно совершен;
 * - false, в противном случае.
 */
function goBack($path){
    if (preg_match_all("/\//uis", $path) == 1) {
        return false;
    }
    preg_match("/(?<newPath>.*)\/.*?$/uis", $path, $arr);
    $_SESSION['path'] = $arr['newPath'];
    return true;
}

/**
 * Открытие корневой директории пользователя.
 *
 * @param [dataBase] $mysql - объект базы данных.
 * @param [string] $user - имя пользователя, в директорию которого собираются перейти.
 * @return bool - true, если переход успешно совершен;
 * - false, в противном случае.
 */
function openUser(&$mysql, $user){
    $dir = "localStorage/".$user;
    if (!is_dir($dir)){
        return false;
    }
    $accessRigths = checkAccessRights($mysql, $dir, $_SESSION['username']);
    if ($accessRigths === -1){
        return false;
    }
    $_SESSION['path'] = $dir;
    return true;
}