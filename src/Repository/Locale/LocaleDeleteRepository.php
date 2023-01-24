<?php

declare(strict_types=1);

namespace App\Repository\Locale;

use App\Repository\DeleteRepository;

/**
 * Repository for the delete method of the Locale entity.
 */
class LocaleDeleteRepository extends DeleteRepository
{
    // Traits :

    use LocaleRepository;
}
