<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('registrate a new user and save him in DataBase');
$I->amOnPage('/');
$I->fillField('username','hacker');
$I->fillField('email','hacker@mail.ru');
$I->fillField('password','123');
$I->click('Зарегистрироваться');
$I->seeInDatabase ('users', array ('username' => 'hacker', 'email' => 'hacker@mail.ru'));
$I->seeInDatabase ('accessrights', array ('path' => 'localStorage/hacker', 'owner' => 'hacker'));


