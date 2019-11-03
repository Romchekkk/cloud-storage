<?php

require_once "JsHttpRequest/JsHttpRequest.php";
require_once "checkAccessRights.php";
require_once "createNewWindow.php";

session_start();

$js = new JsHttpRequest("utf-8");

foreach(array('action', 'newMod', 'dirName', 'fileName', 'username') as $parameterName){
    $$parameterName = isset($_REQUEST[$parameterName])
    ? trim($_REQUEST[$parameterName])
    : "";
}

$path = $_SESSION['path'];
$topWindow;

switch($action){
    case "changeMod":
        $action($path, $newMod);
        break;
    case "createDirectory":
    case "deleteDirectory":
        $action($path, $dirName);
        break;
    case "deleteFile":
        $topWindow = $action($path, $fileName);
        break;
    case "uploadFile": 
        $topWindow = $action($path, $username);
        break; 
    case "downloadFile":
        $action($path, $fileName);
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

global $_RESULT;
$_RESULT = array(
    "window" => newWindow($path),
    "space" => $topWindow
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

function uploadFile($path, $username){
    // if (!checkAccessRights($path, $_SESSION['user'])){
    //     return false;
    $size = filesize($_FILES['file']['tmp_name']);
    if (move_uploaded_file($_FILES['file']['tmp_name'], $path."/".$_FILES['file']['name'])) {

        $ini = parse_ini_file("database/mysql.ini");
        $mysql = mysqli_connect($ini['host'], $ini['user'], $ini['password'], $ini['database']);
        if(!$mysql){
            print("Возникла ошибка во время выполнения. Попробуйте обновить страницу.");
            header("Location: ".$_SERVER["PHP_SELF"]);
            return false;
        }

        $result = mysqli_query($mysql, "SELECT * FROM `users` WHERE username='$username'");
         if($result){
            $userData = mysqli_fetch_array($result);
            $availablespace = $userData['availablespace'] - $size;
             $update = mysqli_query($mysql, "UPDATE `users` SET `availablespace`='$availablespace' WHERE `username`='$username'");
             mysqli_close($mysql);
            if ($update) {
                $_SESSION['availableSpace'] = $availablespace." Байт";
            }
        }
    }
    $menuHTML = $availablespace." Байт";
    
    return $menuHTML;
}


function downloadFile($path,$fileName){
    // if (!checkAccessRights($path, $_SESSION['user'])){
    //     return false;
    // }
        return true;
}

function deleteFile($path, $fileName){
    // if (!checkAccessRights($path, $_SESSION['user'])){
    //     return false;
    // }
    $file = $path."/".$fileName;
    $username = explode('/',$path)[1];
    $size = filesize($file);

    if (unlink($file)) {

        $ini = parse_ini_file("database/mysql.ini");
        $mysql = mysqli_connect($ini['host'], $ini['user'], $ini['password'], $ini['database']);
        if(!$mysql){
            print("Возникла ошибка во время выполнения. Попробуйте обновить страницу.");
            header("Location: ".$_SERVER["PHP_SELF"]);
            return false;
        }

        $result = mysqli_query($mysql, "SELECT * FROM `users` WHERE username='$username'");
         if($result){
            $userData = mysqli_fetch_array($result);
            $availablespace = $userData['availablespace'] + $size;
             $update = mysqli_query($mysql, "UPDATE `users` SET `availablespace`='$availablespace' WHERE `username`='$username'");
             //mysqli_close($mysql);
            if ($update) {
                $_SESSION['availableSpace'] = $availablespace." Байт";
            }
        }
        mysqli_close($mysql);
    }
    $menuHTML = $availablespace." Байт";
    return $menuHTML;
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
