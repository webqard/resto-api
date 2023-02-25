<?php

declare(strict_types=1);

namespace App\Tests\State\Locale;

use App\ApiResource\LocaleInput;
use App\Entity\Locale;
use App\State\Locale\LocalePutProcessor;
use PHPUnit\Framework\Attributes as PA;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Tests the locale put processor.
 */
#[
    PA\CoversClass(LocalePutProcessor::class),
    PA\UsesClass(Locale::class),
    PA\UsesClass(LocaleInput::class),
    PA\Group('state'),
    PA\Group('state_localePutProcessor'),
    PA\Group('locale')
]
final class LocalePutProcessorTest extends KernelTestCase
{
    // Methods :

    /**
     * Test that the entity can be returned.
     */
    public function testCanGetEntity(): void
    {
        $localePutProcessor = new LocalePutProcessor();
        $locale = new Locale('en_GB');
        $localeInput = new LocaleInput('fr_FR');

        $updatedLocale = $localePutProcessor->getEntity($locale, $localeInput);

        self::assertInstanceOf(Locale::class, $updatedLocale);
        self::assertSame('fr_FR', $updatedLocale->getCode());
    }
}
