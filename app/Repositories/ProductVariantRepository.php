<?php

namespace App\Repositories;

use App\Models\ProductVariant;
use App\Repositories\BaseRepository;

class ProductVariantRepository extends BaseRepository
{
    /**
     * ProductVariantRepository constructor.
     *
     * @param ProductVariant $model
     */
    public function __construct(ProductVariant $model)
    {
        parent::__construct($model);
    }

    /**
     * Get variants by product ID.
     *
     * @param int $productId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByProductId(int $productId)
    {
        return $this->model->where('product_id', $productId)->get();
    }
}

