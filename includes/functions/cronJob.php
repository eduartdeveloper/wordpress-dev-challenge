<?php
/**
 * Cron Job Setup
 *
 * This code sets up a cron job to execute a PHP script at a specific schedule.
 */

/**
 * Include Cron Expression library
 *
 * Make sure to include the cron-expression library before using it.
 * The library provides the functionality to generate cron expressions.
 */
require_once __DIR__ . '/vendor/autoload.php'; // Asegúrate de incluir la biblioteca cron-expression

use Cron\CronExpression;

// Path to the PHP file that contains the code to be executed as a cron job
$filePath = LINK_CHECK_MASTER_PATH . "/includes/admin/validator.php";

// Cron job command to run the PHP script daily at 00:00
$cronExpression = CronExpression::factory('@daily')->getExpression();
$cronCommand = 'crontab -l | { cat; echo "'.$cronExpression.' /usr/bin/php '.$filePath.'"; } | crontab -';

// Cron job command to run the PHP script daily at 00:00
$result = shell_exec($cronCommand);



?>
