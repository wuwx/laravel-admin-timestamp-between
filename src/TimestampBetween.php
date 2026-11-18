<?php
namespace Wuwx\LaravelAdminTimestampBetween;

use Encore\Admin\Grid\Filter\Between;
use Illuminate\Support\Arr;

class TimestampBetween extends Between
{
    public function condition($inputs)
    {
        if ($this->ignore) {
            return;
        }

        if (!Arr::has($inputs, $this->column)) {
            return;
        }

        $this->value = Arr::get($inputs, $this->column);

        if (!is_array($this->value)) {
            return;
        }

        $value = array_filter($this->value, function ($val) {
            return $val !== '';
        });

        $start = isset($value['start']) ? $this->timestamp($value['start']) : null;
        $end   = isset($value['end']) ? $this->timestamp($value['end']) : null;

        if (is_null($start) && is_null($end)) {
            return;
        }

        if (is_null($start)) {
            return $this->buildCondition($this->column, '<=', $end);
        }

        if (is_null($end)) {
            return $this->buildCondition($this->column, '>=', $start);
        }

        $this->query = 'whereBetween';

        return $this->buildCondition($this->column, ['start' => $start, 'end' => $end]);
    }

    private function timestamp($value)
    {
        if (!is_string($value)) {
            return null;
        }

        $timestamp = strtotime($value);

        return $timestamp === false ? null : $timestamp;
    }
}
