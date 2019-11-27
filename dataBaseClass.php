<?php

class dataBase{

    private $_mysql;

    public function __construct(){
        $ini = parse_ini_file("database/mysql.ini");
        $this->_mysql = mysqli_connect($ini['host'], $ini['user'], $ini['password'], $ini['database']);
    }

    public function __destruct(){
        mysqli_close($this->_mysql);
    }

    public function isConnect(){
        return $this->_mysql ? true : false;
    }

    public function getUsers(){
        $usersArr = array();
        $result = mysqli_query($this->_mysql, "SELECT * FROM `users` ORDER BY `users`.`username` ASC");
        while($row = mysqli_fetch_array($result)){
            $usersArr[] = $row;
        }
        return $usersArr;
    }

    public function getParticularUser($columnName, $value){
        if ($result = mysqli_query($this->_mysql, "SELECT * FROM `users` WHERE $columnName='$value'")) {
            $user = mysqli_fetch_array($result);
            return $user;
        }
        return false;
    }

    public function addUser($username, $email, $passwordHash, $secretKey){
        mysqli_query($this->_mysql, "INSERT INTO `users`(`username`, `email`, `password`, `secretkey`) VALUES ('$username', '$email', '$passwordHash', '$secretKey')");
    }

    public function removeUser($username){
        mysqli_query($this->_mysql, "DELETE FROM `users` WHERE username='$username'");
    }

    public function updateRegistrationInfo($newPasswordHash, $secretKey, $email){
        mysqli_query($this->_mysql, "UPDATE `users` SET `password`='$newPasswordHash', `secretKey`='$secretKey'  WHERE `email`='$email'");
    }

    public function getUsersForSearch($forSearch){
        $result = mysqli_query($this->_mysql, "SELECT `username` FROM `users` WHERE INSTR(`username`, '$forSearch')=1 ORDER BY `users`.`username` ASC");
        $i = 0;
        $usersArr = array();
        while($user = mysqli_fetch_array($result)){
            if ($i == 15){
                break;
            }
            $i++;
            $usersArr[] = $user;
        }
        return $usersArr;
    }

    public function getUserId($username){
        $result = mysqli_query($this->_mysql, "SELECT `id` FROM `users` WHERE username='$username'");
        return mysqli_fetch_array($result)['id'];
    }

    public function updateAvailableSpace($availablespace, $username){
        mysqli_query($this->_mysql, "UPDATE `users` SET `availablespace`='$availablespace' WHERE `username`='$username'");
    }
    
    public function addToAccessrights($path){
        $owner = $_SESSION['username'];
        mysqli_query($this->_mysql, "INSERT INTO `accessrights`(`path`, `owner`) VALUES ('$path', '$owner')");
        return true;
    }

    public function removeFromAccessrights($path){
        if (mysqli_query($this->_mysql, "DELETE FROM `accessrights` WHERE path='$path'")){
            return true;
        }
        return false;
    }

    public function getAccessrights($path){
        if ($result = mysqli_query($this->_mysql, "SELECT `accessmod` FROM `accessrights` WHERE path='$path'")) {
            $accessmod = mysqli_fetch_array($result)['accessmod'];
            return $accessmod;
        }
        return false;
    }

    public function getOwner($path){
        $result = mysqli_query($this->_mysql, "SELECT * FROM `accessrights` WHERE path='$path'");
        return mysqli_fetch_array($result);
    }

    public function updateAccessRights($path, $newMod, $sharedaccess = ''){
        mysqli_query($this->_mysql, "UPDATE `accessrights` SET `sharedaccess`=$sharedaccess, `accessmod`=$newMod WHERE path='$path'");
    }
}
