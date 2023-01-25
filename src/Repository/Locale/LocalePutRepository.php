<?php

declare(strict_types=1);

namespace App\Repository\Locale;

use App\Entity\Locale;

/**
 * Repository for the put method of the Locale entity.
 */
class LocalePutRepository extends LocaleGetRepository
{
    // Methods :

    /**
     * Saves a locale.
     * @param \App\Entity\Locale $locale the locale.
     */
    public function save(Locale $locale): void
    {
        $this->_em->persist($locale);
        $this->_em->flush();
    }
}
