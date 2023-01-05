<?php

declare(strict_types=1);

namespace App\State\Locale;

use App\Entity\Locale;
use App\ApiResource\LocaleInput;

/**
 * A processor for post locale.
 */
class LocalePostProcessor
{
    // Methodes :

    /**
     * Returns a new locale filled with input datas.
     * @param \App\ApiResource\LocaleInput $localeInput the input datas.
     * @return \App\Entity\Locale the locale.
     */
    public function getEntity(LocaleInput $localeInput): Locale
    {
        return new Locale($localeInput->getCode());
    }
}
