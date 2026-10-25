<?php

/*
|--------------------------------------------------------------------------
| Refuse To Boot On An Unpatched PHP
|--------------------------------------------------------------------------
|
| PHP2Shell (CVE-2026-109618 / CVE-2026-109619) is fixed in 8.3.36, 8.4.27
| and 8.5.12 (Laravel 13 needs 8.3+). vendor/composer/platform_check.php
| only enforces the lowest bound of the "php" constraint in composer.json,
| so on its own it would still boot on an unpatched 8.4.0-8.4.26. This file
| is loaded through composer.json "autoload.files", so web, artisan, queue
| and tests all run it, and it survives composer regenerating vendor/. Keep
| the floors below in step with the "php" constraint in composer.json.
|
*/

$patchedFloors = [80300 => 80336, 80400 => 80427, 80500 => 80512];
$minor = intdiv(PHP_VERSION_ID, 100) * 100;

if ($minor < 80300 || PHP_VERSION_ID >= 90000 || PHP_VERSION_ID < ($patchedFloors[$minor] ?? 0)) {
    throw new RuntimeException(
        'This app requires PHP 8.3.36+, 8.4.27+ or 8.5.12+ (below 9.0), patched for PHP2Shell. You are running '.PHP_VERSION.'.'
    );
}
