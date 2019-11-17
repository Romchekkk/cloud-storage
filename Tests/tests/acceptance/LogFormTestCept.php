<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('log in to a user account that is not registered');
$I->amOnPage('/');
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
$I->see('Cloud storage by Romchekkk, Knya and SosiskaKiller');
$I->fillField('username','hacker');
$I->fillField('email','hacker@mail.ru');
$I->fillField('password','123');
$I->click('Войти'); 
$I->see('Неверный логин или пароль!');

$I->wantTo('log in to a user account that is already registered');
$I->fillField('username','nukce');
$I->fillField('email','nukce@mail.ru');
$I->fillField('password','123');
$I->click('Войти');
$I->see('Добро пожаловать, nukce');

$I->wantTo('log out');
$I->click('Выйти');
$I->see('Регистрация'); 
