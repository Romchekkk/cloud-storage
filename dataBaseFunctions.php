<?php

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
    $user = mysqli_fetch_array($result);
    return $user;
}