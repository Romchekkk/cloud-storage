<?php 
require_once("../dataBaseClass.php");
$I = new UnitTester($scenario);
$I->wantTo('update registration info in dataBase');
$mysql = new dataBase();

//добваляем пользователя c заданными параметрами и проверяем его наличе в базе данных
$mysql->addUser('hacker','hacker@mail.ru','123','str');
$I->seeInDatabase ('users', array ('username' => 'hacker', 'email' => 'hacker@mail.ru',
 'password' => '123', 'secretkey' => 'str'));

 //изменяем параметры регистрации и проверяем их изменение в базе данных
 $mysql->updateRegistrationInfo('321', 'secret', 'hacker@mail.ru');
 $I->seeInDatabase ('users', array ('username' => 'hacker', 'email' => 'hacker@mail.ru',
 'password' => '321', 'secretkey' => 'secret'));
