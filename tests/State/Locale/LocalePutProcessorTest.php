<?php

declare(strict_types=1);

namespace App\Tests\State\Locale;

use App\ApiResource\LocaleInput;
use App\Entity\Locale;
use App\State\Locale\LocalePutProcessor;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Tests the locale put processor.
 *
 * @coversDefaultClass \App\State\Locale\LocalePutProcessor
 * @group state
 * @group state_localePutProcessor
 * @group locale
 */
final class LocalePutProcessorTest extends KernelTestCase
{
    // Methods :

    /**
     * Test that the entity can be returned.
     *
     * @covers ::getEntity
     * @uses \App\ApiResource\LocaleInput::__construct
     * @uses \App\ApiResource\LocaleInput::getCode
     * @uses \App\Entity\Locale::__construct
     * @uses \App\Entity\Property\Code::getCode
     * @uses \App\Entity\Property\Code::setCode
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
