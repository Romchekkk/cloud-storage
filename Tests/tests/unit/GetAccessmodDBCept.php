<?php 
require_once("../dataBaseClass.php");
$I = new UnitTester($scenario);
$I->wantTo('remove user from accessrights in DB');
$mysql = new dataBase();

//проверяем режим для конкретного файла
$I->seeInDatabase ('accessrights', array ('path' => 'localStorage/nukce', 'owner' => 'nukce', 'accessmod' => '2'));

//получаем режим доступа и проверяем его
$mod = $mysql->getAccessmod('localStorage/nukce');
$I->assertEquals('2', $mod);