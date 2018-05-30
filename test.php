<pre><?php
require_once "autoload.php";

redis_set('123:command', "holdit");
var_dump(redis_get('123:command'));
?></pre>