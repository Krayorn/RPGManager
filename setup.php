<?php

$createSqlDB = shell_exec('vendor\bin\doctrine orm:schema-tool:create --dump-sql');
echo $createSqlDB;
$createDB = shell_exec('vendor\bin\doctrine orm:schema-tool:create');
echo "Done!";