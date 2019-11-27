<?php 
require_once("../dataBaseClass.php");
$I = new UnitTester($scenario);
$I->wantTo('get particular user from dataBase');
$mysql = new dataBase();

//получаем пользователя по заданному имени
$user = $mysql->getParticularUser('username','nukce');
$I->assertEquals('nukce@mail.ru',$user['email']);

//получаем пользователя по заданной почте
$user = $mysql->getParticularUser('email','nukce@mail.ru');
$I->assertEquals('nukce',$user['username']);
