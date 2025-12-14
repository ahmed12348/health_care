<?php

namespace App\Services;

abstract class BaseService
{
    /**
     * Handle the service logic.
     *
     * @param mixed ...$args
     * @return mixed
     */
    abstract public function handle(...$args);
}

