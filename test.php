<?php

var_dump(preg_match("/^(заморо\S+)\D*(\d+)/i", "заморозь 124, ром", $matches));
var_dump($matches);