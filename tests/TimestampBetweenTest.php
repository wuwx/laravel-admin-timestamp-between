<?php

namespace Wuwx\LaravelAdminTimestampBetween\Tests;

use PHPUnit\Framework\TestCase;
use Wuwx\LaravelAdminTimestampBetween\TimestampBetween;

/**
 * @covers \Wuwx\LaravelAdminTimestampBetween\TimestampBetween
 */
class TimestampBetweenTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        date_default_timezone_set('UTC');
    }

    public function testConditionUsesWhereBetweenWhenBothBoundsPresent()
    {
        $condition = $this->filter()->condition([
            'created_at' => [
                'start' => '2020-01-01 00:00:00',
                'end'   => '2020-01-31 00:00:00',
            ],
        ]);

        $this->assertSame([
            'whereBetween' => [
                'created_at',
                ['start' => 1577836800, 'end' => 1580428800],
            ],
        ], $condition);
    }

    public function testConditionUsesGreaterThanWhenOnlyStartPresent()
    {
        $condition = $this->filter()->condition([
            'created_at' => ['start' => '2020-01-01 00:00:00'],
        ]);

        $this->assertSame([
            'where' => ['created_at', '>=', 1577836800],
        ], $condition);
    }

    public function testConditionUsesLessThanWhenOnlyEndPresent()
    {
        $condition = $this->filter()->condition([
            'created_at' => ['end' => '2020-01-31 00:00:00'],
        ]);

        $this->assertSame([
            'where' => ['created_at', '<=', 1580428800],
        ], $condition);
    }

    public function testConditionIgnoresEmptyBounds()
    {
        $condition = $this->filter()->condition([
            'created_at' => ['start' => '', 'end' => ''],
        ]);

        $this->assertNull($condition);
    }

    public function testConditionIgnoresMissingColumn()
    {
        $this->assertNull($this->filter()->condition([]));
    }

    public function testConditionIgnoresFilterMarkedAsIgnored()
    {
        $filter = $this->filter();
        $filter->ignore();

        $condition = $filter->condition([
            'created_at' => ['start' => '2020-01-01 00:00:00'],
        ]);

        $this->assertNull($condition);
    }

    public function testConditionIgnoresScalarInput()
    {
        $this->assertNull($this->filter()->condition(['created_at' => '2020-01-01']));
    }

    public function testConditionIgnoresArrayWithoutBounds()
    {
        $this->assertNull($this->filter()->condition([
            'created_at' => ['from' => '2020-01-01 00:00:00'],
        ]));
    }

    public function testConditionIgnoresUnparsableBounds()
    {
        $this->assertNull($this->filter()->condition([
            'created_at' => ['start' => 'not-a-date', 'end' => 'also-not-a-date'],
        ]));
    }

    public function testConditionFallsBackToRemainingBoundWhenOneIsUnparsable()
    {
        $condition = $this->filter()->condition([
            'created_at' => ['start' => 'not-a-date', 'end' => '2020-01-31 00:00:00'],
        ]);

        $this->assertSame([
            'where' => ['created_at', '<=', 1580428800],
        ], $condition);
    }

    public function testValueKeepsRawInputForFormRedisplay()
    {
        $filter = $this->filter();

        $filter->condition([
            'created_at' => ['start' => '2020-01-01 00:00:00', 'end' => ''],
        ]);

        $this->assertSame(
            ['start' => '2020-01-01 00:00:00', 'end' => ''],
            $filter->getValue()
        );
    }

    private function filter()
    {
        return new TimestampBetween('created_at');
    }
}
