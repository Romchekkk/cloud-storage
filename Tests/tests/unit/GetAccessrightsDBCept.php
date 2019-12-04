<?php 
require_once("../dataBaseClass.php");
$I = new UnitTester($scenario);
$I->wantTo('get accessrights for certain file from DB');
$mysql = new dataBase();

//проверяем владельца конретного файла 
$I->seeInDatabase ('accessrights', array ('path' => 'localStorage/nukce', 'owner' => 'nukce'));

//удаляем владельца конкретного файла и проверяем его отсутсвие
$mysql->removeFromAccessrights('localStorage/nukce');
$I->dontSeeInDatabase ('accessrights', array ('path' => 'localStorage/nukce', 'owner' => 'nukce'));