<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('register new user and change mod of folder to shared(for new user) and back( to all)');
$I->amOnPage('/');
$I->fillField('input[name=username]','friend');
$I->fillField('input[name=email]','shoshlikkiller@kfc.com');
$I->fillField('input[name=password]','root');
$I->click('#regAuth');
$I->click('//input[@value="Выйти"]');
$I->click('#auth');
$I->fillField('input[name=email]','nukce@mail.ru');
$I->fillField('input[name=password]','123');
$I->click('#regAuth');
$I->seeInDatabase ('accessrights', array ('path' => 'localStorage/nukce', 'owner' => 'nukce', 'accessmod' => 2, 'sharedaccess' => 1));
$I->click('//input[@value="Изменить"]');
$I->click('#secondVar');
$I->wait(0.2);
$I->checkOption('//input[@type="checkbox"][@value="friend"]');
$I->click('#closeChangeAccessRights');
$I->wait(0.3);
$I->seeInDatabase ('accessrights', array ('path' => 'localStorage/nukce', 'owner' => 'nukce', 'accessmod' => 1, 'sharedaccess' => "1/5/"));
$I->click('//input[@value="Изменить"]');
$I->click('#secondVar');
$I->click('#closeChangeAccessRights');
$I->wait(0.3);
$I->seeInDatabase ('accessrights', array ('path' => 'localStorage/nukce', 'owner' => 'nukce', 'accessmod' => 2, 'sharedaccess' => 1));
rmdir('C:\Open_Server\OSPanel\domains\c.s\cloud-storage\localStorage\friend');