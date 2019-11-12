<?php

require_once "JsHttpRequest/JsHttpRequest.php";
require_once "additionalFunctions.php";
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
        $action($path, $fileName, $newMod, $usersArr);
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
        $path = $_SESSION['path'];
        $_RESULT['path'] = explode("/", $path, 2)[1];
        break;
    case "changeDirectory":
        $action($dirName);
        $path = $_SESSION['path'];
        $_RESULT['path'] = explode("/", $path, 2)[1];
        break;
    default:
        exit();
}

$_RESULT["window"] = newWindow($path);
$_RESULT["space"] = $_SESSION['availablespace'];

function createDirectory($path, $dirName){
    // if (!checkAccessRights($path, $_SESSION['user'])){
    //     return false;
    // }
    $dir = $path . '/' . $dirName;
    if(!is_dir($dir)) {
        $ini = parse_ini_file("database/mysql.ini");
        $mysql = mysqli_connect($ini['host'], $ini['user'], $ini['password'], $ini['database']);
        if (mkdir($dir)) {
            if (addToAccessrights($mysql, $dir)) {
                mysqli_close($mysql);
                return true;
            }
            else{
                mysqli_close($mysql);
                rmdir($dir);
                return false;
            }
        }
        else{
            mysqli_close($mysql);
            return false;
        }
    }
    else{
        return false;
    }
}

function deleteDirectory($path, $dirName){
    // if (!checkAccessRights($path, $_SESSION['user'])){
    //     return false;
    // }
    $ini = parse_ini_file("database/mysql.ini");
    $mysql = mysqli_connect($ini['host'], $ini['user'], $ini['password'], $ini['database']);
    if (!$mysql) {
        return false;
    }
    else{
        $dir = $path . "/" . $dirName;
        if (removeFromAccessrights($mysql, $dir)) {
            if (removeDir($dir, $mysql)) {
                mysqli_close($mysql);
                return true;
            } 
            else {
                addToAccessrights($mysql, $dir);
                mysqli_close($mysql);
                return false;
            }
        }
        else{
            mysqli_close($mysql);
            return false;
        }
    }
}

function uploadFile($path){
    // if (!checkAccessRights($path, $_SESSION['user'])){
    //     return false;
    // }
    $size = $_FILES['file']['size'];
    if ($size > $_SESSION['availablespace']){
        return false;
    }
    else{
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
        $ini = parse_ini_file("database/mysql.ini");
        $mysql = mysqli_connect($ini['host'], $ini['user'], $ini['password'], $ini['database']);
        if (move_uploaded_file($_FILES['file']['tmp_name'], $filePath)) {
            if (addToAccessrights($mysql, $filePath)) {
                if (newAvailableSpace($size, "+", $_SESSION['username'], $mysql)){
                    mysqli_close($mysql);
                    return true;
                }
                else{
                    removeFromAccessrights($mysql, $filePath);
                    mysqli_close($mysql);
                    unlink($filePath);
                    return false;
                }
            }
            else{
                mysqli_close($mysql);
                unlink($filePath);
                return false;
            }
        }
        else{
            mysqli_close($mysql);
            return false;
        }
    }
}


function downloadFile($path, $fileName){
    // if (!checkAccessRights($path, $_SESSION['user'])){
    //     return false;
    // }
    return true;
}

function deleteFile($path, $fileName){
    // if (!checkAccessRights($path, $_SESSION['user'])){
    //     return false;
    // }
    $file = $path . "/" . $fileName;
    $size = filesize($file);
    $ini = parse_ini_file("database/mysql.ini");
    $mysql = mysqli_connect($ini['host'], $ini['user'], $ini['password'], $ini['database']);
    if (!$mysql) {
        return false;
    }
    else{
        if (newAvailableSpace($size, "-", $_SESSION['username'], $mysql)) {
            if (removeFromAccessrights($mysql, $file)){
                if (unlink($file)){
                    mysqli_close($mysql);
                    return true;
                }
                else{
                    addToAccessrights($mysql, $file);
                    newAvailableSpace($size, "+", $_SESSION['username'], $mysql);
                    mysqli_close($mysql);
                    return false;
                }
            }
            else{
                newAvailableSpace($size, "+", $_SESSION['username'], $mysql);
                mysqli_close($mysql);
                return false;
            }
        }
        else{
            mysqli_close($mysql);
            return false;
        }
    }
}

function changeMod($path, $fileName, $newMod, $usersArr = array()) {
    // if (!checkAccessRights($path, $_SESSION['user'])){
    //     return false;
    // }
    $file = $path.'/'.$fileName;
    $ini = parse_ini_file("database/mysql.ini");
    $mysql = mysqli_connect($ini['host'], $ini['user'], $ini['password'], $ini['database']);
    if ($mysql) {
        if ($newMod == 1){
            $sharedaccess = "";
            foreach ($usersArr as $username) {
                $userdata = getConcreteUser($mysql, "username", $username);
                if ($userdata) {
                    $sharedaccess .= $userdata['id']."/";
                } 
                else {
                    mysqli_close($mysql);
                    return false;
                }
            }
            if (!mysqli_query($mysql, "UPDATE `accessrights` SET `sharedaccess`='$sharedaccess', `accessmod`=$newMod WHERE path='$file'")){
                mysqli_close($mysql);
                return false;
            }
            else{
                mysqli_close($mysql);
                return true;
            }
        }
        elseif($newMod == 0 || $newMod == 2){
            if (!mysqli_query($mysql, "UPDATE `accessrights` SET `sharedaccess`='', `accessmod`=$newMod WHERE path='$file'")){
                mysqli_close($mysql);
                return false;
            }
            else{
                mysqli_close($mysql);
                return true;
            }
        }
    }
    else {
        mysqli_close($mysql);
        return false;
    }
}

function changeDirectory($dirName){
    // if (!checkAccessRights($path, $_SESSION['user'])){
    //     return false;
    // }
    $_SESSION['path'] .= '/' . $dirName;
    return true;
}

function goBack($path){
    if (preg_match_all("/\//uis", $path) == 1) {
        return false;
    }
    else{
        preg_match("/(?<newPath>.*)\/.*?$/uis", $path, $arr);
        $_SESSION['path'] = $arr['newPath'];
        return true;
    }
}
