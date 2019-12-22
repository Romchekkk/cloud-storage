Для запуска тестов необходимо:
1) Изменить в acceptance.suite.yml значение поля 'url' на ваш локальный адрес сайта (url: http://*/cloud-storage);
2) Изменить в unit.suite.yml значение поля 'url' на ваш локальный адрес сайта (url: http://*/cloud-storage);
3) Добавить путь к переменной среды 'PATH' значение пути до директории файла (например - C:\Open_Server\OSPanel\domains\c.s\cloud-storage\Tests\Selenium);
4) Запустить файл selenium-server-standalone.bat (..\cloud-storage\Tests\Selenium\vendor\bin\selenium-server-standalone.bat);
5) Открыть консоль из папки Test (..\cloud-storage\Tests);
6) В открывшейся консоли собрать тесты командой '.\cept build';
7) Запустить тесты командой '.\cept run';
