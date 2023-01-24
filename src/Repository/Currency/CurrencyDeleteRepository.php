<?php

declare(strict_types=1);

namespace App\Repository\Currency;

use App\Repository\DeleteRepository;

/**
 * Repository for the delete method of the Currency entity.
 */
class CurrencyDeleteRepository extends DeleteRepository
{
    // Traits :

    use CurrencyRepository;
}
