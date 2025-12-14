<?php

namespace App\Repositories;

use App\Models\Promotion;
use App\Repositories\BaseRepository;

class PromotionRepository extends BaseRepository
{
    /**
     * PromotionRepository constructor.
     *
     * @param Promotion $model
     */
    public function __construct(Promotion $model)
    {
        parent::__construct($model);
    }
}

