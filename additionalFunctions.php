<?php

//Файл содержит дополнительные функции, которые используются в других .php файлах

require_once "dataBaseClass.php";

session_start();


/**
 * Создание нового окна.
 * Создает окно с файловой системой для пользователя $username по пути $path.
 *
 * @param [dataBase] $mysql - объект базы данных.
 * @param [string] $path - путь, по которому генерируется окно.
 * @param [string] $username - имя пользователя, для которого генерируется окно.
 * @return string - сгенерированное окно в видет строки.
 */
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
                            <img src=\"images/dir.png\" onclick=\"changeDirectory('".preg_replace("/'/uis", "\'", $nameFile)."')\" title=\"$nameFile\" />$nameFile
                        </div>";
            }
            elseif($accessRights === 1 || $accessRights === 2){
                $htmldir .= "<div class=\"directory\">
                <img src=\"images/dir.png\" onclick=\"changeDirectory('".preg_replace("/'/uis", "\'", $nameFile)."')\" title=\"$nameFile\" />$nameFile
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
                            <img src=\"images/file.png\" title=\"$nameFile\" />$nameFile
                        </div>";
            }
            elseif($accessRights === 1 || $accessRights === 2){
                $htmlfile .= "<div class=\"file\">
                                <div class=\"hide\">
                                    <input class=\"download\" type=\"button\" value=\"&nbsp;\" onclick=\"downloadFile('".preg_replace("/'/uis", "\'", $nameFile)."')\" />
                                </div>
                                <img src=\"images/file.png\" title=\"$nameFile\" />$nameFile
                            </div>";
            }
        }
    }
    return $htmldir.$htmlfile;
}

/**
 * Удаление папки.
 * Рекурсивно обходит указанную директорию $path и удаляет все ее 
 * 
 * поддиректории и файлы, а затем и саму директорию.
 *
 * @param [string] $path - путь к директории, которую следует удалить
 * @param [dataBase] $mysql - объект базы данных
 * @return bool - true, если директория удалена успешно
 * - false в противном случае.
 */
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
                newAvailableSpace($size, "+", $_SESSION['username'], $mysql);
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

/**
 * Обновление доступного места.
 * Обновляет значение доступного места для пользователя $username.
 *
 * @param [integer] $size - размер, на который следует обновить значение.
 * @param [string] $sign - знак операции. "+", если размер следует увеличить, и "-" в противном случае.
 * @param [string] $username - имя пользователя, у которого изменяется значение
 * @param [dataBase] $mysql - объект базы данных
 * @return void
 */
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
}

/**
 * Проверка куки.
 * Проверяет куки пользователя и авторизует его, если куки успешно прошло проверку.
 *
 * @param [dataBase] $mysql - объект базы данных
 * @return bool - true, если куки прошла проверку
 * - false в противном случае
 */
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

/**
 * Проверка права доступа.
 * Проверяет право доступа к файлу или директории $path у пользователя $username
 *
 * @param [dataBase] $mysql - объект базы данных.
 * @param [string] $path - путь к файлу или директории, для которой выполняется проверка.
 * @param [string] $username - имя пользователя, для которого выполняется проверка.
 * @return integer - 0, если пользователя является владельцем файла или директории; 
 * - 1, если пользователь не владелец файла или директории, но имеет к ней доступ;
 * - -1, если у пользователя нет доступа к файлу или директории.
 */
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