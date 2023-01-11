<?php

declare(strict_types=1);

namespace App\Tests\State\Currency;

use App\ApiResource\CurrencyInput;
use App\Entity\Currency;
use App\State\Currency\CurrencyPutProcessor;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Tests the currency put processor.
 *
 * @coversDefaultClass \App\State\Currency\CurrencyPutProcessor
 * @group state
 * @group state_currencyPutProcessor
 * @group currency
 */
final class CurrencyPutProcessorTest extends KernelTestCase
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
     * @uses \App\Entity\Currency::setDecimals
     * @uses \App\Entity\Property\Code::getCode
     * @uses \App\Entity\Property\Code::setCode
     */
    public function testCanGetEntity(): void
    {
        $currencyPutProcessor = new CurrencyPutProcessor();
        $currency = new Currency('EUR', 2);
        $currencyInput = new CurrencyInput('GBP', 3);

        $updatedCurrency = $currencyPutProcessor->getEntity($currency, $currencyInput);

        self::assertInstanceOf(Currency::class, $updatedCurrency);
        self::assertSame('GBP', $updatedCurrency->getCode());
        self::assertSame(3, $updatedCurrency->getDecimals());
    }
}
