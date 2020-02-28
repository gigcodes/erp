<?php
$cmd = 'sh ' . getenv('SERVER_SCRIPTS_PATH') . '/restart_db01.sh 2>&1';

echo $cmd; exit();

$allOutput = array();
$allOutput[] = $cmd;
$result = exec($cmd, $allOutput);

echo "<pre>\n";
var_dump($allOutput);
echo "</pre>\n";