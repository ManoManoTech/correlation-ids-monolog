<?php

declare(strict_types=1);

namespace ManoManoTech\CorrelationIdMonolog\Tests;

use ManoManoTech\CorrelationId\CorrelationEntryNameInterface;
use ManoManoTech\CorrelationId\CorrelationIdContainerInterface;
use ManoManoTech\CorrelationIdMonolog\CorrelationIdProcessor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/** @covers \ManoManoTech\CorrelationIdMonolog\CorrelationIdProcessor */
final class CorrelationIdProcessTest extends TestCase
{
    /** @var CorrelationEntryNameInterface|MockObject */
    private $correlationEntryName;

    protected function setUp(): void
    {
        parent::setUp();

        $correlationEntryName = $this->createMock(CorrelationEntryNameInterface::class);
        $correlationEntryName->expects(self::once())
                             ->method('current')
                             ->willReturn('current');
        $correlationEntryName->expects(self::once())
                             ->method('parent')
                             ->willReturn('parent');
        $correlationEntryName->expects(self::once())
                             ->method('root')
                             ->willReturn('root');

        $this->correlationEntryName = $correlationEntryName;
    }

    /**
     * @dataProvider provideDataForProcessor
     */
    public function testProcessor(string $expectedCurrent, ?string $expectedParent, ?string $expectedRoot): void
    {
        // init
        $correlationIdContainer = $this->createCorrelationIdContainer($expectedCurrent, $expectedParent, $expectedRoot);

        $object = new CorrelationIdProcessor($correlationIdContainer, $this->correlationEntryName);

        // run
        $result = $object([]);

        // check
        $expectedResult = [
            'extra' => [
                'current' => $expectedCurrent,
                'parent' => $expectedParent,
                'root' => $expectedRoot,
            ],
        ];
        static::assertEquals($expectedResult, $result);
    }

    /** @dataProvider provideDataForProcessor */
    public function testGroupCorrelationIdsInOneArrayWithKey(
        string $expectedCurrent,
        ?string $expectedParent,
        ?string $expectedRoot
    ): void {
        // init
        $correlationIdContainer = $this->createCorrelationIdContainer($expectedCurrent, $expectedParent, $expectedRoot);

        $object = new CorrelationIdProcessor($correlationIdContainer, $this->correlationEntryName);

        // run
        $object->groupCorrelationIdsInOneArrayWithKey('my_key');
        $result = $object([]);

        // check
        $expectedResult = [
            'extra' => [
                'my_key' => [
                    'current' => $expectedCurrent,
                    'parent' => $expectedParent,
                    'root' => $expectedRoot,
                ],
            ],
        ];
        static::assertEquals($expectedResult, $result);
    }

    /** @dataProvider provideDataForProcessor */
    public function testLogEmptyValues(string $expectedCurrent, ?string $expectedParent, ?string $expectedRoot): void
    {
        // init
        $correlationIdContainer = $this->createCorrelationIdContainer($expectedCurrent, $expectedParent, $expectedRoot);

        $object = new CorrelationIdProcessor($correlationIdContainer, $this->correlationEntryName);

        // run
        $object->skipEmptyValues();
        $result = $object([]);

        // check
        $expectedResult = [
            'extra' => [
                'current' => $expectedCurrent,
            ],
        ];

        if (null !== $expectedParent) {
            $expectedResult['extra']['parent'] = $expectedParent;
        }

        if (null !== $expectedRoot) {
            $expectedResult['extra']['root'] = $expectedRoot;
        }

        static::assertEquals($expectedResult, $result);
    }

    public function provideDataForProcessor(): array
    {
        return [
            'all values provided' => [
                'foo',
                'bar',
                'baz',
            ],
            'parent is null' => [
                'foo',
                null,
                'baz',
            ],
            'root is null' => [
                'foo',
                'bar',
                null,
            ],
            'both parent and root are null' => [
                'foo',
                null,
                null,
            ],
        ];
    }

    private function createCorrelationIdContainer(
        string $expectedCurrent,
        ?string $expectedParent,
        ?string $expectedRoot
    ): CorrelationIdContainerInterface {
        $correlationIdContainer = $this->createMock(CorrelationIdContainerInterface::class);
        $correlationIdContainer->expects(self::once())
                               ->method('current')
                               ->willReturn($expectedCurrent);
        $correlationIdContainer->expects(self::once())
                               ->method('parent')
                               ->willReturn($expectedParent);
        $correlationIdContainer->expects(self::once())
                               ->method('root')
                               ->willReturn($expectedRoot);

        return $correlationIdContainer;
    }
}
