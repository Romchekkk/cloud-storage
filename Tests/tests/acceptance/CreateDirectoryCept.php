<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('create a new directory and deleted');
$I->amOnPage('/'); //начинаем с главной страницы
$I->click('#auth');
$I->fillField('input[name=email]','nukce@mail.ru');
$I->fillField('input[name=password]','123');
$I->click('#regAuth');
$I->see('Добро пожаловать, nukce'); // зашли в учетную запись пользователя

//папки не было в бд
$I->dontSeeInDatabase ('accessrights', array ('path' => 'localStorage/nukce/Test_Folder', 'owner' => 'nukce')); 
$I->fillField('input[id=dirName]','Test_Folder');
$I->click('//input[@value="Создать директорию"]');
sleep(2);
//папка появилась в бд 
$I->seeInDatabase ('accessrights', array ('path' => 'localStorage/nukce/Test_Folder', 'owner' => 'nukce')); 
$I->moveMouseOver(['css' => 'div.directory']);
$I->click('.delete');
$I->wait(1);
if (file_exists('..\localstorage\nukce\Test_Folder'))
    $I->see("ERRORHAVE");
else 
    $I->see('nukce');
