# Inactive

**📢 Note:** This repository is not maintained any more.

Monolog Correlation plugin
==========================

Injects correlation ids in all records.

Installation
------------

```bash
composer require manomano-tech/correlation-ids-monolog
```

Usage
-----

```php
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
```

Customizing output format
-------------------------

### Entry names

By default, values will be rendered like this:

```php
$record = [
    'extra' => [
        'current' => '6a051d24-aa5b-4c57-bcb4-bbbb7eda1c16',
        'parent' => '3fc044d9-90fa-4b50-b6d9-3423f567155f',
        'root' => '3b5263fa-1644-4750-8f11-aaf61e58cd9e',
    ],
];
```

You can change this by providing a second argument to the constructor:

```php
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
```

will produce:

```php
$record = [
    'extra' => [
        'current-id' => '6a051d24-aa5b-4c57-bcb4-bbbb7eda1c16',
        'parent-id' => '3fc044d9-90fa-4b50-b6d9-3423f567155f',
        'root-id' => '3b5263fa-1644-4750-8f11-aaf61e58cd9e',
    ],
];
```

### Group request correlation identifier in one entry

By default, the processor will add an entry in the `extra` section for every
correlation id key.

You can group all the id in one array:

```php
use ManoManoTech\CorrelationIdMonolog\CorrelationIdProcessor;
use Monolog\Logger;

$processor = new CorrelationIdProcessor($correlationIdContainer);
$processor->groupCorrelationIdsInOneArrayWithKey('correlation');

$logger = new Logger('channel-name');
$logger->pushProcessor([$processor]);

$logger->addInfo('message');
```

will produce:

```php
$record = [
    'extra' => [
        'correlation' => [
            'current' => '6a051d24-aa5b-4c57-bcb4-bbbb7eda1c16',
            'parent' => '3fc044d9-90fa-4b50-b6d9-3423f567155f',
            'root' => '3b5263fa-1644-4750-8f11-aaf61e58cd9e',
        ],
    ],
];
```

### Skip empty correlation id

By default, the processor will add every correlation id even if they are empty.

```php
$record = [
    'extra' => [
        'current' => '6a051d24-aa5b-4c57-bcb4-bbbb7eda1c16',
        'parent' => null,
        'root' => null,
    ],
];
```

You can change this behavior with the `skipEmptyValues` method:


```php
use ManoManoTech\CorrelationIdMonolog\CorrelationIdProcessor;
use Monolog\Logger;

$processor = new CorrelationIdProcessor($CorrelationIdContainer);
$processor->skipEmptyValues();

$logger = new Logger('channel-name');
$logger->pushProcessor([$processor]);

$logger->addInfo('message');
```

will produce:

```php
$record = [
    'extra' => [
        'current' => '6a051d24-aa5b-4c57-bcb4-bbbb7eda1c16',
    ],
];
```

