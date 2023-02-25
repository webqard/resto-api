<?php

declare(strict_types=1);

namespace App\Tests\State\Currency;

use App\ApiResource\CurrencyInput;
use App\Entity\Currency;
use App\State\Currency\CurrencyPutProcessor;
use PHPUnit\Framework\Attributes as PA;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Tests the currency put processor.
 */
#[
    PA\CoversClass(CurrencyPutProcessor::class),
    PA\UsesClass(Currency::class),
    PA\UsesClass(CurrencyInput::class),
    PA\Group('state'),
    PA\Group('state_currencyPutProcessor'),
    PA\Group('currency')
]
final class CurrencyPutProcessorTest extends KernelTestCase
{
    // Methods :

    /**
     * Test that the entity can be returned.
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
