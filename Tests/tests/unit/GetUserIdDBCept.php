<?php 
require_once("../dataBaseClass.php");
$I = new UnitTester($scenario);
$I->wantTo('get user id from dataBase');
$mysql = new dataBase();

//получаем индентификатор пользователя из базы данных
$id = $mysql->getUserId('nukce');
$I->assertEquals('1',$id);
