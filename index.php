<?php
require "autoload.php";


use RomanBots\Bot\Bot;

debug("a", "b");

$bot = new Bot(VK_API_ACCESS_TOKEN, CALLBACK_API_CONFIRMATION_TOKEN, VK_API_SECRET);
$bot->listen();