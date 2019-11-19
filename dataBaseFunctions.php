<?php

session_start();

function getUsers($mysql){
    $usersArr = array();
    $result = mysqli_query($mysql, "SELECT * FROM `users` ORDER BY `users`.`username` ASC");
    while($row = mysqli_fetch_array($result)){
        $usersArr[] = $row;
    }
    return $usersArr;
}

function getConcreteUser($mysql, $columnName, $value){
    $result = mysqli_query($mysql, "SELECT * FROM `users` WHERE $columnName='$value'");
    if ($result){
        $user = mysqli_fetch_array($result);
        return $user;
    }
    else{
        return false;
    }
}

function addToAccessrights($mysql, $path){
    $owner = $_SESSION['username'];
    if (mysqli_query($mysql, "INSERT INTO `accessrights`(`path`, `owner`) VALUES ('$path', '$owner')")){
        return true;
    }
    else{
        return false;
    }
}

function removeFromAccessrights($mysql, $path){
    if (mysqli_query($mysql, "DELETE FROM `accessrights` WHERE path='$path'")){
        return true;
    }
    else{
        return false;
    }
}

function getAccessrights($mysql, $path){
    $result = mysqli_query($mysql, "SELECT `accessmod` FROM `accessrights` WHERE path='$path'");
    if ($result){
        $accessmod = mysqli_fetch_array($result)['accessmod'];
        return $accessmod;
    }
    else{
        return false;
    }
}