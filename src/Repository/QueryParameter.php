<?php

declare(strict_types=1);

namespace App\Repository;

/**
 * Directions for sorting.
 */
enum QueryParameter: string
{
    case Criteria = 'criteria';
    case OrderBy = 'orderBy';
    case Limit = 'limit';
    case Offset = 'offset';
}
