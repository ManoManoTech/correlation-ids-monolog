<?php

declare(strict_types=1);
require '01-usage.php';

use ManoManoTech\CorrelationIdMonolog\CorrelationIdProcessor;
use Monolog\Logger;

$processor = new CorrelationIdProcessor($correlationIdContainer);
$processor->groupCorrelationIdsInOneArrayWithKey('correlation');

$logger = new Logger('channel-name');
$logger->pushProcessor([$processor]);

$logger->addInfo('message');
