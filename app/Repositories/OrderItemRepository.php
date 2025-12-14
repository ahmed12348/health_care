<?php

namespace App\Repositories;

use App\Models\OrderItem;

class OrderItemRepository extends BaseRepository
{
    /**
     * OrderItemRepository constructor.
     *
     * @param OrderItem $model
     */
    public function __construct(OrderItem $model)
    {
        parent::__construct($model);
    }

    /**
     * Get order items by order ID.
     *
     * @param int $orderId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByOrderId(int $orderId)
    {
        return $this->model->where('order_id', $orderId)->get();
    }

    /**
     * Delete all items for an order.
     *
     * @param int $orderId
     * @return bool
     */
    public function deleteByOrderId(int $orderId)
    {
        return $this->model->where('order_id', $orderId)->delete();
    }
}

