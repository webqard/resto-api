<?php

declare(strict_types=1);

namespace App\Tests\State\Locale;

use App\ApiResource\LocaleInput;
use App\Entity\Locale;
use App\State\Locale\LocalePostProcessor;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Tests the locale post processor.
 *
 * @coversDefaultClass \App\State\Locale\LocalePostProcessor
 * @group state
 * @group state_localePostProcessor
 * @group locale
 */
final class LocalePostProcessorTest extends KernelTestCase
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
