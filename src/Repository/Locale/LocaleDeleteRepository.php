<?php

declare(strict_types=1);

namespace App\Repository\Locale;

use App\Entity\Locale;

/**
 * Repository for the delete method of the Locale entity.
 */
class LocaleDeleteRepository extends LocaleGetRepository
{
    // Methods :

    /**
     * Deletes a locale.
     * @param \App\Entity\Locale $locale the locale.
     */
    public function delete(Locale $locale): void
    {
        $this->_em->remove($locale);
        $this->_em->flush();
    }
}
