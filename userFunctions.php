<?php

require_once "JsHttpRequest/JsHttpRequest.php";
require_once "checkAccessRights.php";
require_once "createNewWindow.php";
require_once "dataBaseFunctions.php";

session_start();

$js = new JsHttpRequest("utf-8");

foreach (array('action', 'newMod', 'dirName', 'fileName') as $parameterName) {
    $$parameterName = isset($_REQUEST[$parameterName])
        ? trim($_REQUEST[$parameterName])
        : "";
}

$path = $_SESSION['path'];

global $_RESULT;
switch ($action) {
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
        $action($path);
        break;
    case "downloadFile":
        $action($path, $fileName);
        $_RESULT["href"] = $path . '/' . $fileName;
        break;
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
$topWindow = $_SESSION['availableSpace'];

$_RESULT["window"] = newWindow($path);
$_RESULT["space"] = $topWindow;

function createDirectory($path, $dirName)
{
    // if (!checkAccessRights($path, $_SESSION['user'])){
    //     return false;
    // }
    $dir = $path . '/' . $dirName;
    if(!is_dir($dir)) {
        mkdir($dir);
    }
    return true;
}

function deleteDirectory($path, $dirName)
{
    // if (!checkAccessRights($path, $_SESSION['user'])){
    //     return false;
    // }
    $ini = parse_ini_file("database/mysql.ini");
    $mysql = mysqli_connect($ini['host'], $ini['user'], $ini['password'], $ini['database']);
    if (!$mysql) {
        return false;
    }
    removeDir($path . "/" . $dirName, $mysql);
    mysqli_close($mysql);
    return true;
}

function uploadFile($path)
{
    // if (!checkAccessRights($path, $_SESSION['user'])){
    //     return false;
    // }
    $size = $_FILES['file']['size'];
    if ($size > $_SESSION['availableSpace']){
        return false;
    }
    $filePath = $path."/".$_FILES['file']['name'];
    $fileName = preg_split("/\./uis", $_FILES['file']['name'])[0];
    if (file_exists($filePath)){
        $number = 1;
        foreach(glob("$path/$fileName*") as $file){
            preg_match("@"."$path/$fileName"."\((?<number>[0-9]*)\)@uis", $file, $arr);
            if ($arr["number"] > $number){
                $number = $arr["number"];
            }
            elseif ($arr["number"] == $number){
                $number++;
            }
        }
        $filePath = preg_replace("@"."$path/$fileName"."@uis", "$path/$fileName($number)", $filePath);
    }

    if (move_uploaded_file($_FILES['file']['tmp_name'], $filePath)) {
        $ini = parse_ini_file("database/mysql.ini");
        $mysql = mysqli_connect($ini['host'], $ini['user'], $ini['password'], $ini['database']);
        if (!$mysql) {
            return false;
        }
        newAvailableSpace($size, "+", $_SESSION['username'], $mysql);
        mysqli_close($mysql);
    }

    return true;
}


function downloadFile($path, $fileName)
{
    // if (!checkAccessRights($path, $_SESSION['user'])){
    //     return false;
    // }
    return true;
}

function deleteFile($path, $fileName)
{
    // if (!checkAccessRights($path, $_SESSION['user'])){
    //     return false;
    // }
    $file = $path . "/" . $fileName;
    $size = filesize($file);
    if (unlink($file)) {
        $ini = parse_ini_file("database/mysql.ini");
        $mysql = mysqli_connect($ini['host'], $ini['user'], $ini['password'], $ini['database']);
        if (!$mysql) {
            return false;
        }
        newAvailableSpace($size, "-", $_SESSION['username'], $mysql);
        mysqli_close($mysql);
    }
    return true;
}


function changeMod($path, $newMod)
{
    // if (!checkAccessRights($path, $_SESSION['user'])){
    //     return false;
    // }
    // Изменение прав доступа
    return true;
}

function changeDirectory($dirName)
{
    // if (!checkAccessRights($path, $_SESSION['user'])){
    //     return false;
    // }
    $_SESSION['path'] .= '/' . $dirName;
    return true;
}

function goBack($path)
{
    if (preg_match_all("/\//uis", $path) == 1) {
        return false;
    }
    preg_match("/(?<newPath>.*)\/.*?$/uis", $path, $arr);
    $_SESSION['path'] = $arr['newPath'];
    return true;
}

function removeDir($path, $mysql)
{
    foreach (glob($path . '/*') as $file) {
        if (is_dir($file)) {
            removeDir($file, $mysql);
            rmdir($file);
        } 
        else {
            $size = filesize($file);
            if (unlink($file)) {
                newAvailableSpace($size, "-", $_SESSION['username'], $mysql);
            }
        }
    }
    rmdir($path);
    return true;
}

function newAvailableSpace($size, $sign, $username, $mysql){
    $user = getConcreteUser($mysql, "username", $username);
    if (count($user) != 0) {
        $availablespace = 0;
        if ($sign == "+"){
            $availablespace = $user['availablespace']+$size;
        }
        else{
            $availablespace = $user['availablespace']-$size;
        }
        $update = mysqli_query($mysql, "UPDATE `users` SET `availablespace`='$availablespace' WHERE `username`='$username'");
        if ($update) {
            $_SESSION['availableSpace'] = $availablespace;
        }
    }
    return true;
}