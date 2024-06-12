<?php

declare(strict_types=1);

namespace App\Repository\Locale;

/**
 * Queryable fields for locales.
 */
enum LocaleQueryableField: string
{
    case Id = 'id';
    case Code = 'code';
}
