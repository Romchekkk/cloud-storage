<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('change mod of folder to Exclusive and back( to all)');
$I->amOnPage('/');
$I->click('#auth');
$I->fillField('input[name=email]','nukce@mail.ru');
$I->fillField('input[name=password]','123');
$I->click('#regAuth');
$I->seeInDatabase ('accessrights', array ('path' => 'localStorage/nukce', 'owner' => 'nukce', 'accessmod' => 2, 'sharedaccess' => 1));
$I->click('//input[@value="Изменить"]');
$I->click('#firstVar');
$I->wait(0.1);  //ждем долю секунды, чтобы в базе данных однозначно успело измениться
$I->seeInDatabase ('accessrights', array ('path' => 'localStorage/nukce', 'owner' => 'nukce', 'accessmod' => 0, 'sharedaccess' => 1));
$I->click('#secondVar');
$I->wait(0.1);
$I->seeInDatabase ('accessrights', array ('path' => 'localStorage/nukce', 'owner' => 'nukce', 'accessmod' => 2, 'sharedaccess' => 1));