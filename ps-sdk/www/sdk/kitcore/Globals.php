<?php

/*
 * Промежуток между вызовами внешнего процесса (в секундах)
 */
define('EXTERNAL_PROCESS_CALL_DELAY', 600);

/*
 * Время жизни кешей (в минутах)
 */
define('CACHE_LITE_LIFE_TIME', 720);

/*
 * Включено ли логирование
 */
define('LOGGING_ENABLED', true);

/*
 * Включено ли профилирование
 */
define('PROFILING_ENABLED', true);

/*
 * Размер пачки записей для дампа таблицы
 */
define('TABLE_DUMP_PORTION', 20000);

/*
 * Максимальный размер файла аудита (в мегабайтах)
 */
define('PROFILING_MAX_FILE_SIZE', 1);

/*
 * Максимальное кол-во дампов файлов последних эксепшенов
 */
define('EXCEPTION_MAX_FILES_COUNT', 10);

/*
 * Максимальное кол-во хранимых последних отправленных email
 */
define('EMAILS_MAX_FILES_COUNT', 10);

/*
 * Интервал между действиями пользователя (в секундах)
 */
define('ACTIVITY_INTERVAL', 0);

/*
 * Максимальная глубина дерева комментариев (root=1)
 */
define('MAX_COMMENTS_DEEP_LEVEL', 10);

/*
 * Заменяем ли формулы на картинки
 */
define('REPLACE_FORMULES_WITH_IMG', true);

/*
 * Заменять ли формулы на спрайты
 */
define('REPLACE_FORMULES_WITH_SPRITES', true);

/*
 * Нормализация страницы (удаление двойных пробелов)
 */
define('NORMALIZE_PAGE', false);

/*
 * Кол-во комментариев, показываемых под постом
 */
define('MAX_COMMENTS_COUNT', 50);

/*
 * Кол-во постов в одном пейджинге
 */
define('POSTS_IN_ONE_PAGING', 3);

/*
 * Кол-во новостей в ленте
 */
define('NEWS_IN_LINE', 5);

/*
 * Включение режима Production
 */
define('PS_PRODUCTION', false);

/*
 * Возможность переходить на пост/рубрику по идентификатору
 */
define('ALLOW_DIRECT_BY_IDENT', false);

/*
 * Адрес базы
 */
define('DB_HOST', 'localhost');

/*
 * Пользователь доступа к базе
 */
define('DB_USER', 'ps');

/*
 * Пароль для доступа к базе
 */
define('DB_PASSWORD', 'ps');

/*
 * Название схемы
 */
define('DB_NAME', 'ps');

/*
 * Адрес SMTP сервера
 */
define('SMTP_HOST', 'smtp.yandex.ru');

/*
 * Имя для доступа к SMTP
 */
define('SMTP_USERNAME', 'postupayu@yandex.ru');

/*
 * Пароль для доступа к SMTP
 */
define('SMTP_PASSWORD', 'Anastasiya!1997');
?>