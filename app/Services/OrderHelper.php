<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderHelper
{
    const DEFAULT_GAP = 100000;
    const PRECISION = 30;

    /**
     * Assign default order to new item.
     */
    public static function assign(Model $model, string $orderField = 'order'): void
    {
        $maxOrder = $model->newQuery()->max($orderField) ?? 0;
        $model->{$orderField} = $maxOrder + self::DEFAULT_GAP;
    }

    /**
     * Reorder an item globally between two others.
     * You can pass null to beforeId or afterId to simulate start/end of list.
     */
    public static function reorder(Model $model, $beforeModel = null, $afterModel = null, string $orderField = 'order'): void
    {
        $beforeOrder = $beforeModel?->{$orderField} ?? 0;
        $afterOrder = $afterModel?->{$orderField} ?? ($beforeOrder + (self::DEFAULT_GAP * 2));

        // calculate midpoint with precision
        $newOrder = self::midpoint($beforeOrder, $afterOrder);

        $model->{$orderField} = $newOrder;
        $model->save();
    }

    /**
     * Normalize a scope of items (optional maintenance).
     */
    public static function normalize(string $modelClass, string $orderField = 'order', ?\Closure $scopedQuery = null): void
    {
        $query = (new $modelClass)->newQuery();
        if ($scopedQuery) {
            $query = $scopedQuery($query);
        }

        $items = $query->orderBy($orderField)->get();

        DB::transaction(function () use ($items, $orderField) {
            $order = self::DEFAULT_GAP;
            foreach ($items as $item) {
                $item->{$orderField} = $order;
                $item->save();
                $order += self::DEFAULT_GAP;
            }
        });
    }

    /**
     * Calculate midpoint with defined precision.
     */
    private static function midpoint($a, $b): string
    {
        return bcdiv(bcadd((string) $a, (string) $b, self::PRECISION), '2', self::PRECISION);
    }
}
