<?php

declare(strict_types=1);

namespace App\State\Locale;

use App\Entity\Locale;
use App\ApiResource\LocaleInput;

/**
 * A processor for put locale.
 */
class LocalePutProcessor
{
    // Methodes :

    /**
     * Returns the locale filled with input datas.
     * @param \App\Entity\Locale $locale the locale to update.
     * @param \App\ApiResource\LocaleInput $localeInput the input datas.
     * @return \App\Entity\Locale the updated locale.
     */
    public function getEntity(Locale $locale, LocaleInput $localeInput): Locale
    {
        $locale->setCode($localeInput->getCode());

        return $locale;
    }
}
