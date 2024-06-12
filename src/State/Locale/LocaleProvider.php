<?php

declare(strict_types=1);

namespace App\State\Locale;

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
     * @throws \DomainException if the locale has a null id.
     */
    public function provideLocaleOutput(Locale $locale): LocaleOutput
    {
        $id = $locale->getId();

        if ($id === null) {
            throw new \DomainException('entityNotPersisted');
        }

        return new LocaleOutput(
            $id,
            $locale->getCode()
        );
    }
}
