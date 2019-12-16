<?php 
require_once("../dataBaseClass.php");
$I = new UnitTester($scenario);
$I->wantTo('get array of users from dataBase');
$mysql = new dataBase();

//получаем массив пользвателей и проверяем его
$usersArr = $mysql->getUsers();
$I->assertEquals('cheburek',$usersArr[0]['username']);
$I->assertEquals('pivo47@mail.ru',$usersArr[0]['email']);
$I->assertEquals('hacker',$usersArr[1]['username']);
$I->assertEquals('hackerman2008@gmail.com',$usersArr[1]['email']);
$I->assertEquals('onetwoz1',$usersArr[3]['username']);
$I->assertEquals('onetwoz1@mail.ru',$usersArr[3]['email']);
$I->assertEquals('nukce',$usersArr[2]['username']);
$I->assertEquals('nukce@mail.ru',$usersArr[2]['email']);