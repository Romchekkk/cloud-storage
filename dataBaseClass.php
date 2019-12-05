<?php

//Файл содержит класс базы данных

class dataBase{

    private $_mysql;

    /**
     * Конструктор класса.
     */
    public function __construct(){
        $ini = parse_ini_file("database/mysql.ini");
        $this->_mysql = mysqli_connect($ini['host'], $ini['user'], $ini['password'], $ini['database']);
    }

    /**
     * Деструктор класса.
     */
    public function __destruct(){
        mysqli_close($this->_mysql);
    }

    /**
     * Проверка успешности открытия базы данных.
     *
     * true, если база данных открыта;
     * 
     * false, в противном случае.
     * @return bool
     */
    public function isConnect(){
        return $this->_mysql ? true : false;
    }

    /**
     * Получить список всех пользователей из базы данных.
     *
     * Возвращает массив пользователей.
     * @return array
     */
    public function getUsers(){
        $usersArr = array();
        $result = mysqli_query($this->_mysql, "SELECT * FROM `users` ORDER BY `users`.`username` ASC");
        while($row = mysqli_fetch_array($result)){
            $usersArr[] = $row;
        }
        return $usersArr;
    }

    /**
     * Получить конкретного пользователя со значением $value в стоблце $columnName.
     *
     * Возвращает массив со значениями из базы данных, если пользователь найден;
     * 
     * false, в противном случае.
     * @param [string] $columnName - название стоблца.
     * @param [string] $value - значение в стоблце.
     * @return array|bool
     */
    public function getParticularUser($columnName, $value){
        if ($result = mysqli_query($this->_mysql, "SELECT * FROM `users` WHERE $columnName='$value'")) {
            $user = mysqli_fetch_array($result);
            return $user;
        }
        return false;
    }

    /**
     * Добавить пользователя в базу данных.
     *
     * @param [string] $username - имя пользователя.
     * @param [string] $email - электронная почта пользователя.
     * @param [string] $passwordHash - хэш пароля пользователя.
     * @param [string] $secretKey - секретный ключ пользователя.
     * @return void
     */
    public function addUser($username, $email, $passwordHash, $secretKey){
        mysqli_query($this->_mysql, "INSERT INTO `users`(`username`, `email`, `password`, `secretkey`) VALUES ('$username', '$email', '$passwordHash', '$secretKey')");
    }

    /**
     * Удаляет пользователя из базы данных.
     *
     * @param [string] $username - имя пользователя, которого требуется удалить.
     * @return void
     */
    public function removeUser($username){
        mysqli_query($this->_mysql, "DELETE FROM `users` WHERE username='$username'");
    }

    /**
     * Обновить ячейки, необходимые для проверки авторизации, у пользователя с почтой $email.
     *
     * @param [string] $newPasswordHash - новый хэш пароля пользователя.
     * @param [string] $secretKey - новый секртеный ключ пользователя.
     * @param [string] $email - электронная почта пользователя.
     * @return void
     */
    public function updateRegistrationInfo($newPasswordHash, $secretKey, $email){
        mysqli_query($this->_mysql, "UPDATE `users` SET `password`='$newPasswordHash', `secretKey`='$secretKey'  WHERE `email`='$email'");
    }

    /**
     * Получить первые 15 пользователей с именем, начинаеющимся на $forSearch.
     *
     * Возвращает массив найденных пользователей.
     * @param [type] $forSearch - начало имени пользователей.
     * @return array
     */
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

    /**
     * Получить id пользователя $username.
     *
     * Возвращает id пользователя.
     * @param [string] $username - имя пользователя.
     * @return integer
     */
    public function getUserId($username){
        $result = mysqli_query($this->_mysql, "SELECT `id` FROM `users` WHERE username='$username'");
        return mysqli_fetch_array($result)['id'];
    }

    /**
     * Обновить доступное место у пользователя $username.
     *
     * @param [type] $availablespace - новое значение доступного пространства.
     * @param [type] $username - имя пользователя.
     * @return void
     */
    public function updateAvailableSpace($availablespace, $username){
        mysqli_query($this->_mysql, "UPDATE `users` SET `availablespace`='$availablespace' WHERE `username`='$username'");
    }
    
    /**
     * Добавить файл или директорию в базу данных.
     *
     * @param [string] $path - путь к файлу иди директории.
     * @param [string] $owner - владелец файла или директории.
     * @return void
     */
    public function addToAccessrights($path, $owner){
        mysqli_query($this->_mysql, "INSERT INTO `accessrights`(`path`, `owner`) VALUES ('$path', '$owner')");
    }

    /**
     * Удалить файл или директорию из базы данных.
     *
     * @param [string] $path - путь к файлу или директории.
     * @return void
     */
    public function removeFromAccessrights($path){
        mysqli_query($this->_mysql, "DELETE FROM `accessrights` WHERE path='$path'");
    }

    /**
     * Получить режим доступа к файлу или директории.
     *
     * Возвращает действующий режим доступа, если файл или директория существует;
     * 
     * false, в противном случае.
     * @param [string] $path - путь к файлу или директории.
     * @return integer|bool
     */
    public function getAccessmod($path){
        if ($result = mysqli_query($this->_mysql, "SELECT `accessmod` FROM `accessrights` WHERE path='$path'")) {
            $accessmod = mysqli_fetch_array($result)['accessmod'];
            return $accessmod;
        }
        return false;
    }

    /**
     * Получить массив со значениями из базы данных для файла или директории.
     *
     * Возвращает массив значений из базы данных.
     * @param [string] $path - путь к файлу или директории.
     * @return array
     */
    public function getFileAccessInfo($path){
        $result = mysqli_query($this->_mysql, "SELECT * FROM `accessrights` WHERE path='$path'");
        return mysqli_fetch_array($result);
    }

    /**
     * Обновить режим доступа и список разделяемого доступа у файла или директории.
     * 
     * @param [string] $path - путь к файлу или директории.
     * @param [integer] $newMod - новый режим доступа. 
     * 
     * Передавайте -1, если его обновление не требуется.
     * @param [string] $sharedaccess - список разделяемого доступа.
     * 
     * Передавайте "-1", если его обновление не требуется.
     * @return void
     */
    public function updateAccessRights($path, $newMod, $sharedaccess){
        if ($sharedaccess == "-1"){
            $sharedaccess = $this->getFileAccessInfo($path)['sharedaccess'];
        }
        if ($newMod == -1){
            $newMod = $this->getFileAccessInfo($path)['accessmod'];
        }
        mysqli_query($this->_mysql, "UPDATE `accessrights` SET `sharedaccess`='$sharedaccess', `accessmod`=$newMod WHERE path='$path'");
    }
}