<?php

namespace App\Services;

use App\Repositories\PromotionRepository;
use App\Services\BaseService;

class PromotionService extends BaseService
{
    /**
     * PromotionService constructor.
     *
     * @param PromotionRepository $repository
     */
    public function __construct(
        protected PromotionRepository $repository
    ) {}

    /**
     * Get active promotions.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActivePromotions()
    {
        $now = now();
        
        return $this->repository->findBy('is_active', true)
            ->filter(function ($promotion) use ($now) {
                $startValid = !$promotion->start_date || $promotion->start_date <= $now;
                $endValid = !$promotion->end_date || $promotion->end_date >= $now;
                return $startValid && $endValid;
            });
    }

    /**
     * Apply promotion discount to price.
     *
     * @param float $price
     * @param int|null $promotionId
     * @param int|null $categoryId
     * @return float
     */
    public function applyPromotion(float $price, ?int $promotionId = null, ?int $categoryId = null): float
    {
        $promotion = null;

        if ($promotionId) {
            $promotion = $this->repository->getById($promotionId);
        } elseif ($categoryId) {
            $promotions = $this->repository->findBy('category_id', $categoryId);
            $promotion = $promotions->where('is_active', true)->first();
        }

        if (!$promotion || !$promotion->is_active) {
            return $price;
        }

        // Check if minimum purchase amount is met
        if ($promotion->min_purchase_amount > 0 && $price < $promotion->min_purchase_amount) {
            return $price;
        }

        // Check date validity
        $now = now();
        if ($promotion->start_date && $promotion->start_date > $now) {
            return $price;
        }
        if ($promotion->end_date && $promotion->end_date < $now) {
            return $price;
        }

        // Apply discount
        if ($promotion->discount_type === 'percentage') {
            $discount = $price * ($promotion->discount_value / 100);
        } else {
            // Fixed amount
            $discount = $promotion->discount_value;
        }

        return max(0, $price - $discount);
    }

    /**
     * Handle the service logic (required by BaseService).
     *
     * @param mixed ...$args
     * @return mixed
     */
    public function handle(...$args)
    {
        return $this->getActivePromotions();
    }
}

