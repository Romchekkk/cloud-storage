<?php 
require_once("../dataBaseClass.php");
$I = new UnitTester($scenario);
$I->wantTo('remove user from dataBase');
$mysql = new dataBase();

//добваляем пользователя и проверяем его наличе в базе данных
$mysql->addUser('hacker','hacker@mail.ru','123','str');
$I->seeInDatabase ('users', array ('username' => 'hacker', 'email' => 'hacker@mail.ru'));

//удаляем пользователя и проверяем его отсутсвие в базе данных
$mysql->removeUser('hacker');
$I->dontSeeInDatabase ('users', array ('username' => 'hacker'));


