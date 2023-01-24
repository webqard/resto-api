<?php

declare(strict_types=1);

namespace App\Repository\Locale;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * Repository for the get method of the Locale entity.
 */
class LocaleGetRepository extends ServiceEntityRepository
{
    // Traits :

    use LocaleRepository;
}
