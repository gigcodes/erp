<?php

if (isset($_GET['restart']) && $_GET['restart'] == 'yes') {
    $cmd = 'sh ' . __DIR__ . '/../../server_scripts/restart_db01.sh 2>&1';

    $allOutput   = [];
    $allOutput[] = $cmd;
    $result      = exec($cmd, $allOutput);

    echo "<pre>\n";
    var_dump($allOutput);
    echo "</pre>\n";
}

?>

<a href="./Eak4Ue4Zu8Hem8.php?restart=yes">Restart Database</a>
