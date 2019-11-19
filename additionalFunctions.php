<?php

session_start();

function newWindow($path, $username){
    $ini = parse_ini_file("database/mysql.ini");
    $mysql = mysqli_connect($ini['host'], $ini['user'], $ini['password'], $ini['database']);
    $htmldir = "";
    $htmlfile = "";
    $files = glob($path."/*");
    foreach($files as $filename){
        $nameFile = basename($filename);
        if (is_dir($filename)){
            $htmldir .= "<div class=\"directory\">";
            $accessRights = checkAccessRights($mysql, $filename, $username);
            if($accessRights === 0){
                $htmldir .= "<div class=\"hide\">
                                <input class=\"changeMod\" type=\"button\" value=\"".getAccessrights($mysql, $filename)."\" onclick=\"changeMod(this.value, '".preg_replace("/'/uis", "\'", $nameFile)."')\" />
                                <input class=\"delete\" type=\"button\" value=\"&nbsp;\" onclick=\"deleteDirectory('".preg_replace("/'/uis", "\'", $nameFile)."')\" />
                             </div>
                            <img src=\"images/dir.png\" onclick=\"changeDirectory('".preg_replace("/'/uis", "\'", $nameFile)."')\" />$nameFile
                        </div>";
            }
            elseif($accessRights === 1 || $accessRights === 2){
                $htmldir .= "<img src=\"images/dir.png\" onclick=\"changeDirectory('".preg_replace("/'/uis", "\'", $nameFile)."')\" />$nameFile</div>";
            }
        }
        else {
            $htmlfile .= "<div class=\"file\">";
            $accessRights = checkAccessRights($mysql, $filename, $username);
            if($accessRights === 0){
                $htmlfile .= "<div class=\"hide\">
                                <input class=\"changeMod\" type=\"button\" value=\"".getAccessrights($mysql, $filename)."\" onclick=\"changeMod(this.value, '".preg_replace("/'/uis", "\'", $nameFile)."')\" />
                                <input class=\"download\" type=\"button\" value=\"&nbsp;\" onclick=\"downloadFile('".preg_replace("/'/uis", "\'", $nameFile)."')\" />
                                <input class=\"delete\" type=\"button\" value=\"&nbsp;\" onclick=\"deleteFile('".preg_replace("/'/uis", "\'", $nameFile)."')\" />
                              </div>
                            <img src=\"images/file.png\" />$nameFile
                        </div>";
            }
            elseif($accessRights === 1 || $accessRights === 2){
                $htmlfile .= "<div class=\"hide\">
                                <input class=\"download\" type=\"button\" value=\"&nbsp;\" onclick=\"downloadFile('".preg_replace("/'/uis", "\'", $nameFile)."')\" />
                              </div>
                            <img src=\"images/file.png\" />$nameFile
                        </div>";
            }
        }
    }
    mysqli_close($mysql);
    return $htmldir.$htmlfile;
}

function removeDir($path, $mysql){
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
            if (newAvailableSpace($size, "-", $_SESSION['username'], $mysql)) {
                if (removeFromAccessrights($mysql, $file)){
                    if (unlink($file)) {
                        continue;
                    }
                    else{
                        addToAccessrights($mysql, $file);
                        newAvailableSpace($size, "+", $_SESSION['username'], $mysql);
                        return false;
                    }
                }
                else{
                    newAvailableSpace($size, "+", $_SESSION['username'], $mysql);
                    return false;
                }
            }
            else{
                return false;
            }
        }
    }
    if (removeFromAccessrights($mysql, $path)){
        if (rmdir($path)){
            return true;
        }
        else{
            addToAccessrights($mysql, $path);
            return false;
        }
    }
    else{
        return false;
    }
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
            $_SESSION['availablespace'] = $availablespace;
            return true;
        }
        else{
            return false;
        }
    }
    else{
        return false;
    }
}

function checkCoockie(){
    if (isset($_COOKIE["cloudStorage"])){
        $coockieArr = explode(":",$_COOKIE["cloudStorage"]);
        $email = $coockieArr[0];
        $ini = parse_ini_file("database/mysql.ini");
        $mysql = mysqli_connect($ini['host'], $ini['user'], $ini['password'], $ini['database']);
        if(!$mysql){
            print("Возникла ошибка во время выполнения. Попробуйте обновить страницу.");
            die();
        }
        $secretKey = "";
        $user = getConcreteUser($mysql, "email", $email);
        if($user){
            $secretKey = $user['secretkey'];
        }
        else{
            mysqli_close($mysql);
            return false;
        }
        $coockieKeyHash = $coockieArr[1];
        $key = $secretKey.$_SERVER["REMOTE_ADDR"];
        if (password_verify($key, $coockieKeyHash)){
                $_SESSION['username'] = $user["username"];
                $_SESSION['path'] = "localStorage/".$_SESSION["username"];
                $_SESSION['availablespace'] = $user["availablespace"];
                mysqli_close($mysql);
        }
        else{
            mysqli_close($mysql);
            return checkCoockie();
        }
        return true;
    }
    else{
        return false;
    }
}

function checkAccessRights($mysql, $path, $username){
    $result = mysqli_query($mysql, "SELECT `owner` FROM `accessrights` WHERE path='$path'");
    if ($result) {
        $owner = mysqli_fetch_array($result)['owner'];
        if ($owner == $username) {
            return 0;
        }
        else {
            $result = mysqli_query($mysql, "SELECT `accessmod` FROM `accessrights` WHERE path='$path'");
            if ($result) {
                $accessmod = mysqli_fetch_array($result)['accessmod'];
                if ($accessmod == 0){
                        return -1;
                }
                elseif ($accessmod == 1){
                    $result = mysqli_query($mysql, "SELECT `sharedaccess` FROM `accessrights` WHERE path='$path'");
                    if ($result) {
                        $sharedaccess = mysqli_fetch_array($result)['sharedaccess'];
                        $result = mysqli_query($mysql, "SELECT `id` FROM `users` WHERE username='$username'");
                        if ($result) {
                            $id = mysqli_fetch_array($result)['id'];
                            if (preg_match("/\/$id\//uis", $sharedaccess)) {
                                return 1;
                            }
                            else{
                                return -1;
                            }
                        }
                        else{
                            return -1;
                        }
                    }
                    else{
                        return -1;
                    }
                }
                elseif($accessmod == 2){
                    return 2;
                }
            }
            else{
                return -1;
            }
        }
    }
    else{
        return -1;
    }
}