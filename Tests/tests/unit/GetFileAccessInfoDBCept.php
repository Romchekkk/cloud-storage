<?php 
require_once("../dataBaseClass.php");
$I = new UnitTester($scenario);
$I->wantTo('get owner from certain file from DB');
$mysql = new dataBase();

//проверяем права доступа и инофрмацию конкретного файла
$I->seeInDatabase ('accessrights', array ('path' => 'localStorage/nukce', 'owner' => 'nukce', 'accessmod' => '2'));

//получаем информацию и сверяем ее
$info = $mysql->getFileAccessInfo('localStorage/nukce');

$I->assertEquals('localStorage/nukce', $info['path']);
$I->assertEquals('nukce', $info['owner']);
$I->assertEquals('2', $info['accessmod']);
$I->assertEquals(NULL, $info['sharedaccess']);