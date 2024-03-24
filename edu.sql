-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Мар 24 2024 г., 17:27
-- Версия сервера: 8.0.24
-- Версия PHP: 8.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `edu`
--

-- --------------------------------------------------------

--
-- Структура таблицы `basket`
--

CREATE TABLE `basket` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `amount_product` int NOT NULL DEFAULT '1',
  `is_open` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `basket`
--

INSERT INTO `basket` (`id`, `user_id`, `order_id`, `product_id`, `amount_product`, `is_open`) VALUES
(80, 1, 1, 4, 3, 0),
(87, 3, 4, 11, 1, 0),
(89, 3, 5, 1, 5, 0),
(98, 1, 6, 6, 2, 0),
(100, 1, 6, 4, 2, 0),
(105, 4, 7, 9, 89, 0),
(108, 4, 8, 14, 1, 0),
(109, 1, 9, 3, 1, 0),
(120, 5, 11, 8, 2, 0),
(121, 6, 12, 12, 2, 0),
(122, 6, 12, 10, 1, 0),
(127, 6, 13, 11, 1, 1),
(128, 6, 13, 12, 3, 1),
(141, 1, 14, 3, 5, 0),
(158, 1, 15, 11, 1, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `name` varchar(30) NOT NULL,
  `description` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `photo` varchar(50) NOT NULL,
  `company` varchar(30) NOT NULL,
  `amount` int NOT NULL,
  `price` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `photo`, `company`, `amount`, `price`) VALUES
(1, 'Банан', 'Банан португальский', 'img_1.jpg', 'Fyffes', 11, 30),
(2, 'Яблоко', 'Яблоко кисло-сладкое', 'img_2.jpg', 'Fyffes', 1, 30),
(3, 'Клюква', 'Клюква Ягоды Карелии свежая, мытая', 'img_3.jpg', 'СППСК Ягоды Карелии', 12, 400),
(4, 'Салат', 'Салат Эко-культура Афицион в горшочке', 'img_4.jpg', 'Торговый дом Эко-Культура', 17, 80),
(5, 'Лимон', 'Лимон из Турции', 'img_5.webp', 'Торговый дом Эко-Культура', 38, 40),
(6, 'Груша', 'Груши Китайские', 'img_6.webp', 'Fyffes', 65, 100),
(7, 'Грейпфрут', 'Грейпфрут красный', 'img_7.webp', 'Global village', 89, 130),
(8, 'Грейпфрут', 'Грейпфрут Свити', 'img_8.webp', 'Global village', 121, 140),
(9, 'Яблоко', 'Яблоки Симиренко', 'img_9.webp', 'Global village', 69, 132),
(10, 'Мандарины', 'Мандарины, 562 г', 'img_10.webp', 'Fyffes', 97, 88),
(11, 'Авокадо', 'Авокадо, 400 г', 'img_11.webp', 'Агроном-сад', 62, 177),
(12, 'Айва', 'Айва, 500 г', 'img_12.webp', 'Агроном-сад', 128, 85),
(13, 'Картофель', 'Картофель мытый', 'img_13.webp', 'Торговый дом Эко-Культура', 10, 139),
(14, 'Картофель', 'Картофель красный мытый, 900 г', 'img_14.webp', 'Global village', 61, 45),
(15, 'Редис', 'Редис красный', 'img_15.webp', 'Fyffes', 6, 120);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `login` varchar(50) NOT NULL,
  `password` varchar(200) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `login`, `password`, `name`) VALUES
(1, 'user1', '$2y$10$9LbN/pb06w6LcTEnhKQ7keJu56hFGsga9YJ6auR3/9gUL4yGR9UgS', 'Azat'),
(2, 'ivan', '$2y$10$nsfQkNEc0EQFUw.gY9iTCuVf/vC/dWNASviLNScQ./9bnKE6nVjkq', 'user3'),
(3, 'azat', '$2y$10$qPfWfpt6fpcze2Vlm8tgRuKwsewhpczmZr6rHpjdZlpNOdpUCHKBm', 'user4'),
(4, 'user2', '$2y$10$4W35tp.wVHx7J5GumoLpReC2gQak5tHedNVpg/PgCRC7zAKIXJBK6', 'Ivan'),
(5, 'user4', '$2y$10$bqB2TJeuqiTA9etCiexHXe5FSeXgfoa0Dmoj/.CVuAm6K7Y07d5wW', 'Petr'),
(6, 'smak80', '$2y$10$yvk7y4qcHFjff4wWplYP.ua.pRWalpaXA.ThQKXtVwwXWQCMnU.72', 'Сергей'),
(7, 'user10', '$2y$10$8Q7xYyw/WfuEHJRrwf35POSc9cHCVtH7l9.WQ6.lSPKQysZr0VJqi', 'Павел'),
(8, 'user5', '$2y$10$Naz4dT5uUUzbUDLmUQZ.YOZTfANBoMB/iBf5pku47hABfyQ83.SlW', 'Василий');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `basket`
--
ALTER TABLE `basket`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Индексы таблицы `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `basket`
--
ALTER TABLE `basket`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=161;

--
-- AUTO_INCREMENT для таблицы `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `basket`
--
ALTER TABLE `basket`
  ADD CONSTRAINT `basket_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `basket_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
