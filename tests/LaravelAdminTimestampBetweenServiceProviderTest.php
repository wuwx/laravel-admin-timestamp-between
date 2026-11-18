<?php

namespace Wuwx\LaravelAdminTimestampBetween\Tests;

use Encore\Admin\Grid\Filter;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Wuwx\LaravelAdminTimestampBetween\LaravelAdminTimestampBetweenServiceProvider;
use Wuwx\LaravelAdminTimestampBetween\TimestampBetween;

/**
 * @covers \Wuwx\LaravelAdminTimestampBetween\LaravelAdminTimestampBetweenServiceProvider
 */
class LaravelAdminTimestampBetweenServiceProviderTest extends TestCase
{
    public function testBootRegistersTimestampBetweenFilter()
    {
        (new LaravelAdminTimestampBetweenServiceProvider(new Container()))->boot();

        $this->assertSame(TimestampBetween::class, $this->registeredFilters()['timestampBetween']);
    }

    private function registeredFilters()
    {
        $property = new ReflectionProperty(Filter::class, 'supports');

        if (PHP_VERSION_ID < 80100) {
            $property->setAccessible(true);
        }

        return $property->getValue();
    }
}
