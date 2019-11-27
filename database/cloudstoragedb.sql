-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Ноя 17 2019 г., 22:03
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
('localStorage/nukce', 'nukce', 2, '1');

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
(1, 'nukce', 'nukce@mail.ru', '$2y$10$Ed1zybf.iDOm.DdmuiDO.esgbiftJmmMfwdW72Q1UFDG3eIFAaMMu', '104857600', '5dd16cd5c40a4');

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
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'уникальный идентификатор', AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
