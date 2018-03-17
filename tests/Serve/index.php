<?php

$handle = fopen('access.log', 'a');
fwrite($handle, json_encode($_SERVER). PHP_EOL);
fclose($handle);
