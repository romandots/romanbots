<?php
require "autoload.php";

use RomanBots\Bots\VkBot;


log_msg("######################## STARTING ########################");


$bot = new VkBot();
$bot->listen();