-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Дек 16 2019 г., 23:56
-- Версия сервера: 8.0.12
-- Версия PHP: 7.2.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `cloudstoragedb`
--

-- --------------------------------------------------------

--
-- Структура таблицы `accessrights`
--

CREATE TABLE `accessrights` (
  `path` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'путь к директории',
  `owner` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Владелец файла или папки',
  `accessmod` int(1) NOT NULL DEFAULT '0' COMMENT '0 - только для клиента, 1 - для выделенной группы пользователей, 2 - для всех пользователей',
  `sharedaccess` text CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT 'id пользователей, которые имеют доступ к файлу или папке'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `accessrights`
--

INSERT INTO `accessrights` (`path`, `owner`, `accessmod`, `sharedaccess`) VALUES
('localStorage/nukce', 'nukce', 2, ''),
('localStorage/nukce/Новая папка', 'nukce', 2, ''),
('localStorage/nukce/Безымянный.png', 'nukce', 2, ''),
('localStorage/nukce/Новая папка/супер классная текстовая игра.rar', 'nukce', 1, '/2//4/'),
('localStorage/onetwoz1', 'onetwoz1', 2, ''),
('localStorage/onetwoz1/Папа', 'onetwoz1', 0, NULL),
('localStorage/onetwoz1/Папа/Рома.png', 'onetwoz1', 0, NULL),
('localStorage/onetwoz1/Игры', 'onetwoz1', 2, ''),
('localStorage/onetwoz1/Игры/Змейка.exe', 'onetwoz1', 2, ''),
('localStorage/hacker', 'hacker', 0, NULL),
('localStorage/hacker/Как взломать вконтакте', 'hacker', 0, NULL),
('localStorage/hacker/Как взломать вконтакте/уроки html.txt', 'hacker', 0, NULL),
('localStorage/hacker/Мои взломы вк', 'hacker', 0, NULL),
('localStorage/hacker/Мои взломы вк/жон.jpg', 'hacker', 0, NULL),
('localStorage/hacker/Мои взломы вк/Eggman.png', 'hacker', 0, NULL),
('localStorage/hacker/Мои взломы вк/Knakls HD.png', 'hacker', 0, NULL),
('localStorage/hacker/Мои взломы вк/Sanic HD.png', 'hacker', 0, NULL),
('localStorage/hacker/Мои взломы вк/Саник Бублек.png', 'hacker', 0, NULL),
('localStorage/hacker/Мои взломы вк/Knakls-annihilation HD.png', 'hacker', 0, NULL),
('localStorage/hacker/Мои взломы вк/ATEWrapper.dll', 'hacker', 0, NULL),
('localStorage/hacker/Мои взломы вк/icudtl.dat', 'hacker', 0, NULL),
('localStorage/hacker/Мои взломы вк/cef.pak', 'hacker', 0, NULL),
('localStorage/hacker/Мои взломы вк/icuind53.dll', 'hacker', 0, NULL),
('localStorage/hacker/Мои взломы вк/VidScreen_Phaseblocked.mp4', 'hacker', 0, NULL),
('localStorage/hacker/Мои взломы вк/VidScreen_Plucked.mp4', 'hacker', 0, NULL),
('localStorage/hacker/Моя первая программа', 'hacker', 0, NULL),
('localStorage/hacker/Моя первая программа/DeleteUselessFile.exe', 'hacker', 0, NULL),
('localStorage/hacker/Взлом пентагона', 'hacker', 0, NULL),
('localStorage/hacker/Взлом пентагона/Завершен на 14%', 'hacker', 0, NULL),
('localStorage/hacker/Взлом пентагона/Завершен на 14%/Продолжить', 'hacker', 0, NULL),
('localStorage/hacker/Взлом пентагона/Завершен на 14%/Продолжить/Взлома на 100%', 'hacker', 0, NULL),
('localStorage/hacker/Взлом пентагона/Завершен на 14%/Продолжить/Взлома на 100%/document.tex', 'hacker', 0, NULL),
('localStorage/cheburek', 'cheburek', 0, NULL),
('localStorage/cheburek/Sony.Vegas.Pro.v13.0.373.exe', 'cheburek', 0, NULL),
('localStorage/cheburek/Ответ на payday.txt', 'cheburek', 0, NULL),
('localStorage/cheburek/gsdrfhgjkhjhjtyrytyuyftdutyikuyiuytrterstrygkhjytrtyuiuuytyrdtykutrttykuuyhtghfjkhhhfjgkuliugdfygukhftyui.txt', 'cheburek', 0, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(10) NOT NULL COMMENT 'уникальный идентификатор',
  `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'имя пользователя',
  `email` varchar(255) NOT NULL COMMENT 'адрес электронной почты',
  `password` varchar(255) NOT NULL COMMENT 'хэшированный пароль',
  `availablespace` varchar(255) NOT NULL DEFAULT '104857600' COMMENT 'доступное место для хранения',
  `secretkey` varchar(255) NOT NULL COMMENT 'секретный ключ для куки-авторизации'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `availablespace`, `secretkey`) VALUES
(1, 'nukce', 'nukce@mail.ru', '$2y$10$VIVZbE/2pBVDMNwmXETtBO8dZMurwhTploqu/zCHOld9cx/b7ovr.', '98121529', '5df7ee3ee0d3e'),
(2, 'onetwoz1', 'onetwoz1@mail.ru', '$2y$10$G7qtrdAKL.x3s52SYACP0uWMo/fOSvOvT/Ef8cPASSK6lUx9zkT/2', '102828781', '5df7ea16b8d9f'),
(3, 'hacker', 'hackerman2008@gmail.com', '$2y$10$qfl7P/55UEAJl2t7tySRM.X1QX62VSq16KYgIGD61J8yEEIT5A1Le', '40837896', '5df7ef0e83495'),
(4, 'cheburek', 'pivo47@mail.ru', '$2y$10$IwAwStEjPCulBSqpEQfbPeEIPiVipkHAg0Jg6YE1xFUnYc28vKHNm', '29560997', '5df7eeffb83a0');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'уникальный идентификатор', AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
