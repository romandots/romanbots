<?php
//Строка для подтверждения адреса сервера из настроек Callback API
define('CALLBACK_API_CONFIRMATION_TOKEN', 'b1d8183d');

//Ключ доступа сообщества
define('VK_API_ACCESS_TOKEN', '2a4f020ca3ac6b989d51bf54125323155899debd21c24c12a403a3b7060caa571f23056a7e36a29e52c61');

//Cекретная подпись callback запросов
define('VK_API_SECRET', 'zYbOYnxeVflOYEccTccq');

// Ключ для доступа к Yandex Speech Kit
define('YANDEX_API_KEY', '2c7f3fc3-854a-4e70-8640-a44b6f5fc675');


const DB_HOST = '128.199.49.62';
const DB_DATABASE = 'admin_dev_bezpravil';
const DB_USERNAME = 'admin_bezpravil';
const DB_PASSWORD = '90ung11gTv';


define('BOT_BASE_DIRECTORY', __DIR__);
define('BOT_LOGS_DIRECTORY', BOT_BASE_DIRECTORY.'/logs');
define('BOT_IMAGES_DIRECTORY', BOT_BASE_DIRECTORY.'/static');
define('BOT_AUDIO_DIRECTORY', BOT_BASE_DIRECTORY.'/audio');
define('BOT_VOICE_DIRECTORY', BOT_BASE_DIRECTORY.'/voice');