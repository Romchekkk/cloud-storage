<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('log in from registrated user');
$I->amOnPage('/'); //начинаем с главной страницы
$I->see('Cloud storage by Romchekkk, Knya and SosiskaKiller');
$I->click('#auth');
$I->fillField('input[name=email]','nukce@mail.ru');
$I->fillField('input[name=password]','123');
$I->click('#regAuth');
$I->see('Добро пожаловать, nukce');

//$I->fillField('//input[@name="email"]', "hacker@mail.ru");
//$I->fillField('input[name=username]','hacker');
//$I->appendField('//input[@name="username"]', 'hacker');
