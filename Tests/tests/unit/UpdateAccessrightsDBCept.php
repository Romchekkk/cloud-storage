<?php 
require_once("../dataBaseClass.php");
$I = new UnitTester($scenario);
$I->wantTo('update accessrights for certain file in DB');
$mysql = new dataBase();

//проверяем права для конкретного файла 
$I->seeInDatabase ('accessrights', array ('path' => 'localStorage/nukce', 'owner' => 'nukce', 'accessmod' => '2'));

//получаем режим доступа и проверяем его
$owner = $mysql->updateAccessRights('localStorage/nukce', '1', '2');
$I->seeInDatabase ('accessrights', array ('path' => 'localStorage/nukce', 'owner' => 'nukce', 'accessmod' => '1',
 'sharedaccess' => '2'));
