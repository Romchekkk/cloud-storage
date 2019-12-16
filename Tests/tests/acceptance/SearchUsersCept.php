<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('register new user and search his account at field with all users');
$I->amOnPage('/');
$I->fillField('input[name=username]','Pudge');
$I->fillField('input[name=email]','shoshlikkiller@kfc.com');
$I->fillField('input[name=password]','FreshMeat');
$I->click('#regAuth');
$I->click('//input[@value="Выйти"]');
$I->click('#auth');
$I->fillField('input[name=email]','nukce@mail.ru');
$I->fillField('input[name=password]','123');
$I->click('#regAuth');
$I->fillField('#search','Pudge');
$I->see("Pudge");
$I->fillField('#search','NothingUser');
$I->dontSee("NothingUser");
rmdir('..\localStorage\Pudge');