<?php

declare(strict_types=1);

namespace App\Repository;

/**
 * Directions for sorting.
 */
enum OrderableDirection: string
{
    /**
     * Ascending.
     */
    case Ascending = 'ASC';

    /**
     * Descending.
     */
    case Descending = 'DESC';
}
