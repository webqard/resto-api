<?php

declare(strict_types=1);

namespace App\Tests\State\Currency;

use App\ApiResource\CurrencyInput;
use App\Entity\Currency;
use App\State\Currency\CurrencyPostProcessor;
use PHPUnit\Framework\Attributes as PA;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Tests the currency post processor.
 */
#[
    PA\CoversClass(CurrencyPostProcessor::class),
    PA\UsesClass(Currency::class),
    PA\UsesClass(CurrencyInput::class),
    PA\Group('state'),
    PA\Group('state_currencyPostProcessor'),
    PA\Group('currency')
]
final class CurrencyPostProcessorTest extends KernelTestCase
{
    // Methods :

    /**
     * Test that the entity can be returned.
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
