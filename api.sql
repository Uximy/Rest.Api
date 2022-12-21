-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Окт 31 2022 г., 16:54
-- Версия сервера: 8.0.31-0ubuntu0.22.04.1
-- Версия PHP: 8.1.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `api`
--

-- --------------------------------------------------------

--
-- Структура таблицы `accounts`
--

CREATE TABLE `accounts` (
  `id` int NOT NULL,
  `login` varchar(252) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `pwd_hash` varchar(252) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `email` varchar(252) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `steamid` varchar(252) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `ip` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `autologin` int DEFAULT NULL,
  `tokens` int DEFAULT NULL,
  `exp` int DEFAULT NULL,
  `skill_points` int DEFAULT NULL,
  `admin_lvl` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `accounts`
--

INSERT INTO `accounts` (`id`, `login`, `pwd_hash`, `email`, `steamid`, `ip`, `autologin`, `tokens`, `exp`, `skill_points`, `admin_lvl`) VALUES
(1, 'uximy', '6dbd0fe19c9a301c4708287780df41a2', 'uximy6553@gmail.com', '3324223432423214324', '35.334.12.424', 1, 10099, 15100, 10, 1),
(2, 'test2', 'd8578edf8458ce06fbc5bb76a58c5ca4', 'test@gmail.com', '6546546', '60890345345', 0, 2500, 1000, 2, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `bans`
--

CREATE TABLE `bans` (
  `id` int NOT NULL,
  `account_id` int DEFAULT NULL,
  `reason` int DEFAULT NULL,
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `duration` int DEFAULT NULL COMMENT 'длительность времени в секундах',
  `ingame` tinyint(1) DEFAULT NULL,
  `set_by` int DEFAULT NULL,
  `set_etc` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `removed_by` int DEFAULT NULL,
  `removed_etc` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `bans`
--

INSERT INTO `bans` (`id`, `account_id`, `reason`, `date`, `duration`, `ingame`, `set_by`, `set_etc`, `removed_by`, `removed_etc`) VALUES
(10, 1, 1, '2022-10-28 22:35:33', 60, 1, 1, 'none', NULL, NULL),
(11, 1, 2, '2022-10-29 02:07:26', 11, 1, 1, 'none', NULL, NULL),
(12, 1, 2, '2022-10-29 10:48:32', 60, 1, 1, 'none', NULL, NULL),
(13, 1, 2, '2022-10-29 10:49:32', 60, 1, 1, 'none', NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `gags`
--

CREATE TABLE `gags` (
  `id` int NOT NULL,
  `account_id` int DEFAULT NULL,
  `reason` int DEFAULT NULL,
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `duration` int DEFAULT NULL,
  `ingame` int DEFAULT NULL,
  `set_by` int DEFAULT NULL,
  `set_etc` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `removed_by` int DEFAULT NULL,
  `removed_etc` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `gags`
--

INSERT INTO `gags` (`id`, `account_id`, `reason`, `date`, `duration`, `ingame`, `set_by`, `set_etc`, `removed_by`, `removed_etc`) VALUES
(21, 1, 2, '2022-10-28 22:08:57', 11, 1, 1, 'none', NULL, NULL),
(27, 1, 2, '2022-10-29 10:45:09', 60, 1, 1, 'none', NULL, NULL),
(28, 1, 2, '2022-10-29 10:46:11', 60, 1, 1, 'none', NULL, NULL),
(29, 1, 2, '2022-10-29 10:47:11', 60, 1, 1, 'none', NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `items`
--

CREATE TABLE `items` (
  `id` int NOT NULL,
  `owner_id` int NOT NULL,
  `type` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `items`
--

INSERT INTO `items` (`id`, `owner_id`, `type`) VALUES
(1, 1, 5),
(2, 2, 6),
(3, 1, 20);

-- --------------------------------------------------------

--
-- Структура таблицы `packs`
--

CREATE TABLE `packs` (
  `id` int NOT NULL,
  `owner_id` int NOT NULL,
  `type` int DEFAULT NULL,
  `until` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `packs`
--

INSERT INTO `packs` (`id`, `owner_id`, `type`, `until`) VALUES
(1, 2, 5, '2022-10-26 21:00:00'),
(2, 2, 50, '2022-10-31 20:00:00'),
(4, 2, 20, '2022-10-31 20:00:00');

-- --------------------------------------------------------

--
-- Структура таблицы `skills`
--

CREATE TABLE `skills` (
  `id` int NOT NULL,
  `owner_id` int NOT NULL,
  `type` int NOT NULL,
  `level` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `skills`
--

INSERT INTO `skills` (`id`, `owner_id`, `type`, `level`) VALUES
(1, 1, 5, 10),
(2, 1, 4, 60);

-- --------------------------------------------------------

--
-- Структура таблицы `stats_clanwar`
--

CREATE TABLE `stats_clanwar` (
  `id` int NOT NULL,
  `kills` int NOT NULL,
  `deaths` int NOT NULL,
  `headshots` int NOT NULL,
  `plants` int NOT NULL,
  `explosions` int NOT NULL,
  `defusions` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `stats_clanwar`
--

INSERT INTO `stats_clanwar` (`id`, `kills`, `deaths`, `headshots`, `plants`, `explosions`, `defusions`) VALUES
(1, 890, 4, 5, 6, 7, 8);

-- --------------------------------------------------------

--
-- Структура таблицы `stats_classic`
--

CREATE TABLE `stats_classic` (
  `id` int NOT NULL,
  `kills` int NOT NULL,
  `deaths` int NOT NULL,
  `headshots` int NOT NULL,
  `plants` int NOT NULL,
  `explosions` int NOT NULL,
  `defusions` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `stats_classic`
--

INSERT INTO `stats_classic` (`id`, `kills`, `deaths`, `headshots`, `plants`, `explosions`, `defusions`) VALUES
(1, 5002, 4999, 5, 6, 7, 8);

-- --------------------------------------------------------

--
-- Структура таблицы `stats_dm_ffa`
--

CREATE TABLE `stats_dm_ffa` (
  `id` int NOT NULL,
  `kills` int NOT NULL,
  `deaths` int NOT NULL,
  `headshots` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `stats_dm_ffa`
--

INSERT INTO `stats_dm_ffa` (`id`, `kills`, `deaths`, `headshots`) VALUES
(1, 10001, 12, 13);

-- --------------------------------------------------------

--
-- Структура таблицы `stats_duel`
--

CREATE TABLE `stats_duel` (
  `id` int NOT NULL,
  `kills` int NOT NULL,
  `deaths` int NOT NULL,
  `headshots` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `bans`
--
ALTER TABLE `bans`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `gags`
--
ALTER TABLE `gags`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `packs`
--
ALTER TABLE `packs`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `skills`
--
ALTER TABLE `skills`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `stats_clanwar`
--
ALTER TABLE `stats_clanwar`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `stats_classic`
--
ALTER TABLE `stats_classic`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `stats_dm_ffa`
--
ALTER TABLE `stats_dm_ffa`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `stats_duel`
--
ALTER TABLE `stats_duel`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `bans`
--
ALTER TABLE `bans`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT для таблицы `gags`
--
ALTER TABLE `gags`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT для таблицы `items`
--
ALTER TABLE `items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `packs`
--
ALTER TABLE `packs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `skills`
--
ALTER TABLE `skills`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `stats_clanwar`
--
ALTER TABLE `stats_clanwar`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `stats_classic`
--
ALTER TABLE `stats_classic`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `stats_dm_ffa`
--
ALTER TABLE `stats_dm_ffa`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `stats_duel`
--
ALTER TABLE `stats_duel`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
