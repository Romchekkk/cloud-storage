<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('registrate user which was already registrated');
$I->amOnPage('/'); //начинаем с главной страницы
$I->fillField('input[name=username]','nukce');
$I->fillField('input[name=email]','nukce@mail.ru');
$I->fillField('input[name=password]','123');
$I->click('#regAuth');
$I->see('Имя пользователя занято!');
