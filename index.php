<?php
require "vendor/autoload.php";


use RomanBots\Bot\Bot;


$bot = new Bot(VK_API_ACCESS_TOKEN, CALLBACK_API_CONFIRMATION_TOKEN, VK_API_SECRET);
$bot->listen();