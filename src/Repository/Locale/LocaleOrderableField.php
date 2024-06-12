<?php

declare(strict_types=1);

namespace App\Repository\Locale;

/**
 * Queryable fields for locales.
 */
enum LocaleOrderableField: string
{
    case Id = 'id';
    case Code = 'code';
}
