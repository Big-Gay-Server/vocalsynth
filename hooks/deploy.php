<?php
$secret = 'JUdleaAm3uuaRPKzcv7ToJQEW';
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';

$output = shell_exec("cd /usr/share/nginx/html/vocalsynth && git pull origin main 2>&1");
file_put_contents('deploy_log.txt', date('Y-m-d H:i:s') . "\n" . $output . "\n---\n", FILE_APPEND);
echo $output;