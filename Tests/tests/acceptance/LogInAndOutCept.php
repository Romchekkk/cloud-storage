<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('log in from registrated user and logout');
$I->amOnPage('/'); 
$I->see('Cloud storage by Romchekkk, Knya and SosiskaKiller');
$I->click('#auth');
$I->fillField('input[name=email]','nukce@mail.ru');
$I->fillField('input[name=password]','123');
$I->click('#regAuth');
$I->see('Добро пожаловать, nukce');
$I->dontSee('Cloud storage by Romchekkk, Knya and SosiskaKiller');
$I->click('//input[@value="Выйти"]');
$I->see('Cloud storage by Romchekkk, Knya and SosiskaKiller');