<?php

require_once "dataBaseClass.php";

session_start();

function newWindow(&$mysql, $path, $username){
    $htmldir = "";
    $htmlfile = "";
    $files = glob($path."/*");
    foreach($files as $filename){
        $nameFile = basename($filename);
        if (is_dir($filename)){
            $accessRights = checkAccessRights($mysql, $filename, $username);
            if($accessRights === 0){
                $htmldir .= "<div class=\"directory\">
                                <div class=\"hide\">
                                    <input class=\"changeMod\" type=\"button\" value=\"&nbsp;\" onclick=\"show('".preg_replace("/'/uis", "\'", $nameFile)."', false)\" />
                                    <input class=\"delete\" type=\"button\" value=\"&nbsp;\" onclick=\"deleteDirectory('".preg_replace("/'/uis", "\'", $nameFile)."')\" />
                                </div>
                            <img src=\"images/dir.png\" onclick=\"changeDirectory('".preg_replace("/'/uis", "\'", $nameFile)."')\" />$nameFile
                        </div>";
            }
            elseif($accessRights === 1 || $accessRights === 2){
                $htmldir .= "<div class=\"directory\">
                <img src=\"images/dir.png\" onclick=\"changeDirectory('".preg_replace("/'/uis", "\'", $nameFile)."')\" />$nameFile
                </div>";
            }
        }
        else {
            $htmlfile .= "";
            $accessRights = checkAccessRights($mysql, $filename, $username);
            if($accessRights === 0){
                $htmlfile .= "<div class=\"file\">
                                <div class=\"hide\">
                                    <input class=\"changeMod\" type=\"button\" value=\"&nbsp;\" onclick=\"show('".preg_replace("/'/uis", "\'", $nameFile)."', false)\" />
                                    <input class=\"download\" type=\"button\" value=\"&nbsp;\" onclick=\"downloadFile('".preg_replace("/'/uis", "\'", $nameFile)."')\" />
                                    <input class=\"delete\" type=\"button\" value=\"&nbsp;\" onclick=\"deleteFile('".preg_replace("/'/uis", "\'", $nameFile)."')\" />
                                </div>
                            <img src=\"images/file.png\" />$nameFile
                        </div>";
            }
            elseif($accessRights === 1 || $accessRights === 2){
                $htmlfile .= "<div class=\"file\">
                                <div class=\"hide\">
                                    <input class=\"download\" type=\"button\" value=\"&nbsp;\" onclick=\"downloadFile('".preg_replace("/'/uis", "\'", $nameFile)."')\" />
                                </div>
                                <img src=\"images/file.png\" />$nameFile
                            </div>";
            }
        }
    }
    return $htmldir.$htmlfile;
}

function removeDir($path, &$mysql){
    foreach (glob($path . '/*') as $file) {
        if (is_dir($file)) {
            if (removeDir($file, $mysql)){
                continue;
            }
            else{
                return false;
            }
        } 
        else {
            $size = filesize($file);
            if (unlink($file)) {
                newAvailableSpace($size, "-", $_SESSION['username'], $mysql);
                $mysql->removeFromAccessrights($file);
                continue;
            }
            else{
                return false;
            }
        }
    }
    if (rmdir($path)){
        $mysql->removeFromAccessrights($path);
        return true;
    }
    return false;
}

function newAvailableSpace($size, $sign, $username, &$mysql){
    $user = $mysql->getParticularUser("username", $username);
    $availablespace = 0;
    if ($sign == "+"){
        $availablespace = $user['availablespace']+$size;
    }
    else{
        $availablespace = $user['availablespace']-$size;
    }
    $mysql->updateAvailableSpace($availablespace, $username);
    $_SESSION['availablespace'] = $availablespace;
    return true;
}

function checkCoockie(&$mysql){
    if (isset($_COOKIE["cloudStorage"])){
        $coockieArr = explode(":", $_COOKIE["cloudStorage"]);
        $email = $coockieArr[0];
        $secretKey = "";
        if($user = $mysql->getParticularUser("email", $email)){
            $secretKey = $user['secretkey'];
        }
        else{
            return false;
        }
        $coockieKeyHash = $coockieArr[1];
        $key = $secretKey.$_SERVER["REMOTE_ADDR"];
        if (password_verify($key, $coockieKeyHash)){
            $_SESSION['username'] = $user["username"];
            $_SESSION['path'] = "localStorage/".$_SESSION["username"];
            $_SESSION['availablespace'] = $user["availablespace"];
            return true;
        }
        return false;
    }
    return false;
}

function checkAccessRights(&$mysql, $path, $username){
    $fileAccessInfo = $mysql->getFileAccessInfo($path);
    $accessmod = $fileAccessInfo['accessmod'];
    if ($fileAccessInfo['owner'] == $username) {
        return 0;
    }
    elseif ($accessmod == 0){
        return -1;
    }
    elseif ($accessmod == 1){
        $id = $mysql->getUserId($username);
        if (preg_match("/\/$id\//uis", $fileAccessInfo['sharedaccess'])) {
            return 1;
        }
        return -1;
    }
    elseif($accessmod == 2){
        return 1;
    }
}