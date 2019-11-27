<?php
require_once("../dataBaseClass.php");
$I = new UnitTester($scenario);
$I->wantTo('add user to dataBase');

//пользователь отсутсвует в базе данных
$I->dontSeeInDatabase ('users', array ('username' => 'hacker', 'email' => 'hacker@mail.ru'));
$mysql = new dataBase();

//добваляем пользователя и проверяем его наличе в базе данных
$mysql->addUser('hacker','hacker@mail.ru','123','str');
$I->seeInDatabase ('users', array ('username' => 'hacker', 'email' => 'hacker@mail.ru'));