<?php

declare(strict_types=1);

namespace App\State;

use App\ApiResource\LocaleOutput;
use App\Entity\Locale;

/**
 * A locale provider.
 */
class LocaleProvider
{
    // Methodes :

    /**
     * Returns a locale to output.
     * @param \App\Entity\Locale $locale the locale.
     * @return \App\ApiResource\LocaleOutput the locale to output.
     */
    public function provideLocaleOutput(Locale $locale): LocaleOutput
    {
        return new LocaleOutput($locale->getCode());
    }
}
