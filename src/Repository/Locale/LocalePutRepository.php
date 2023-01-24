<?php

declare(strict_types=1);

namespace App\Repository\Locale;

use App\Entity\Locale;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * Repository for the put method of the Locale entity.
 */
class LocalePutRepository extends ServiceEntityRepository
{
    // Traits :

    use LocaleRepository;


    // Methods :

    /**
     * @return \App\Entity\Locale|null a locale.
     */
    public function find($id, $lockMode = null, $lockVersion = null): ?Locale
    {
        return parent::find($id, $lockMode, $lockVersion);
    }

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
