<?php
$shouldRunPail = extension_loaded('pcntl') && function_exists('pcntl_fork');
if ($shouldRunPail) {
    passthru('php artisan pail --timeout=0');
}
?>

