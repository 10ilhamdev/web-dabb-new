<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

trait SwapsOrder
{
    /**
     * Swap order when updating an item's order.
     *
     * Handles all cases:
     * - Moving forward (e.g. order 3 → 1): shifts items 1,2 down to 2,3
     * - Moving backward (e.g. order 1 → 3): shifts items 2,3 up to 1,2
     * - Moving to empty slot (e.g. 1 → 4 in [1,2,3]): shifts 2,3 to 3,4
     *
     * @param Model $model           Item that is being updated
     * @param int   $newOrder        Target order after update
     * @param int   $oldOrder        Current order before update
     * @param array $scopeConditions Scope conditions, e.g. ['parent_id' => 5]
     */
    protected function swapOrder($model, int $newOrder, int $oldOrder, array $scopeConditions): void
    {
        $modelClass = get_class($model);
        $table = $model->getTable();

        DB::transaction(function () use ($model, $newOrder, $oldOrder, $scopeConditions, $modelClass, $table) {
            // 1. Move the current item to a temporary negative position to clear its current 'order' slot
            // This prevents unique constraint violations during the subsequent range shift.
            $tempOrder = -($model->id);
            DB::table($table)->where('id', $model->id)->update(['order' => $tempOrder]);

            // 2. Perform the range shift for other items
            if ($newOrder < $oldOrder) {
                // Moving forward (e.g. 3 -> 1): Shift items in range [new, old-1] forward by 1
                $query = $modelClass::query();
                foreach ($scopeConditions as $column => $value) {
                    if ($value === null) {
                        $query->whereNull($column);
                    } else {
                        $query->where($column, $value);
                    }
                }
                $query->where('order', '>=', $newOrder)
                    ->where('order', '<', $oldOrder)
                    ->orderBy('order', 'desc')
                    ->increment('order');
            } elseif ($newOrder > $oldOrder) {
                // Moving backward (e.g. 1 -> 3): Shift items in range [old+1, new] backward by 1
                $query = $modelClass::query();
                foreach ($scopeConditions as $column => $value) {
                    if ($value === null) {
                        $query->whereNull($column);
                    } else {
                        $query->where($column, $value);
                    }
                }
                $query->where('order', '>', $oldOrder)
                    ->where('order', '<=', $newOrder)
                    ->orderBy('order', 'asc')
                    ->decrement('order');
            }

            // 3. Finally, move the current item from the temporary position to its final newOrder
            DB::table($table)->where('id', $model->id)->update(['order' => $newOrder]);
        });
    }

    /**
     * Insert a new item at a specific order, shifting existing items forward by 1.
     * If insertOrder exceeds the current max order, cap it to max + 1 (no gaps allowed).
     *
     * @param string $modelClass      Fully qualified model class name
     * @param int    $insertOrder     The desired order for the new item
     * @param array  $scopeConditions Scope conditions, e.g. ['feature_page_id' => 5]
     * @param array  $extraAttributes Extra attributes for the new item
     * @return Model                   The newly created model instance
     */
    protected function insertAndShiftOrder(string $modelClass, int $insertOrder, array $scopeConditions, array $extraAttributes = []): Model
    {
        $query = $modelClass::query();
        foreach ($scopeConditions as $column => $value) {
            if ($value === null) {
                $query->whereNull($column);
            } else {
                $query->where($column, $value);
            }
        }

        // Get current max order
        $maxOrder = (int) $query->max('order');
        $maxPlusOne = $maxOrder + 1;

        // If insertOrder exceeds max + 1, cap it to max + 1 (no gaps allowed)
        if ($insertOrder > $maxPlusOne) {
            $insertOrder = $maxPlusOne;
        }

        // Shift all items at or after the target order forward by 1
        // We MUST use orderBy('order', 'desc') to avoid temporary unique constraint violations
        $query->where('order', '>=', $insertOrder)
            ->orderBy('order', 'desc')
            ->increment('order');

        // Create new item at target order
        return $modelClass::create(array_merge($scopeConditions, $extraAttributes, ['order' => $insertOrder]));
    }

    /**
     * Delete an item and compact remaining items sequentially to fill gaps.
     *
     * @param Model $model           The item to delete
     * @param array $scopeConditions Scope conditions, e.g. ['feature_page_id' => 5]
     * @return bool|null
     */
    protected function deleteAndShiftOrder(Model $model, array $scopeConditions): ?bool
    {
        $modelClass = get_class($model);
        $result = $model->delete();

        if ($result) {
            $query = $modelClass::query();
            foreach ($scopeConditions as $column => $value) {
                if ($value === null) {
                    $query->whereNull($column);
                } else {
                    $query->where($column, $value);
                }
            }
            $items = $query->orderBy('order')->orderBy('id')->get();

            foreach ($items as $index => $item) {
                $item->updateQuietly(['order' => $index + 1]);
            }
        }

        return $result;
    }
}
