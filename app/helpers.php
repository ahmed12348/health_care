<?php

if (!function_exists('__t')) {
    /**
     * Translation helper function.
     *
     * @param string $key
     * @param array $replace
     * @return string
     */
    function __t(string $key, array $replace = []): string
    {
        return \App\Helpers\TranslationHelper::trans($key, $replace);
    }
}

