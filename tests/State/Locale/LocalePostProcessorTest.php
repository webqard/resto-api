<?php

declare(strict_types=1);

namespace App\Tests\State\Locale;

use App\ApiResource\LocaleInput;
use App\Entity\Locale;
use App\State\Locale\LocalePostProcessor;
use PHPUnit\Framework\Attributes as PA;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Tests the locale post processor.
 */
#[
    PA\CoversClass(LocalePostProcessor::class),
    PA\UsesClass(Locale::class),
    PA\UsesClass(LocaleInput::class),
    PA\Group('state'),
    PA\Group('state_localePostProcessor'),
    PA\Group('locale')
]
final class LocalePostProcessorTest extends KernelTestCase
{
    // Methods :

    /**
     * Test that the entity can be returned.
     */
    public function testCanGetEntity(): void
    {
        $localePostProcessor = new LocalePostProcessor();
        $localeInput = new LocaleInput('en_GB');

        $locale = $localePostProcessor->getEntity($localeInput);

        self::assertInstanceOf(Locale::class, $locale);
        self::assertSame('en_GB', $locale->getCode());
    }
}
