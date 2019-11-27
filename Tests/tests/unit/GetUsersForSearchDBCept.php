<?php 
require_once("../dataBaseClass.php");
$I = new UnitTester($scenario);
$I->wantTo('get users for search from dataBase');
$mysql = new dataBase();

//добваляем пользователя c заданными параметрами и проверяем его наличе в базе данных
$mysql->addUser('nurik','nurik@mail.ru','111','ptr');
$I->seeInDatabase ('users', array ('username' => 'nurik', 'email' => 'nurik@mail.ru'));

//проверяем что получили пользователей согласно запросу
$usersArr = $mysql->getUsersForSearch("nu");
$I->assertEquals('nukce',$usersArr[0]['username']);
$I->assertEquals('nurik',$usersArr[1]['username']);

//проверка с другим запросом
$usersArr = $mysql->getUsersForSearch("nuk");
foreach ($usersArr as $user) {
    $I->assertEquals('nukce',$user['username']);
}

