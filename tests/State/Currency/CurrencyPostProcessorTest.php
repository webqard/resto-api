<?php

declare(strict_types=1);

namespace App\Tests\State\Currency;

use App\ApiResource\CurrencyInput;
use App\Entity\Currency;
use App\State\Currency\CurrencyPostProcessor;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Tests the currency post processor.
 *
 * @coversDefaultClass \App\State\Currency\CurrencyPostProcessor
 * @group state
 * @group state_currencyPostProcessor
 * @group currency
 */
final class CurrencyPostProcessorTest extends KernelTestCase
{
    // Methods :

    /**
     * Test that the entity can be returned.
     *
     * @covers ::getEntity
     * @uses \App\ApiResource\CurrencyInput::__construct
     * @uses \App\ApiResource\CurrencyInput::getCode
     * @uses \App\ApiResource\CurrencyInput::getDecimals
     * @uses \App\Entity\Currency::__construct
     * @uses \App\Entity\Currency::getDecimals
     * @uses \App\Entity\Property\Code::getCode
     */
    public function testCanGetEntity(): void
    {
        $currencyPostProcessor = new CurrencyPostProcessor();
        $currencyInput = new CurrencyInput('EUR', 2);

        $currency = $currencyPostProcessor->getEntity($currencyInput);

        self::assertInstanceOf(Currency::class, $currency);
        self::assertSame('EUR', $currency->getCode());
        self::assertSame(2, $currency->getDecimals());
    }
}
