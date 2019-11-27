<?php 
require_once("../dataBaseClass.php");
$I = new UnitTester($scenario);
$I->wantTo('update user`s availablespace in dataBase');
$mysql = new dataBase();

//добваляем пользователя c заданными параметрами и проверяем его наличе в базе данных
$mysql->addUser('hacker','hacker@mail.ru','123','str');
$I->seeInDatabase ('users', array ('username' => 'hacker', 'email' => 'hacker@mail.ru',
    'availablespace' => '104857600'));

 //изменяем размер доступной памияти и проверяем изменение в базе данных
 $mysql->updateAvailableSpace(1337,'hacker');
 $I->seeInDatabase ('users', array ('username' => 'hacker', 'email' => 'hacker@mail.ru',
    'availablespace' => '1337'));

