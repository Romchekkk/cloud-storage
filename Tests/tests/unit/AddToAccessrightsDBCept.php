<?php 
require_once("../dataBaseClass.php");
$I = new UnitTester($scenario);
$I->wantTo('add to accessrights another user');
$mysql = new dataBase();

//проверяем владельца конретного файла и отсутствие добавляемого
$I->seeInDatabase ('accessrights', array ('path' => 'localStorage/nukce', 'owner' => 'nukce'));
$I->dontSeeInDatabase ('accessrights', array ('path' => 'localStorage/nukce', 'owner' => 'hacker'));

//изменяем владельца конкретного файла
 $mysql->addToAccessrights('localStorage/nukce', 'hacker');
 $I->seeInDatabase ('accessrights', array ('path' => 'localStorage/nukce', 'owner' => 'hacker'));
