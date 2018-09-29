<?php

declare(strict_types=1);

use ManoManoTech\CorrelationId\CorrelationEntryName;
use ManoManoTech\CorrelationId\Factory\HeaderCorrelationIdContainerFactory;
use ManoManoTech\CorrelationId\Generator\RamseyUuidGenerator;
use ManoManoTech\CorrelationIdMonolog\CorrelationIdProcessor;
use Monolog\Logger;

// We specify which generator will be responsible for generating the
// identification of the current process
$generator = new RamseyUuidGenerator();

// We define what are the http header names to look for
// this is optional. We show the default values here.
$correlationEntryNames = new CorrelationEntryName(
    'current-correlation-id',
    'parent-correlation-id',
    'root-correlation-id'
);

$factory = new HeaderCorrelationIdContainerFactory(
    $generator,
    $correlationEntryNames
);
$correlationIdContainer = $factory->create(getallheaders());

// now you can create your monolog processor

$processor = new CorrelationIdProcessor($correlationIdContainer);

$logger = new Logger('channel-name');
$logger->pushProcessor([$processor]);

$logger->addInfo('message');
