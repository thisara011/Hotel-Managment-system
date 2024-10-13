<?php
function writeLog($message) {
    $logFile = 'log.txt'; // Change to your desired log file path
    $timestamp = date("Y-m-d H:i:s");
    $logMessage = "[$timestamp] $message" . PHP_EOL;
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}
?>
