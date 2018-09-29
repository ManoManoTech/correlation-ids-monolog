<?php

declare(strict_types=1);
require '01-usage.php';

use ManoManoTech\CorrelationId\CorrelationEntryName;
use ManoManoTech\CorrelationIdMonolog\CorrelationIdProcessor;
use Monolog\Logger;

$correlationEntryName = new CorrelationEntryName(
    'current-id',
    'parent-id',
    'root-id'
);

$processor = new CorrelationIdProcessor(
    $correlationIdContainer,
    $correlationEntryName
);

$logger = new Logger('channel-name');
$logger->pushProcessor([$processor]);

$logger->addInfo('message');
