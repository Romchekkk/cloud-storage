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
    die();
}

foreach (array('action', 'newMod', 'dirName', 'fileName', 'user', 'isRoot') as $parameterName) {
    $$parameterName = isset($_REQUEST[$parameterName])
        ? trim($_REQUEST[$parameterName])
        : "";
}

if($newMod !== ""){
    $newMod += 1;
    $newMod %= 3;
}

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

if (!$user){
    $user = $_SESSION['username'];
}
$_RESULT["window"] = newWindow($mysql, $path, $user);
$_RESULT["space"] = $_SESSION['availablespace'];

function createDirectory($mysql, $path, $dirName){
    $accessRigths = checkAccessRights($mysql, $path, $_SESSION['username']);
    if ($accessRigths !== 0){
        return false;
    }
    $dir = $path . '/' . $dirName;
    if(!file_exists($dir)) {
        if (mkdir($dir)) {
            $mysql->addToAccessrights($dir);
            return true;
        }
        else{
            return false;
        }
    }
    return false;
}

function deleteDirectory($mysql, $path, $dirName){
    $accessRigths = checkAccessRights($mysql, $path, $_SESSION['username']);
    if ($accessRigths !== 0){
        return false;
    }
    return removeDir("$path/$dirName", $mysql);
}

function uploadFile($mysql, $path){
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
        $mysql->addToAccessrights($filePath);
        newAvailableSpace($size, "+", $_SESSION['username'], $mysql);
        return true;
    }
    return false;
}

function downloadFile($mysql, $path, $fileName){
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

function deleteFile($mysql, $path, $fileName){
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
        newAvailableSpace($size, "-", $_SESSION['username'], $mysql);
        return true;
    }
    return false;
}

function changeMod($mysql, $path, $fileName, $newMod, $isRoot, $usersArr = array()) {
    $file = $isRoot
        ? "localStorage/".$_SESSION['username']
        : "$path/$fileName";
    if (!file_exists($file)){
        return false;
    }
    $accessRigths = checkAccessRights($mysql, $file, $_SESSION['username']);
    if ($accessRigths !== 0){
        return false;
    }
    $mysql->updateAccessRights($file, $newMod);
    return true;
    // $file = $path.'/'.$fileName;
    // if ($mysql) {
    //     if ($newMod == 1){
    //         $sharedaccess = "";
    //         foreach ($usersArr as $username) {
    //             $userdata = getConcreteUser($mysql, "username", $username);
    //             if ($userdata) {
    //                 $sharedaccess .= $userdata['id']."/";
    //             } 
    //             else {
    //                 return false;
    //             }
    //         }
    //         if (!mysqli_query($mysql, "UPDATE `accessrights` SET `sharedaccess`='$sharedaccess', `accessmod`=$newMod WHERE path='$file'")){
    //             return false;
    //         }
    //         else{
    //             return true;
    //         }
    //     }
    //     elseif($newMod == 0 || $newMod == 2){
    //         if (!mysqli_query($mysql, "UPDATE `accessrights` SET `sharedaccess`='', `accessmod`=$newMod WHERE path='$file'")){
    //             return false;
    //         }
    //         else{
    //             return true;
    //         }
    //     }
    // }
    // else {
    //     return false;
    // }
}

function changeDirectory($mysql, $path, $dirName){
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

function goBack($path){
    if (preg_match_all("/\//uis", $path) == 1) {
        return false;
    }
    preg_match("/(?<newPath>.*)\/.*?$/uis", $path, $arr);
    $_SESSION['path'] = $arr['newPath'];
    return true;
}

function openUser($mysql, $user){
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