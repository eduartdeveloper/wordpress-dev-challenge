<?php

require_once __DIR__ . '/vendor/autoload.php'; // Asegúrate de incluir la biblioteca cron-expression

use Cron\CronExpression;

// Ruta al archivo PHP que contiene el código a ejecutar como cron job
$filePath = LINK_CHECK_MASTER_PATH . "/includes/admin/validator.php";

// Comando cron job que se ejecutará cada día a las 00:00
$cronExpression = CronExpression::factory('@daily')->getExpression();
$cronCommand = 'crontab -l | { cat; echo "'.$cronExpression.' /usr/bin/php '.$filePath.'"; } | crontab -';

// Ejecutar el comando para agregar el cron job
$result = shell_exec($cronCommand);



?>
