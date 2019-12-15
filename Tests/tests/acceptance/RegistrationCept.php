<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('registrate a new user');
$I->amOnPage('/'); //начинаем с главной страницы
$I->fillField('input[name=username]','hacker');
$I->fillField('input[name=email]','hacker@mail.ru');
$I->fillField('input[name=password]','123');
$I->click('#regAuth');
$I->see('Добро пожаловать, hacker');
rmdir('C:\Open_Server\OSPanel\domains\c.s\cloud-storage\localStorage\hacker');