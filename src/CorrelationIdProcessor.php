<?php

declare(strict_types=1);

namespace ManoManoTech\CorrelationIdMonolog;

use ManoManoTech\CorrelationId\CorrelationEntryName;
use ManoManoTech\CorrelationId\CorrelationEntryNameInterface;
use ManoManoTech\CorrelationId\CorrelationIdContainerInterface;

final class CorrelationIdProcessor
{
    /** @var string */
    private $extraEntryName = '';
    /** @var bool */
    private $skipEmptyValues = false;
    /** @var CorrelationEntryNameInterface */
    private $correlationEntryName;
    /** @var CorrelationIdContainerInterface */
    private $correlationIdContainer;

    public function __construct(
        CorrelationIdContainerInterface $correlationIdContainer,
        CorrelationEntryNameInterface $correlationEntryName = null
    ) {
        $this->correlationIdContainer = $correlationIdContainer;
        $this->correlationEntryName = $correlationEntryName ?? CorrelationEntryName::simple();
    }

    public function __invoke(array $record): array
    {
        $extra = [
            $this->correlationEntryName->current() => $this->correlationIdContainer->current(),
            $this->correlationEntryName->parent() => $this->correlationIdContainer->parent(),
            $this->correlationEntryName->root() => $this->correlationIdContainer->root(),
        ];

        if ($this->skipEmptyValues) {
            $extra = array_filter($extra);
        }

        if (!isset($record['extra'])) {
            $record['extra'] = [];
        }

        if ('' !== $this->extraEntryName) {
            $record['extra'][$this->extraEntryName] = $extra;
        } else {
            $record['extra'] = array_merge($record['extra'], $extra);
        }

        return $record;
    }

    /**
     * Group correlation ids in an array.
     *
     * @param string $key The key of the array in the monolog extra array
     */
    public function groupCorrelationIdsInOneArrayWithKey(string $key): void
    {
        $this->extraEntryName = $key;
    }

    public function skipEmptyValues(): void
    {
        $this->skipEmptyValues = true;
    }
}
