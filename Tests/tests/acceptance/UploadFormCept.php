<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('load file to storage');

$I->amOnPage('/'); //начинаем с главной страницы
$I->click('#auth');
$I->fillField('input[name=email]','nukce@mail.ru');
$I->fillField('input[name=password]','123');
$I->click('#regAuth');
$I->see('Добро пожаловать, nukce'); // зашли в учетную запись пользователя

$place = 104857600;
$I->see("Доступно места: $place байт");

//файла не было в хранилище
if (!file_exists('C:\Open_Server\OSPanel\domains\c.s\cloud-storage\localStorage\nukce\images.png')) {
    $I->attachFile('//input[@type="file"]', 'images.png');
    $I->click('//input[@value="Загрузить файл"]'); //загружаем файл

    //количество доступного места изменилось
    $place -= filesize('C:\Open_Server/OSPanel/domains/c.s/cloud-storage/Tests/tests/_data/images.png');

    $I->wait(1);
    $I->see("Доступно места: $place байт");

    //файл появился в хранилище
    if (file_exists('C:\Open_Server\OSPanel\domains\c.s\cloud-storage\localStorage\nukce\images.png')) {
        unlink('C:\Open_Server\OSPanel\domains\c.s\cloud-storage\localStorage\nukce\images.png');
    }

    else {
        $I->assertEquals('1','0');
    }
}
else {
    $I->assertEquals('1','0');
}



