<?php

declare(strict_types=1);

use ManoManoTech\CorrelationIdMonolog\CorrelationIdProcessor;
use Monolog\Logger;

$processor = new CorrelationIdProcessor($CorrelationIdContainer);
$processor->skipEmptyValues();

$logger = new Logger('channel-name');
$logger->pushProcessor([$processor]);

$logger->addInfo('message');
