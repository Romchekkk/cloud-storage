<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('registrate a new user');
$I->amOnPage('/'); //начинаем с главной страницы
$I->fillField('input[name=username]','hackerman');
$I->fillField('input[name=email]','hackerKiller@mail.ru');
$I->fillField('input[name=password]','123');
$I->click('#regAuth');
$I->see('Добро пожаловать, hackerman');
rmdir('..\localStorage\hackerman');