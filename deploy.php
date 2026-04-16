<?php
// Simple verification (optional but recommended) - commented out for now until i figure this out
$secret = 'JUdleaAm3uuaRPKzcv7ToJQEW';
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';

// Run the git pull command
$output = shell_exec("cd /mnt/raid5/create/vocalsynth && git pull origin main 2>&1");
echo "<pre>$output</pre>";
?>