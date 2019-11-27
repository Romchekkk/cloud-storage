<?php 
require_once("../dataBaseClass.php");
$I = new UnitTester($scenario);
$I->wantTo('get array of users from dataBase');
$mysql = new dataBase();

//получаем массив пользвателей и проверяем его
$usersArr = $mysql->getUsers();
$I->assertEquals('nukce',$usersArr[0]['username']);
$I->assertEquals('nukce@mail.ru',$usersArr[0]['email']);


