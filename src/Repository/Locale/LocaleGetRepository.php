<?php

declare(strict_types=1);

namespace App\Repository\Locale;

use App\Entity\Locale;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * Repository for the get method of the Locale entity.
 */
class LocaleGetRepository extends ServiceEntityRepository
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
}
