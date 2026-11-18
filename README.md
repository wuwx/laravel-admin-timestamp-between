Laravel Admin Timestamp Between Extension
======
[![CI](https://github.com/wuwx/laravel-admin-timestamp-between/actions/workflows/ci.yml/badge.svg)](https://github.com/wuwx/laravel-admin-timestamp-between/actions/workflows/ci.yml)

给 laravel-admin 的 Grid Filter 增加一个按 Unix 时间戳区间筛选的过滤器。

## 安装

```
composer require wuwx/laravel-admin-timestamp-between
```

服务提供者通过包自动发现注册，无需手动添加到 `config/app.php`。

Composer 2.10 及以上会因 `encore/laravel-admin`（`<=1.8.19`，CVE-2023-24249）的安全公告拒绝解析依赖。已在项目里装过 laravel-admin 的可以直接安装；否则需要放行：

```
composer config policy.advisories.block false
```

## 用法

```php
$grid->filter(function ($filter) {
    $filter->timestampBetween('created_at', '创建时间')->datetime();
});
```

`->datetime()` 会把两个输入框换成日期时间选择器，并让结束时间不能早于开始时间。不加也能用，就是两个纯文本输入框。

支持关联字段，如 `timestampBetween('user.created_at')`，此时生成的名称是 `user[created_at][start]`。

## 筛选行为

| 输入 | 生成的查询 |
| --- | --- |
| start 和 end 都有 | `whereBetween(column, [start, end])` |
| 只有 start | `where(column, '>=', start)` |
| 只有 end | `where(column, '<=', end)` |
| 都为空、缺失或无法解析成时间 | 该条件不参与查询 |

两端都填写时，区间是闭区间，两端都包含。

## 注意事项

- **只支持秒级时间戳**。列里存的是毫秒（13 位）时，筛选值仍按秒解析再和它比较，区间相差三个数量级，结果基本恒为空且不报错。这类列请用原生 `between` 并自行换算。
- **只有 end 且用 `->date()` 时，解析结果是当天 00:00:00**，当天的数据会被排除。要么用 `->datetime()` 精确到时间，要么把结束时间放到次日 00:00:00。
- 无法解析的时间串按「未填写」处理，不是报错。这可以避免手工改 URL 把筛选页面打挂，但也意味着写错格式时条件会被静默丢弃。

## 测试

```
composer install
composer test
```

Composer 2.10 及以上需先按上文放行安全公告，否则 `composer install` 无法解析依赖。

CI 覆盖 PHP 7.4 和 8.3 两个版本跑 PHPUnit，另外对 7.4 至 8.4 做语法检查。
