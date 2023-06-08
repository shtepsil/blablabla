-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Июн 09 2023 г., 01:46
-- Версия сервера: 8.0.30
-- Версия PHP: 8.0.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `dump`
--

-- --------------------------------------------------------

--
-- Структура таблицы `about_history`
--

CREATE TABLE `about_history` (
  `id` int NOT NULL,
  `year` varchar(255) NOT NULL COMMENT 'Год',
  `body` text NOT NULL COMMENT 'Текст',
  `sort` int DEFAULT NULL COMMENT 'Порядок'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `actions`
--

CREATE TABLE `actions` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'Название',
  `small_body` varchar(1000) DEFAULT NULL COMMENT 'Краткий текст',
  `body` text NOT NULL COMMENT 'Текст',
  `img` varchar(255) NOT NULL COMMENT 'Изображение',
  `created_at` int NOT NULL COMMENT 'Дата создания',
  `date_start` int NOT NULL COMMENT 'Дата начала',
  `date_end` int NOT NULL COMMENT 'Дата окончания',
  `isVisible` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Видимость',
  `isWholesale` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Структура таблицы `actions_items`
--

CREATE TABLE `actions_items` (
  `id` int NOT NULL,
  `action_id` int NOT NULL,
  `item_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `auth`
--

CREATE TABLE `auth` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `source` varchar(255) NOT NULL,
  `source_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `banners`
--

CREATE TABLE `banners` (
  `id` int NOT NULL,
  `img` varchar(255) NOT NULL COMMENT 'Изображение',
  `img_mobile` varchar(255) DEFAULT NULL,
  `url` varchar(255) NOT NULL COMMENT 'Ссылка',
  `isVisible` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Видимость',
  `sort` int DEFAULT '0' COMMENT 'Порядок'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `banners_cities`
--

CREATE TABLE `banners_cities` (
  `id` int NOT NULL,
  `banner_id` int NOT NULL,
  `city_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `bonus_settings`
--

CREATE TABLE `bonus_settings` (
  `id` int NOT NULL,
  `percent` decimal(12,2) NOT NULL COMMENT 'Процент',
  `price_start` int DEFAULT NULL COMMENT 'Начальная сумма',
  `price_end` int DEFAULT NULL COMMENT 'Конечная сумма'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `brands`
--

CREATE TABLE `brands` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'Название',
  `country` varchar(255) DEFAULT NULL COMMENT 'Страна',
  `isVisible` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Видимость',
  `img` varchar(255) DEFAULT NULL COMMENT 'Изображение',
  `body` text COMMENT 'Текст',
  `isBanner` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'В карусель на главную'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `callback`
--

CREATE TABLE `callback` (
  `id` int NOT NULL,
  `name` varchar(255) DEFAULT NULL COMMENT 'Имя',
  `phone` varchar(255) NOT NULL COMMENT 'Номер телефона',
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `cards`
--

CREATE TABLE `cards` (
  `id` int NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `token` varchar(100) NOT NULL,
  `card_last_four` varchar(4) DEFAULT NULL,
  `card_exp_date` varchar(10) DEFAULT NULL,
  `card_type` varchar(50) DEFAULT NULL,
  `user_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `category`
--

CREATE TABLE `category` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'Название',
  `isVisible` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Видимость',
  `isDeleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Удалена',
  `isWholesale` int NOT NULL DEFAULT '0',
  `parent_id` int DEFAULT NULL COMMENT 'Родитель',
  `sort` int NOT NULL DEFAULT '0' COMMENT 'Порядок',
  `type` varchar(20) NOT NULL DEFAULT '' COMMENT 'Тип',
  `slug` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL COMMENT 'Заголовок',
  `body` text COMMENT 'Текст',
  `img_list` varchar(255) DEFAULT NULL COMMENT 'Изоб-ние для списковой',
  `img` varchar(255) DEFAULT NULL,
  `isHideincatalog` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `city`
--

CREATE TABLE `city` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'Название',
  `phone` varchar(255) DEFAULT NULL COMMENT 'Телефон',
  `price_delivery` int DEFAULT NULL COMMENT 'Стоимость доставки',
  `pickup` text COMMENT 'Место самовывоза',
  `info_delivery` text COMMENT 'Информация о доставки',
  `isOnlyPickup` tinyint(1) DEFAULT '0' COMMENT 'Отключить всё кроме самовывоза',
  `pickup_switcher` tinyint(1) DEFAULT '0' COMMENT 'Самовывоз вкл/откл',
  `not_delete` tinyint(1) DEFAULT '0',
  `payment_type` varchar(255) DEFAULT NULL,
  `delivery_weight_sum` float DEFAULT NULL,
  `delivery_free_sum` float DEFAULT NULL,
  `coordinate` varchar(255) DEFAULT NULL,
  `pickup_price` varchar(255) DEFAULT NULL,
  `isYandexDelivery` tinyint(1) DEFAULT '0',
  `view` int NOT NULL DEFAULT '1' COMMENT 'Видимость',
  `min_order_amount_retail` int DEFAULT NULL COMMENT 'Минимальная сумма для розничного заказа',
  `min_order_amount_opt` int DEFAULT NULL COMMENT 'Минимальная сумма для оптового заказа'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `city`
--

INSERT INTO `city` (`id`, `name`, `phone`, `price_delivery`, `pickup`, `info_delivery`, `isOnlyPickup`, `pickup_switcher`, `not_delete`, `payment_type`, `delivery_weight_sum`, `delivery_free_sum`, `coordinate`, `pickup_price`, `isYandexDelivery`, `view`, `min_order_amount_retail`, `min_order_amount_opt`) VALUES
(1, 'Город', '+7 (999) 999 90 90', NULL, NULL, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, 0, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `cron`
--

CREATE TABLE `cron` (
  `id` int NOT NULL,
  `action` varchar(255) NOT NULL COMMENT 'Название действия',
  `params` text NOT NULL COMMENT 'Параметры'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `footer_menu`
--

CREATE TABLE `footer_menu` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'Название',
  `type` varchar(50) DEFAULT NULL COMMENT 'Тип',
  `owner_id` int DEFAULT NULL,
  `url` varchar(500) DEFAULT NULL COMMENT 'Ссылка',
  `isVisible` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Видимость',
  `sort` int NOT NULL DEFAULT '0' COMMENT 'Порядок',
  `parent_id` int DEFAULT NULL COMMENT 'Родитель'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `history_bonus`
--

CREATE TABLE `history_bonus` (
  `id` int NOT NULL,
  `user_id` int NOT NULL COMMENT 'Пользователь',
  `name` varchar(255) NOT NULL COMMENT 'Название пополнения',
  `sum` int NOT NULL COMMENT 'Сумма',
  `created_at` int NOT NULL COMMENT 'Дата создания'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `import_items`
--

CREATE TABLE `import_items` (
  `id` int NOT NULL,
  `data` text NOT NULL,
  `count_update` int NOT NULL,
  `date_created` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `items`
--

CREATE TABLE `items` (
  `id` int NOT NULL,
  `cid` int NOT NULL COMMENT 'Категория',
  `brand_id` int DEFAULT NULL COMMENT 'Бренд',
  `article` varchar(255) DEFAULT NULL COMMENT 'Артикул',
  `name` varchar(255) NOT NULL COMMENT 'Название',
  `body` text COMMENT 'Описание',
  `body_small` varchar(500) DEFAULT NULL,
  `feature` text COMMENT 'Характеристики',
  `storage` text COMMENT 'Условия хранения',
  `delivery` text COMMENT 'Доставка и оплата',
  `discount` int DEFAULT NULL COMMENT 'Скидка',
  `bonus_manager` decimal(13,3) NOT NULL DEFAULT '0.000' COMMENT 'Бонус менеджеру',
  `price` int NOT NULL COMMENT 'Цена',
  `old_price` int DEFAULT NULL COMMENT 'Старая цена',
  `purch_price` int NOT NULL COMMENT 'Закупочная цена',
  `wholesale_price` int DEFAULT NULL COMMENT 'Оптовая цена',
  `wholesale_price2` int DEFAULT NULL COMMENT 'Оптовая цена 2',
  `count` decimal(12,3) NOT NULL DEFAULT '0.000' COMMENT 'Количество',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Наличие. 0-нет в наличии/1-в наличии',
  `isVisible` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Видимость',
  `isDeleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Удалён',
  `isWholesale` tinyint(1) DEFAULT '0',
  `video` varchar(255) DEFAULT NULL COMMENT 'Видео',
  `img_list` varchar(255) DEFAULT NULL COMMENT 'Изображения для списковой',
  `isHit` tinyint(1) DEFAULT '0',
  `isNew` tinyint(1) DEFAULT '0',
  `googleFid` int DEFAULT NULL,
  `measure` tinyint(1) DEFAULT '0' COMMENT 'Измерения',
  `measure_price` tinyint(1) DEFAULT '0' COMMENT 'Вид расчёта',
  `weight` decimal(13,3) DEFAULT NULL,
  `popularity` int DEFAULT '0' COMMENT 'Популярность',
  `slug` varchar(255) DEFAULT NULL,
  `tags` varchar(80) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Структура таблицы `items_category`
--

CREATE TABLE `items_category` (
  `id` int NOT NULL,
  `item_id` int NOT NULL,
  `category_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `items_count`
--

CREATE TABLE `items_count` (
  `id` int NOT NULL,
  `item_id` int NOT NULL,
  `city_id` int NOT NULL,
  `count` decimal(12,3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `items_together`
--

CREATE TABLE `items_together` (
  `id` int NOT NULL,
  `item_main_id` int NOT NULL COMMENT 'К которому привязали',
  `item_id` int NOT NULL COMMENT 'Привязаный товар',
  `discount` varchar(255) DEFAULT NULL COMMENT 'Скидка/Цена',
  `count` decimal(13,3) NOT NULL DEFAULT '1.000'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `items_type_handling`
--

CREATE TABLE `items_type_handling` (
  `id` int NOT NULL,
  `item_id` int NOT NULL,
  `type_handling_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `item_accessory`
--

CREATE TABLE `item_accessory` (
  `id` int NOT NULL,
  `item_id_main` int NOT NULL,
  `item_id_accessory` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `item_associated`
--

CREATE TABLE `item_associated` (
  `id` int NOT NULL,
  `item_id_main` int NOT NULL,
  `item_id_sub` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `item_favorites`
--

CREATE TABLE `item_favorites` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `item_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `item_img`
--

CREATE TABLE `item_img` (
  `id` int NOT NULL,
  `item_id` int NOT NULL,
  `url` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL COMMENT 'Название',
  `sort` int NOT NULL DEFAULT '0' COMMENT 'Порядок'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `item_modifications`
--

CREATE TABLE `item_modifications` (
  `id` int NOT NULL,
  `item_main_id` int NOT NULL COMMENT 'Основной товар',
  `item_mod_id` int NOT NULL COMMENT 'Модификация товара'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `item_options_value`
--

CREATE TABLE `item_options_value` (
  `id` int NOT NULL,
  `item_id` int NOT NULL COMMENT 'Товар',
  `option_id` int NOT NULL COMMENT 'Характеристика',
  `option_value_id` int DEFAULT NULL COMMENT 'Значение фильтра из списка',
  `value` varchar(500) DEFAULT NULL COMMENT 'Значение фильтра'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `item_recommend`
--

CREATE TABLE `item_recommend` (
  `id` int NOT NULL,
  `item_main_id` int NOT NULL COMMENT 'Основной товар',
  `item_rec_id` int NOT NULL COMMENT 'Рекомендуемый товар'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `jobs`
--

CREATE TABLE `jobs` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'Название',
  `body` varchar(255) NOT NULL COMMENT 'Текст',
  `isVisible` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Видимость',
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `l_message`
--

CREATE TABLE `l_message` (
  `id` int NOT NULL DEFAULT '0',
  `language` varchar(16) NOT NULL DEFAULT '',
  `translation` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `l_source_message`
--

CREATE TABLE `l_source_message` (
  `id` int NOT NULL,
  `category` varchar(32) DEFAULT NULL,
  `message` text,
  `default` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `menu`
--

CREATE TABLE `menu` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'Название',
  `type` varchar(50) DEFAULT NULL COMMENT 'Тип',
  `owner_id` int DEFAULT NULL,
  `url` varchar(500) DEFAULT NULL COMMENT 'Ссылка',
  `isVisible` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Видимость',
  `sort` int NOT NULL DEFAULT '0' COMMENT 'Порядок',
  `parent_id` int DEFAULT NULL COMMENT 'Родитель'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 ROW_FORMAT=COMPACT;

--
-- Дамп данных таблицы `menu`
--

INSERT INTO `menu` (`id`, `name`, `type`, `owner_id`, `url`, `isVisible`, `sort`, `parent_id`) VALUES
(1, 'Оплата и доставка', 'module', 4, NULL, 1, 0, NULL),
(3, 'О нас', 'module', 5, NULL, 1, 1, NULL),
(4, 'Новости', 'module', 7, NULL, 1, 2, NULL),
(5, 'Оптовикам', 'page', 1, NULL, 1, 3, NULL),
(6, 'Контакты', 'module', 1, NULL, 1, 4, NULL),
(7, 'Бренды', 'module', 9, NULL, 0, 5, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `menu_category`
--

CREATE TABLE `menu_category` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'Название',
  `type` varchar(50) DEFAULT NULL COMMENT 'Тип',
  `owner_id` int DEFAULT NULL,
  `url` varchar(500) DEFAULT NULL COMMENT 'Ссылка',
  `isVisible` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Видимость',
  `sort` int NOT NULL DEFAULT '0' COMMENT 'Порядок',
  `parent_id` int DEFAULT NULL COMMENT 'Родитель',
  `img` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 ROW_FORMAT=COMPACT;

--
-- Дамп данных таблицы `menu_category`
--

INSERT INTO `menu_category` (`id`, `name`, `type`, `owner_id`, `url`, `isVisible`, `sort`, `parent_id`, `img`) VALUES
(1, 'Морепродукты', 'category', 9, NULL, 1, 0, NULL, '/uploads/menuCategory/1_img.jpg'),
(2, 'Сеты', 'module', 6, NULL, 0, 6, NULL, NULL),
(3, 'Рецепты', 'module', 2, NULL, 1, 7, NULL, NULL),
(4, 'Акции', 'module', 3, NULL, 1, 8, NULL, NULL),
(5, 'Рыба', 'category', 14, NULL, 1, 1, NULL, NULL),
(6, 'Бакалея', 'category', 40, NULL, 1, 5, NULL, NULL),
(7, 'Тест 3', 'category', 18, NULL, 0, 0, NULL, NULL),
(8, 'Икра', 'category', 39, NULL, 1, 2, NULL, NULL),
(10, 'Оливковое Италия', 'category', 49, NULL, 1, 6, NULL, NULL),
(12, 'Мясо', 'category', 82, NULL, 1, 4, NULL, NULL),
(14, 'Гастрономика', 'category', 101, NULL, 1, 5, NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `migration`
--

CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `migration`
--

INSERT INTO `migration` (`version`, `apply_time`) VALUES
('m130524_201442_init', 1423570418);

-- --------------------------------------------------------

--
-- Структура таблицы `module`
--

CREATE TABLE `module` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL COMMENT 'Название',
  `action` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `params` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT 'Дополнительные параметры',
  `path` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Дамп данных таблицы `module`
--

INSERT INTO `module` (`id`, `name`, `action`, `params`, `path`) VALUES
(1, 'Контакты', 'site/contacts', NULL, 'contacts'),
(2, 'Рецепты', 'site/recipes', NULL, 'recipes'),
(3, 'Акции', 'site/actions', NULL, 'actions'),
(4, 'Оплата и доставка', 'site/payment-delivery', NULL, 'payment-delivery'),
(5, 'О нас', 'site/about', NULL, 'about'),
(6, 'Сеты', 'site/sets', NULL, 'sets'),
(7, 'Новости', 'site/news', NULL, 'news'),
(8, 'Акции', 'site/actions', NULL, 'actions'),
(9, 'Бренды', 'site/brands', NULL, 'brands');

-- --------------------------------------------------------

--
-- Структура таблицы `monitoring_sms`
--

CREATE TABLE `monitoring_sms` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `created_at` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `musers`
--

CREATE TABLE `musers` (
  `id` int NOT NULL,
  `username` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `auth_key` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `password_reset_token` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `status` smallint NOT NULL DEFAULT '10',
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Структура таблицы `news`
--

CREATE TABLE `news` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'Название',
  `small_body` varchar(1000) DEFAULT NULL COMMENT 'Краткий текст',
  `body` text NOT NULL COMMENT 'Текст',
  `img` varchar(255) NOT NULL COMMENT 'Изображение',
  `created_at` int NOT NULL COMMENT 'Дата',
  `isVisible` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Видимость'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `options`
--

CREATE TABLE `options` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'Название',
  `type` varchar(50) NOT NULL DEFAULT 'multi_select' COMMENT 'Тип'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `options_category`
--

CREATE TABLE `options_category` (
  `id` int NOT NULL,
  `cid` int NOT NULL COMMENT 'Категория',
  `option_id` int NOT NULL COMMENT 'Характеристика',
  `isFilter` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Использовать как фильтр'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `options_value`
--

CREATE TABLE `options_value` (
  `id` int NOT NULL,
  `option_id` int NOT NULL,
  `value` varchar(500) NOT NULL COMMENT 'Значение'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL COMMENT 'Пользователь',
  `user_name` varchar(255) NOT NULL COMMENT 'Имя',
  `user_phone` varchar(255) NOT NULL COMMENT 'Телефон',
  `user_mail` varchar(255) DEFAULT NULL COMMENT 'E-Mail',
  `user_address` varchar(500) NOT NULL COMMENT 'Адрес',
  `user_comments` text COMMENT 'Комментарий пользователя',
  `city_id` int DEFAULT NULL,
  `isEntity` tinyint(1) NOT NULL DEFAULT '0',
  `date_delivery` int NOT NULL COMMENT 'Дата доставки',
  `time_delivery` varchar(255) NOT NULL COMMENT 'Время доставки',
  `code` varchar(255) DEFAULT NULL COMMENT 'Промо код',
  `full_price` int NOT NULL COMMENT 'Сумма заказа',
  `full_purch_price` int NOT NULL,
  `discount` varchar(255) DEFAULT NULL COMMENT 'Скидка',
  `price_delivery` int DEFAULT '0',
  `payment` varchar(50) NOT NULL COMMENT 'Способ оплаты',
  `bonus_use` int NOT NULL DEFAULT '0' COMMENT 'Используемые бонусы',
  `bonus_add` int NOT NULL DEFAULT '0' COMMENT 'Бонус за заказ',
  `bonus_manager` decimal(13,3) DEFAULT NULL,
  `bonus_driver` int DEFAULT NULL COMMENT 'Бонус водителя',
  `status` tinyint NOT NULL DEFAULT '0' COMMENT 'Статус',
  `pay_status` varchar(255) DEFAULT NULL,
  `manager_id` int DEFAULT NULL COMMENT 'Менеджер',
  `driver_id` int DEFAULT NULL COMMENT 'Курьер',
  `collector_id` int DEFAULT NULL COMMENT 'Сборщик',
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL,
  `isFast` tinyint(1) NOT NULL DEFAULT '0',
  `isWholesale` tinyint(1) DEFAULT '0' COMMENT 'Оптовик',
  `id_1c` varchar(255) DEFAULT NULL COMMENT 'Номер накладной',
  `enable_bonus` tinyint(1) DEFAULT '1',
  `promo_code_id` int DEFAULT NULL COMMENT 'Промокод',
  `admin_comments` text COMMENT 'Ком-рий админ, мен-жер',
  `isPhoneOrder` tinyint(1) DEFAULT '0',
  `pickpoint_id` int DEFAULT NULL,
  `coordinates_json_yandex` varchar(255) DEFAULT NULL,
  `delivery_method` tinyint(1) NOT NULL DEFAULT '0',
  `invoice_file` varchar(255) DEFAULT NULL COMMENT 'Файл накладной',
  `claim_id` varchar(255) DEFAULT NULL,
  `version_edit` int NOT NULL DEFAULT '1',
  `isDeadline` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Уведомление в телеграмм',
  `hand_link` varchar(255) DEFAULT NULL,
  `isApp` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `orders_comments`
--

CREATE TABLE `orders_comments` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_id` int DEFAULT NULL,
  `body` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `orders_history`
--

CREATE TABLE `orders_history` (
  `id` int NOT NULL,
  `order_id` int NOT NULL COMMENT 'Заказ',
  `user_id` int DEFAULT NULL COMMENT 'Пользователь',
  `user_name` varchar(255) DEFAULT NULL COMMENT 'ФИО',
  `action` tinyint(1) NOT NULL COMMENT 'Действие',
  `created_at` int NOT NULL COMMENT 'Дата создания',
  `updated_at` int NOT NULL,
  `claim_id` varchar(255) DEFAULT NULL COMMENT 'id яндекс заявки'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `orders_items`
--

CREATE TABLE `orders_items` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `item_id` int NOT NULL,
  `count` decimal(12,3) NOT NULL,
  `weight` decimal(12,3) DEFAULT NULL,
  `price` int NOT NULL,
  `purch_price` int NOT NULL,
  `bonus_manager` decimal(13,3) NOT NULL DEFAULT '0.000',
  `data` text COMMENT 'Данные модели'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `orders_items_handing`
--

CREATE TABLE `orders_items_handing` (
  `id` int NOT NULL,
  `orders_items_id` int NOT NULL,
  `type_handling_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `orders_pay`
--

CREATE TABLE `orders_pay` (
  `id` bigint NOT NULL,
  `order_id` int NOT NULL,
  `amount` decimal(13,4) NOT NULL,
  `real_amount` decimal(13,4) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `orders_rollback_items`
--

CREATE TABLE `orders_rollback_items` (
  `id` int NOT NULL,
  `order_id` int NOT NULL COMMENT 'Заказ',
  `item_order_id` int NOT NULL COMMENT 'Товар',
  `count` decimal(12,3) NOT NULL COMMENT 'Количество',
  `weight` decimal(12,3) DEFAULT NULL COMMENT 'Вес'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `orders_rollback_sets`
--

CREATE TABLE `orders_rollback_sets` (
  `id` int NOT NULL,
  `order_id` int NOT NULL COMMENT 'Заказ',
  `set_order_id` int NOT NULL COMMENT 'Товар',
  `count` decimal(12,3) NOT NULL COMMENT 'Количество'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Структура таблицы `orders_sets`
--

CREATE TABLE `orders_sets` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `set_id` int NOT NULL,
  `count` int NOT NULL,
  `price` int NOT NULL,
  `purch_price` int NOT NULL,
  `bonus_manager` decimal(13,3) NOT NULL DEFAULT '0.000',
  `date_items` text COMMENT 'Товары'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `orders_unloading`
--

CREATE TABLE `orders_unloading` (
  `id` int NOT NULL,
  `order_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `pages`
--

CREATE TABLE `pages` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'Заголовок',
  `body` text NOT NULL COMMENT 'Текст',
  `isVisible` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Видимость',
  `not_delete` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `pages`
--

INSERT INTO `pages` (`id`, `name`, `body`, `isVisible`, `not_delete`) VALUES
(1, 'Оптовикам', '<p>Просто текст</p>\r\n', 1, 0),
(3, 'Спасибо за {покупку}', '<p>Спасибо&nbsp;ваш заказ №{order_number}&nbsp;оформлен, в течение 30 минут с вами свяжется наш менеджер</p>\r\n<script>\r\n    (function (i, s, o, g, r, a, m) {i[\'GoogleAnalyticsObject\'] = r;\r\n        i[r] = i[r] || function () {(i[r].q = i[r].q || []).push(arguments)}, i[r].l = 1 * new Date();\r\n        a = s.createElement(o), m = s.getElementsByTagName(o)[0];a.async = 1; a.src = g;\r\n        m.parentNode.insertBefore(a, m)\r\n    })(window, document, \'script\', \'https://www.google-analytics.com/analytics.js\', \'ga\');\r\n    ga(\'create\', \'UA-30657669-1\', \'auto\');\r\n    function getRetailCrmCookie(name) {\r\n        var matches = document.cookie.match(new RegExp(\'(?:^|; )\' + name + \'=([^;]*)\'));\r\n        return matches ? decodeURIComponent(matches[1]) : \'\';}\r\n\r\n    ga(\'set\', \'dimension1\', getRetailCrmCookie(\'_ga\'));\r\n    ga(\'send\', \'pageview\');\r\n\r\n    ga(\'require\', \'ecommerce\', \'ecommerce.js\');\r\n    ga(\'ecommerce:addTransaction\', {\'id\': \'{order_number}\'});\r\n    ga(\'ecommerce:send\');\r\n</script>', 1, 1),
(4, 'О нас', '<p>Текст содержимого</p>\r\n', 1, 1),
(5, 'Оплата и доставка', '<h2>Текст</h2>\r\n', 1, 1),
(6, 'Промокод', '<p>Некий текст страницы</p>\r\n', 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `pickpoint`
--

CREATE TABLE `pickpoint` (
  `id` int NOT NULL,
  `active` tinyint(1) DEFAULT '1',
  `name` varchar(255) NOT NULL,
  `desc` varchar(255) DEFAULT NULL,
  `coordinate` varchar(255) DEFAULT NULL,
  `created_at` int DEFAULT NULL,
  `updated_at` int DEFAULT NULL,
  `city_id` int DEFAULT NULL,
  `time_work` varchar(255) DEFAULT NULL,
  `phones` varchar(255) DEFAULT NULL,
  `images` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `pickpoints_users`
--

CREATE TABLE `pickpoints_users` (
  `id` int NOT NULL,
  `pickpoint_id` int NOT NULL,
  `user_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `pickpoint_img`
--

CREATE TABLE `pickpoint_img` (
  `id` int NOT NULL,
  `pickpoint_id` int NOT NULL,
  `url` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL COMMENT 'Название',
  `sort` int NOT NULL DEFAULT '0' COMMENT 'Порядок'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `promo_code`
--

CREATE TABLE `promo_code` (
  `id` int NOT NULL,
  `code` varchar(255) NOT NULL COMMENT 'Код',
  `discount` varchar(255) NOT NULL,
  `min_amount` varchar(20) DEFAULT NULL COMMENT 'Минимальная сумма заказа для активации промокода',
  `body` text COMMENT 'Текст для писем',
  `isEnable` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Включён',
  `date_start` int NOT NULL,
  `date_end` int NOT NULL COMMENT 'Дата окончания',
  `type` varchar(50) NOT NULL COMMENT 'Вид',
  `device` varchar(50) NOT NULL DEFAULT 'web' COMMENT 'Вид устройства, для которого задаётся промокод (android, ios, web и тд)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `recipes`
--

CREATE TABLE `recipes` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'Название',
  `time_cooking` varchar(255) DEFAULT NULL COMMENT 'Время приготовления',
  `description_time_cooking` text COMMENT 'Описание время приготовления',
  `small_body` varchar(500) DEFAULT NULL COMMENT 'Краткое описание',
  `img_list` varchar(255) DEFAULT NULL COMMENT 'Изображения для списковой',
  `isVisible` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Видимость',
  `toMain` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1вкл/0выкл вывести рецепт в слайдер на главную',
  `isDay` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Рецепт дня',
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `recipes_img`
--

CREATE TABLE `recipes_img` (
  `id` int NOT NULL,
  `id_recipes` int NOT NULL,
  `url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `recipes_item`
--

CREATE TABLE `recipes_item` (
  `id` int NOT NULL,
  `recipe_id` int NOT NULL COMMENT 'Рецепт',
  `name` varchar(255) NOT NULL COMMENT 'Название',
  `count` varchar(255) DEFAULT NULL COMMENT 'Количество',
  `item_id` int DEFAULT NULL COMMENT 'Товар',
  `item_count` decimal(13,3) DEFAULT NULL COMMENT 'Количество товара'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `recipes_method`
--

CREATE TABLE `recipes_method` (
  `id` int NOT NULL,
  `recipe_id` int NOT NULL,
  `name` varchar(255) DEFAULT NULL COMMENT 'Заголовок',
  `body` text NOT NULL,
  `img` varchar(255) DEFAULT NULL,
  `sort` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `reviews`
--

CREATE TABLE `reviews` (
  `id` int NOT NULL,
  `rate` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Оценка',
  `name` varchar(255) NOT NULL COMMENT 'Имя',
  `plus_body` text COMMENT 'Достоинства',
  `minus_body` text COMMENT 'Недостатки',
  `body` text NOT NULL COMMENT 'Комментарий',
  `isVisible` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Видимость',
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `reviews_item`
--

CREATE TABLE `reviews_item` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `item_id` int NOT NULL COMMENT 'Товар',
  `rate` tinyint(1) NOT NULL DEFAULT '5' COMMENT 'Оценка',
  `name` varchar(255) NOT NULL COMMENT 'Имя',
  `body` varchar(1000) NOT NULL COMMENT 'Отзыв',
  `isVisible` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Видимость',
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Структура таблицы `seo`
--

CREATE TABLE `seo` (
  `id` int NOT NULL,
  `type` varchar(50) NOT NULL,
  `owner_id` int NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `keywords` varchar(500) DEFAULT NULL,
  `title` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Структура таблицы `seo_lang`
--

CREATE TABLE `seo_lang` (
  `id` int NOT NULL,
  `owner_id` int NOT NULL,
  `lang_id` varchar(6) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `keywords` varchar(500) DEFAULT NULL,
  `title` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `sets`
--

CREATE TABLE `sets` (
  `id` int NOT NULL,
  `name` varchar(255) DEFAULT NULL COMMENT 'Название',
  `body` text COMMENT 'Описание',
  `img` varchar(255) DEFAULT NULL COMMENT 'Изображение',
  `discount` decimal(12,1) DEFAULT NULL COMMENT 'Скидка %',
  `bonus_manager` decimal(12,3) NOT NULL DEFAULT '0.000' COMMENT 'Бонус менеджеру',
  `price` int DEFAULT NULL COMMENT 'Стоимость',
  `price_purch` int DEFAULT NULL,
  `price_sale` int DEFAULT NULL COMMENT 'Экономия',
  `isVisible` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Видимость',
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `sets_items`
--

CREATE TABLE `sets_items` (
  `id` int NOT NULL,
  `set_id` int NOT NULL COMMENT 'Сет',
  `item_id` int NOT NULL COMMENT 'Товар',
  `price` int DEFAULT NULL COMMENT 'Цена',
  `count` decimal(12,3) NOT NULL DEFAULT '1.000' COMMENT 'Количество в сете'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `spec_actions`
--

CREATE TABLE `spec_actions` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `date_start` int NOT NULL,
  `date_end` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `spec_action_codes`
--

CREATE TABLE `spec_action_codes` (
  `id` int NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `spec_action_id` int NOT NULL,
  `item_id` int DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `count` tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `spec_action_phones`
--

CREATE TABLE `spec_action_phones` (
  `id` int NOT NULL,
  `spec_action_code_id` int NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `send_time` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `structure`
--

CREATE TABLE `structure` (
  `id` int NOT NULL,
  `url` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `parent` int DEFAULT NULL,
  `order` int NOT NULL DEFAULT '0',
  `template` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `id_template` int NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `seo` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `meta_tag` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` int NOT NULL,
  `email` varchar(255) NOT NULL,
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `s_auth`
--

CREATE TABLE `s_auth` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `source` varchar(255) NOT NULL,
  `source_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Структура таблицы `s_history_money`
--

CREATE TABLE `s_history_money` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `sum_order` int NOT NULL COMMENT 'Сумма заказа',
  `sum_purch` int NOT NULL,
  `sum_bonus` int NOT NULL COMMENT 'Сумма бонуса',
  `date_created` int NOT NULL COMMENT 'Дата создания'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `s_log_action`
--

CREATE TABLE `s_log_action` (
  `id` int NOT NULL,
  `action` varchar(500) NOT NULL,
  `data` text NOT NULL,
  `time` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `s_seo_redirects`
--

CREATE TABLE `s_seo_redirects` (
  `id` int NOT NULL,
  `old_url` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL COMMENT 'Старая ссылка',
  `new_url` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL COMMENT 'Новая ссылка',
  `type` varchar(3) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '301' COMMENT 'Вид редиректа',
  `isRegex` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Регулярное выражение',
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Дамп данных таблицы `s_seo_redirects`
--

INSERT INTO `s_seo_redirects` (`id`, `old_url`, `new_url`, `type`, `isRegex`, `created_at`, `updated_at`) VALUES
(1, 'page.html/?id=6', 'promo-code', '301', 0, 1686259514, 1686259514),
(2, 'page.html/?id=1', 'optovikam', '301', 0, 1686259535, 1686259535),
(3, 'page.html/?id=5', 'oplata-dostavka', '301', 0, 1686259557, 1686259557),
(4, 'page.html/?id=4', 'about', '301', 0, 1686259572, 1686259572),
(5, 'page.html/?id=3', 'thanks-for-purchase', '301', 0, 1686259618, 1686259618);

-- --------------------------------------------------------

--
-- Структура таблицы `s_seo_urls`
--

CREATE TABLE `s_seo_urls` (
  `id` int NOT NULL,
  `resource` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL COMMENT 'Namespace модели',
  `resource_id` int NOT NULL COMMENT 'Pk модели',
  `controller` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL COMMENT 'Контроллер',
  `action` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL COMMENT 'Action',
  `path` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL COMMENT 'Полный ЧПУ',
  `url` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL COMMENT 'ЧПУ',
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Дамп данных таблицы `s_seo_urls`
--

INSERT INTO `s_seo_urls` (`id`, `resource`, `resource_id`, `controller`, `action`, `path`, `url`, `created_at`, `updated_at`) VALUES
(1, 'backend\\models\\Pages', 6, 'site', 'page', 'promo-code', 'promo-code', 1686259514, 1686259514),
(2, 'backend\\models\\Pages', 1, 'site', 'page', 'optovikam', 'optovikam', 1686259535, 1686259535),
(3, 'backend\\models\\Pages', 5, 'site', 'page', 'oplata-dostavka', 'oplata-dostavka', 1686259557, 1686259557),
(4, 'backend\\models\\Pages', 4, 'site', 'page', 'about', 'about', 1686259572, 1686259572),
(5, 'backend\\models\\Pages', 3, 'site', 'page', 'thanks-for-purchase', 'thanks-for-purchase', 1686259618, 1686259618);

-- --------------------------------------------------------

--
-- Структура таблицы `s_settings`
--

CREATE TABLE `s_settings` (
  `id` int NOT NULL,
  `group` varchar(50) NOT NULL,
  `key` varchar(50) NOT NULL,
  `value` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `s_user`
--

CREATE TABLE `s_user` (
  `id` int NOT NULL,
  `username` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `auth_key` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `password_reset_token` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `status` smallint NOT NULL DEFAULT '10',
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL COMMENT 'Роль',
  `role` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `img` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `salary` int DEFAULT NULL,
  `bonus_delivery` int DEFAULT NULL COMMENT 'Бонус за доставку',
  `withYandexWork` tinyint(1) NOT NULL DEFAULT '0',
  `withYandexWorkAndAll` tinyint(1) NOT NULL DEFAULT '0',
  `withOrdersOutPickpoints` tinyint(1) NOT NULL DEFAULT '0',
  `withIsWholesale` int DEFAULT '0' COMMENT 'Видит только оптовые заказы'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Дамп данных таблицы `s_user`
--

INSERT INTO `s_user` (`id`, `username`, `auth_key`, `password_hash`, `password_reset_token`, `email`, `status`, `created_at`, `updated_at`, `role`, `phone`, `img`, `salary`, `bonus_delivery`, `withYandexWork`, `withYandexWorkAndAll`, `withOrdersOutPickpoints`, `withIsWholesale`) VALUES
(1, 'webmaster', '36rR8cB_q0IgMyozpb_cFGLy_4D_PkEd', '$2y$13$ON.bDhcEfYmi0XOHXX34HOoivnqg7IMbeSaD15bvlkf5LcIgRvQpe', NULL, 'webmaster@mail.ru', 10, 1423570455, 1423570455, 'admin', NULL, NULL, NULL, NULL, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `s_user_plan`
--

CREATE TABLE `s_user_plan` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL COMMENT 'Менеджер',
  `sum` int NOT NULL COMMENT 'Сумма плана',
  `date_start` int NOT NULL COMMENT 'Дата начала',
  `date_end` int NOT NULL COMMENT 'Дата окончания'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `template`
--

CREATE TABLE `template` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `unique_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL,
  `noDeleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `type_handling`
--

CREATE TABLE `type_handling` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'Название',
  `isVisible` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Видимость',
  `img` varchar(255) DEFAULT NULL COMMENT 'Изображение'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `auth_key` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password_reset_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` smallint NOT NULL DEFAULT '10',
  `isEntity` tinyint NOT NULL DEFAULT '0',
  `isWholesale` tinyint(1) NOT NULL DEFAULT '0',
  `bonus` int DEFAULT '0',
  `data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `sex` tinyint(1) DEFAULT '0' COMMENT 'Пол',
  `dob` int DEFAULT NULL COMMENT 'Дата рождения',
  `isSubscription` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Подписка на рассылку',
  `isNotification` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Подписка на уведомления',
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL,
  `code` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `order_sum` int DEFAULT '0',
  `discount` decimal(13,2) DEFAULT NULL,
  `manager_id` int DEFAULT NULL,
  `opt_user_id` int DEFAULT NULL COMMENT 'ID оптового пользователя с этой же таблицы.',
  `city_id` int DEFAULT NULL,
  `count_sms` int NOT NULL DEFAULT '0',
  `date_last_sms` int NOT NULL DEFAULT '0',
  `photo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `settings` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci COMMENT 'Для разных скрытых второстепенных настроек пользователя.',
  `personal_discount` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `payment_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Индивидуальные спсобы оплаты для оптовика'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `user_address`
--

CREATE TABLE `user_address` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `city` int NOT NULL DEFAULT '1' COMMENT 'Город',
  `street` varchar(500) NOT NULL COMMENT 'Улица',
  `home` varchar(255) NOT NULL COMMENT 'Дом',
  `house` varchar(255) DEFAULT NULL COMMENT 'Кв',
  `phone` varchar(255) NOT NULL COMMENT 'Телефон',
  `isMain` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Основной',
  `name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `user_deleted`
--

CREATE TABLE `user_deleted` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(100) DEFAULT NULL,
  `sex` int NOT NULL DEFAULT '0',
  `deleted_at` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COMMENT='Таблица удалённых пользователей';

-- --------------------------------------------------------

--
-- Структура таблицы `user_invited`
--

CREATE TABLE `user_invited` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `user_invited` int DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `about_history`
--
ALTER TABLE `about_history`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `actions`
--
ALTER TABLE `actions`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `actions_items`
--
ALTER TABLE `actions_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `action_id` (`action_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Индексы таблицы `auth`
--
ALTER TABLE `auth`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `banners_cities`
--
ALTER TABLE `banners_cities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `banner_id` (`banner_id`,`city_id`),
  ADD KEY `banner_id_2` (`banner_id`,`city_id`),
  ADD KEY `city_id` (`city_id`);

--
-- Индексы таблицы `bonus_settings`
--
ALTER TABLE `bonus_settings`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `callback`
--
ALTER TABLE `callback`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `cards`
--
ALTER TABLE `cards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Индексы таблицы `city`
--
ALTER TABLE `city`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `cron`
--
ALTER TABLE `cron`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `footer_menu`
--
ALTER TABLE `footer_menu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Индексы таблицы `history_bonus`
--
ALTER TABLE `history_bonus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `import_items`
--
ALTER TABLE `import_items`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cid` (`cid`) USING BTREE,
  ADD KEY `brand_id` (`brand_id`) USING BTREE;

--
-- Индексы таблицы `items_category`
--
ALTER TABLE `items_category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Индексы таблицы `items_count`
--
ALTER TABLE `items_count`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `city_id` (`city_id`);

--
-- Индексы таблицы `items_together`
--
ALTER TABLE `items_together`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_main_id` (`item_main_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Индексы таблицы `items_type_handling`
--
ALTER TABLE `items_type_handling`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `type_handling_id` (`type_handling_id`);

--
-- Индексы таблицы `item_accessory`
--
ALTER TABLE `item_accessory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id_main` (`item_id_main`),
  ADD KEY `item_id_accessory` (`item_id_accessory`);

--
-- Индексы таблицы `item_associated`
--
ALTER TABLE `item_associated`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id_main` (`item_id_main`),
  ADD KEY `item_id_sub` (`item_id_sub`);

--
-- Индексы таблицы `item_favorites`
--
ALTER TABLE `item_favorites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Индексы таблицы `item_img`
--
ALTER TABLE `item_img`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`);

--
-- Индексы таблицы `item_modifications`
--
ALTER TABLE `item_modifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_main_id` (`item_main_id`),
  ADD KEY `item_mod_id` (`item_mod_id`);

--
-- Индексы таблицы `item_options_value`
--
ALTER TABLE `item_options_value`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `option_id` (`option_id`),
  ADD KEY `option_value_id` (`option_value_id`);

--
-- Индексы таблицы `item_recommend`
--
ALTER TABLE `item_recommend`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_recommend_ibfk_1` (`item_main_id`),
  ADD KEY `item_recommend_ibfk_2` (`item_rec_id`);

--
-- Индексы таблицы `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `l_message`
--
ALTER TABLE `l_message`
  ADD PRIMARY KEY (`id`,`language`);

--
-- Индексы таблицы `l_source_message`
--
ALTER TABLE `l_source_message`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`) USING BTREE;

--
-- Индексы таблицы `menu_category`
--
ALTER TABLE `menu_category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`) USING BTREE;

--
-- Индексы таблицы `migration`
--
ALTER TABLE `migration`
  ADD PRIMARY KEY (`version`);

--
-- Индексы таблицы `module`
--
ALTER TABLE `module`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `monitoring_sms`
--
ALTER TABLE `monitoring_sms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `user_id_2` (`user_id`);

--
-- Индексы таблицы `musers`
--
ALTER TABLE `musers`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `options_category`
--
ALTER TABLE `options_category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cid` (`cid`),
  ADD KEY `option_id` (`option_id`);

--
-- Индексы таблицы `options_value`
--
ALTER TABLE `options_value`
  ADD PRIMARY KEY (`id`),
  ADD KEY `option_id` (`option_id`);

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `promo_code_id` (`promo_code_id`),
  ADD KEY `id` (`id`),
  ADD KEY `user_name` (`user_name`),
  ADD KEY `user_phone` (`user_phone`),
  ADD KEY `user_mail` (`user_mail`),
  ADD KEY `user_address` (`user_address`),
  ADD KEY `Induser` (`user_id`),
  ADD KEY `Indstatus` (`status`);

--
-- Индексы таблицы `orders_comments`
--
ALTER TABLE `orders_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Индексы таблицы `orders_history`
--
ALTER TABLE `orders_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`) USING BTREE,
  ADD KEY `user_id` (`user_id`) USING BTREE;

--
-- Индексы таблицы `orders_items`
--
ALTER TABLE `orders_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Индексы таблицы `orders_items_handing`
--
ALTER TABLE `orders_items_handing`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orders_items_id` (`orders_items_id`),
  ADD KEY `type_handling_id` (`type_handling_id`);

--
-- Индексы таблицы `orders_pay`
--
ALTER TABLE `orders_pay`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Индексы таблицы `orders_rollback_items`
--
ALTER TABLE `orders_rollback_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `item_order_id` (`item_order_id`);

--
-- Индексы таблицы `orders_rollback_sets`
--
ALTER TABLE `orders_rollback_sets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`) USING BTREE,
  ADD KEY `set_order_id` (`set_order_id`) USING BTREE;

--
-- Индексы таблицы `orders_sets`
--
ALTER TABLE `orders_sets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `set_id` (`set_id`);

--
-- Индексы таблицы `orders_unloading`
--
ALTER TABLE `orders_unloading`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Индексы таблицы `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `pickpoint`
--
ALTER TABLE `pickpoint`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk-pickpoint-city_id` (`city_id`);

--
-- Индексы таблицы `pickpoints_users`
--
ALTER TABLE `pickpoints_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pickpoint_id` (`pickpoint_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `pickpoint_img`
--
ALTER TABLE `pickpoint_img`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pickpoint_id` (`pickpoint_id`);

--
-- Индексы таблицы `promo_code`
--
ALTER TABLE `promo_code`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `recipes_img`
--
ALTER TABLE `recipes_img`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_recipes` (`id_recipes`);

--
-- Индексы таблицы `recipes_item`
--
ALTER TABLE `recipes_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Индексы таблицы `recipes_method`
--
ALTER TABLE `recipes_method`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Индексы таблицы `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `reviews_item`
--
ALTER TABLE `reviews_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `seo`
--
ALTER TABLE `seo`
  ADD PRIMARY KEY (`id`,`type`,`owner_id`);

--
-- Индексы таблицы `seo_lang`
--
ALTER TABLE `seo_lang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `owner_id` (`owner_id`) USING BTREE,
  ADD KEY `lang_id` (`lang_id`) USING BTREE;

--
-- Индексы таблицы `sets`
--
ALTER TABLE `sets`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `sets_items`
--
ALTER TABLE `sets_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `set_id` (`set_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Индексы таблицы `spec_actions`
--
ALTER TABLE `spec_actions`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `spec_action_codes`
--
ALTER TABLE `spec_action_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `spec_action_id` (`spec_action_id`);

--
-- Индексы таблицы `spec_action_phones`
--
ALTER TABLE `spec_action_phones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `spec_action_code_id` (`spec_action_code_id`);

--
-- Индексы таблицы `structure`
--
ALTER TABLE `structure`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_structure_parent` (`parent`),
  ADD KEY `id_template` (`id_template`);

--
-- Индексы таблицы `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `s_auth`
--
ALTER TABLE `s_auth`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`) USING BTREE;

--
-- Индексы таблицы `s_history_money`
--
ALTER TABLE `s_history_money`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `s_log_action`
--
ALTER TABLE `s_log_action`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `s_seo_redirects`
--
ALTER TABLE `s_seo_redirects`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `idx-s_seo_redirects-old_url` (`old_url`) USING BTREE;

--
-- Индексы таблицы `s_seo_urls`
--
ALTER TABLE `s_seo_urls`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `idx-s_seo_urls-path` (`path`) USING BTREE,
  ADD KEY `idx-s_seo_urls-resource_id` (`resource_id`) USING BTREE,
  ADD KEY `idx-s_seo_urls-controller` (`controller`) USING BTREE,
  ADD KEY `idx-s_seo_urls-action` (`action`) USING BTREE;

--
-- Индексы таблицы `s_settings`
--
ALTER TABLE `s_settings`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `s_user`
--
ALTER TABLE `s_user`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `s_user_plan`
--
ALTER TABLE `s_user_plan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `template`
--
ALTER TABLE `template`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `type_handling`
--
ALTER TABLE `type_handling`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `user_address`
--
ALTER TABLE `user_address`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `city` (`city`);

--
-- Индексы таблицы `user_deleted`
--
ALTER TABLE `user_deleted`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `user_invited`
--
ALTER TABLE `user_invited`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `user_invited` (`user_invited`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `about_history`
--
ALTER TABLE `about_history`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `actions`
--
ALTER TABLE `actions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `actions_items`
--
ALTER TABLE `actions_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `auth`
--
ALTER TABLE `auth`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `banners`
--
ALTER TABLE `banners`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `banners_cities`
--
ALTER TABLE `banners_cities`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `bonus_settings`
--
ALTER TABLE `bonus_settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `callback`
--
ALTER TABLE `callback`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `cards`
--
ALTER TABLE `cards`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `category`
--
ALTER TABLE `category`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `city`
--
ALTER TABLE `city`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `cron`
--
ALTER TABLE `cron`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `footer_menu`
--
ALTER TABLE `footer_menu`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `history_bonus`
--
ALTER TABLE `history_bonus`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `import_items`
--
ALTER TABLE `import_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `items`
--
ALTER TABLE `items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `items_category`
--
ALTER TABLE `items_category`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `items_count`
--
ALTER TABLE `items_count`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `items_together`
--
ALTER TABLE `items_together`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `items_type_handling`
--
ALTER TABLE `items_type_handling`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `item_accessory`
--
ALTER TABLE `item_accessory`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `item_associated`
--
ALTER TABLE `item_associated`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `item_favorites`
--
ALTER TABLE `item_favorites`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `item_img`
--
ALTER TABLE `item_img`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `item_modifications`
--
ALTER TABLE `item_modifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `item_options_value`
--
ALTER TABLE `item_options_value`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `item_recommend`
--
ALTER TABLE `item_recommend`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `l_source_message`
--
ALTER TABLE `l_source_message`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблицы `menu_category`
--
ALTER TABLE `menu_category`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT для таблицы `module`
--
ALTER TABLE `module`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `monitoring_sms`
--
ALTER TABLE `monitoring_sms`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `musers`
--
ALTER TABLE `musers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `news`
--
ALTER TABLE `news`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `options`
--
ALTER TABLE `options`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `options_category`
--
ALTER TABLE `options_category`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `options_value`
--
ALTER TABLE `options_value`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orders_comments`
--
ALTER TABLE `orders_comments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orders_history`
--
ALTER TABLE `orders_history`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orders_items`
--
ALTER TABLE `orders_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orders_items_handing`
--
ALTER TABLE `orders_items_handing`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orders_rollback_items`
--
ALTER TABLE `orders_rollback_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orders_rollback_sets`
--
ALTER TABLE `orders_rollback_sets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orders_sets`
--
ALTER TABLE `orders_sets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orders_unloading`
--
ALTER TABLE `orders_unloading`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT для таблицы `pickpoint`
--
ALTER TABLE `pickpoint`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `pickpoints_users`
--
ALTER TABLE `pickpoints_users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `pickpoint_img`
--
ALTER TABLE `pickpoint_img`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `promo_code`
--
ALTER TABLE `promo_code`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `recipes`
--
ALTER TABLE `recipes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `recipes_img`
--
ALTER TABLE `recipes_img`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `recipes_item`
--
ALTER TABLE `recipes_item`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `recipes_method`
--
ALTER TABLE `recipes_method`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `reviews_item`
--
ALTER TABLE `reviews_item`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `seo`
--
ALTER TABLE `seo`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `seo_lang`
--
ALTER TABLE `seo_lang`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `sets`
--
ALTER TABLE `sets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `sets_items`
--
ALTER TABLE `sets_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `spec_actions`
--
ALTER TABLE `spec_actions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `spec_action_codes`
--
ALTER TABLE `spec_action_codes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `spec_action_phones`
--
ALTER TABLE `spec_action_phones`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `structure`
--
ALTER TABLE `structure`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `s_auth`
--
ALTER TABLE `s_auth`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `s_history_money`
--
ALTER TABLE `s_history_money`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `s_log_action`
--
ALTER TABLE `s_log_action`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `s_seo_redirects`
--
ALTER TABLE `s_seo_redirects`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `s_seo_urls`
--
ALTER TABLE `s_seo_urls`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `s_settings`
--
ALTER TABLE `s_settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `s_user`
--
ALTER TABLE `s_user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `s_user_plan`
--
ALTER TABLE `s_user_plan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `template`
--
ALTER TABLE `template`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `type_handling`
--
ALTER TABLE `type_handling`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `user_address`
--
ALTER TABLE `user_address`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `user_deleted`
--
ALTER TABLE `user_deleted`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `user_invited`
--
ALTER TABLE `user_invited`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `actions_items`
--
ALTER TABLE `actions_items`
  ADD CONSTRAINT `actions_items_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `actions_items_ibfk_2` FOREIGN KEY (`action_id`) REFERENCES `actions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `auth`
--
ALTER TABLE `auth`
  ADD CONSTRAINT `auth_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `banners_cities`
--
ALTER TABLE `banners_cities`
  ADD CONSTRAINT `banners_cities_ibfk_1` FOREIGN KEY (`banner_id`) REFERENCES `banners` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `banners_cities_ibfk_2` FOREIGN KEY (`city_id`) REFERENCES `city` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `cards`
--
ALTER TABLE `cards`
  ADD CONSTRAINT `cards_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `category`
--
ALTER TABLE `category`
  ADD CONSTRAINT `category_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `category` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Ограничения внешнего ключа таблицы `footer_menu`
--
ALTER TABLE `footer_menu`
  ADD CONSTRAINT `footer_menu_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `footer_menu` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `history_bonus`
--
ALTER TABLE `history_bonus`
  ADD CONSTRAINT `history_bonus_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `items_ibfk_1` FOREIGN KEY (`cid`) REFERENCES `category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `items_ibfk_2` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `items_category`
--
ALTER TABLE `items_category`
  ADD CONSTRAINT `items_category_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `items_category_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `items_count`
--
ALTER TABLE `items_count`
  ADD CONSTRAINT `items_count_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `items_count_ibfk_2` FOREIGN KEY (`city_id`) REFERENCES `city` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `items_together`
--
ALTER TABLE `items_together`
  ADD CONSTRAINT `items_together_ibfk_1` FOREIGN KEY (`item_main_id`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `items_together_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`);

--
-- Ограничения внешнего ключа таблицы `items_type_handling`
--
ALTER TABLE `items_type_handling`
  ADD CONSTRAINT `items_type_handling_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `items_type_handling_ibfk_2` FOREIGN KEY (`type_handling_id`) REFERENCES `type_handling` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `item_associated`
--
ALTER TABLE `item_associated`
  ADD CONSTRAINT `item_associated_ibfk_1` FOREIGN KEY (`item_id_main`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `item_associated_ibfk_2` FOREIGN KEY (`item_id_sub`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `item_favorites`
--
ALTER TABLE `item_favorites`
  ADD CONSTRAINT `item_favorites_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `item_favorites_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `item_img`
--
ALTER TABLE `item_img`
  ADD CONSTRAINT `item_img_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `item_options_value`
--
ALTER TABLE `item_options_value`
  ADD CONSTRAINT `item_options_value_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `item_options_value_ibfk_2` FOREIGN KEY (`option_id`) REFERENCES `options` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `item_options_value_ibfk_3` FOREIGN KEY (`option_value_id`) REFERENCES `options_value` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `l_message`
--
ALTER TABLE `l_message`
  ADD CONSTRAINT `l_message_ibfk_1` FOREIGN KEY (`id`) REFERENCES `l_source_message` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `menu`
--
ALTER TABLE `menu`
  ADD CONSTRAINT `menu_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `menu` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `menu_category`
--
ALTER TABLE `menu_category`
  ADD CONSTRAINT `menu_category_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `menu_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `monitoring_sms`
--
ALTER TABLE `monitoring_sms`
  ADD CONSTRAINT `monitoring_sms_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `options_category`
--
ALTER TABLE `options_category`
  ADD CONSTRAINT `options_category_ibfk_1` FOREIGN KEY (`cid`) REFERENCES `category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `options_category_ibfk_2` FOREIGN KEY (`option_id`) REFERENCES `options` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `options_value`
--
ALTER TABLE `options_value`
  ADD CONSTRAINT `options_value_ibfk_1` FOREIGN KEY (`option_id`) REFERENCES `options` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`promo_code_id`) REFERENCES `promo_code` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Ограничения внешнего ключа таблицы `orders_comments`
--
ALTER TABLE `orders_comments`
  ADD CONSTRAINT `orders_comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `s_user` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `orders_comments_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `orders_history`
--
ALTER TABLE `orders_history`
  ADD CONSTRAINT `orders_history_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_history_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `s_user` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Ограничения внешнего ключа таблицы `orders_items`
--
ALTER TABLE `orders_items`
  ADD CONSTRAINT `orders_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`);

--
-- Ограничения внешнего ключа таблицы `orders_items_handing`
--
ALTER TABLE `orders_items_handing`
  ADD CONSTRAINT `orders_items_handing_ibfk_1` FOREIGN KEY (`orders_items_id`) REFERENCES `orders_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_items_handing_ibfk_2` FOREIGN KEY (`type_handling_id`) REFERENCES `type_handling` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `orders_pay`
--
ALTER TABLE `orders_pay`
  ADD CONSTRAINT `orders_pay_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `orders_rollback_items`
--
ALTER TABLE `orders_rollback_items`
  ADD CONSTRAINT `orders_rollback_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_rollback_items_ibfk_2` FOREIGN KEY (`item_order_id`) REFERENCES `orders_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `orders_rollback_sets`
--
ALTER TABLE `orders_rollback_sets`
  ADD CONSTRAINT `orders_rollback_sets_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_rollback_sets_ibfk_2` FOREIGN KEY (`set_order_id`) REFERENCES `orders_sets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `orders_sets`
--
ALTER TABLE `orders_sets`
  ADD CONSTRAINT `orders_sets_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_sets_ibfk_2` FOREIGN KEY (`set_id`) REFERENCES `sets` (`id`);

--
-- Ограничения внешнего ключа таблицы `orders_unloading`
--
ALTER TABLE `orders_unloading`
  ADD CONSTRAINT `orders_unloading_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `pickpoint`
--
ALTER TABLE `pickpoint`
  ADD CONSTRAINT `fk-pickpoint-city_id` FOREIGN KEY (`city_id`) REFERENCES `city` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `pickpoints_users`
--
ALTER TABLE `pickpoints_users`
  ADD CONSTRAINT `pickpoints_users_ibfk_1` FOREIGN KEY (`pickpoint_id`) REFERENCES `pickpoint` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pickpoints_users_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `s_user` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `pickpoint_img`
--
ALTER TABLE `pickpoint_img`
  ADD CONSTRAINT `pickpoint_img_ibfk_1` FOREIGN KEY (`pickpoint_id`) REFERENCES `pickpoint` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `recipes_img`
--
ALTER TABLE `recipes_img`
  ADD CONSTRAINT `recipes_img_ibfk_1` FOREIGN KEY (`id_recipes`) REFERENCES `recipes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `recipes_item`
--
ALTER TABLE `recipes_item`
  ADD CONSTRAINT `recipes_item_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  ADD CONSTRAINT `recipes_item_ibfk_2` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `recipes_method`
--
ALTER TABLE `recipes_method`
  ADD CONSTRAINT `recipes_method_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `reviews_item`
--
ALTER TABLE `reviews_item`
  ADD CONSTRAINT `reviews_item_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reviews_item_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Ограничения внешнего ключа таблицы `seo_lang`
--
ALTER TABLE `seo_lang`
  ADD CONSTRAINT `seo_lang_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `seo` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `sets_items`
--
ALTER TABLE `sets_items`
  ADD CONSTRAINT `sets_items_ibfk_1` FOREIGN KEY (`set_id`) REFERENCES `sets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sets_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `spec_action_codes`
--
ALTER TABLE `spec_action_codes`
  ADD CONSTRAINT `spec_action_codes_ibfk_1` FOREIGN KEY (`spec_action_id`) REFERENCES `spec_actions` (`id`);

--
-- Ограничения внешнего ключа таблицы `spec_action_phones`
--
ALTER TABLE `spec_action_phones`
  ADD CONSTRAINT `spec_action_phones_ibfk_1` FOREIGN KEY (`spec_action_code_id`) REFERENCES `spec_action_codes` (`id`);

--
-- Ограничения внешнего ключа таблицы `structure`
--
ALTER TABLE `structure`
  ADD CONSTRAINT `structure_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `structure` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `structure_ibfk_2` FOREIGN KEY (`id_template`) REFERENCES `template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `s_auth`
--
ALTER TABLE `s_auth`
  ADD CONSTRAINT `s_auth_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `s_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `s_history_money`
--
ALTER TABLE `s_history_money`
  ADD CONSTRAINT `s_history_money_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `s_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `s_user_plan`
--
ALTER TABLE `s_user_plan`
  ADD CONSTRAINT `s_user_plan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `s_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `user_address`
--
ALTER TABLE `user_address`
  ADD CONSTRAINT `user_address_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_address_ibfk_2` FOREIGN KEY (`city`) REFERENCES `city` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `user_invited`
--
ALTER TABLE `user_invited`
  ADD CONSTRAINT `user_invited_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_invited_ibfk_2` FOREIGN KEY (`user_invited`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
