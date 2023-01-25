<?php

declare(strict_types=1);

namespace App\Tests\Api\Currency;

use App\Entity\Currency;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the currency PUT.
 *
 * @coversDefaultClass \App\Controller\Currency\CurrencyPutController
 * @covers ::__construct
 * @covers ::put
 * @uses \App\Repository\Currency\CurrencyGetRepository::find
 * @uses \App\Repository\Currency\CurrencyPutRepository::__construct
 * @group api
 * @group api_currencies
 * @group api_currencies_put
 * @group currency
 */
final class CurrencyPutTest extends WebTestCase
{
    // Methods :

    /**
     * Tests that a currency can be updated.
     *
     * @uses \App\ApiResource\CurrencyInput::__construct
     * @uses \App\ApiResource\CurrencyInput::getCode
     * @uses \App\ApiResource\CurrencyInput::getDecimals
     * @uses \App\Entity\Currency::__construct
     * @uses \App\Entity\Currency::setDecimals
     * @uses \App\Entity\Property\Code::getCode
     * @uses \App\Entity\Property\Code::setCode
     * @uses \App\Repository\Currency\CurrencyPutRepository::save
     * @uses \App\State\Currency\CurrencyPutProcessor::getEntity
     */
    public function testCanPutACurrency(): void
    {
        $params = [
            'headers' => [
                'Accept-Language' => 'en-GB',
            ],
        ];
        $client = static::createClient($params);
        $currency = new Currency('EUR', 2);

        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($currency);
        $entityManager->flush();

        $currencyToPut = [
            'code' => 'GBP',
            'decimals' => 3
        ];

        $client->request('PUT', '/currencies/1', content: json_encode($currencyToPut));

        self::assertResponseStatusCodeSame(204, 'PUT to "/currencies/1" failed.');

        $apiResponse = $client->getResponse()->getContent();

        self::assertEmpty($apiResponse, 'The PUT success must not return a body.');
    }

    /**
     * Tests that a currency can not be updated
     * with the code of an other one.
     *
     * @covers ::sendViolations
     * @uses \App\ApiResource\CurrencyInput::__construct
     * @uses \App\ApiResource\CurrencyInput::getCode
     * @uses \App\ApiResource\CurrencyInput::getDecimals
     * @uses \App\ApiResource\Violation::__construct
     * @uses \App\ApiResource\Violation::jsonSerialize
     * @uses \App\ApiResource\Violations::__construct
     * @uses \App\ApiResource\Violations::add
     * @uses \App\ApiResource\Violations::jsonSerialize
     * @uses \App\Controller\Currency\CurrencyPutController::sendViolations
     * @uses \App\Entity\Currency::__construct
     * @uses \App\Entity\Currency::setDecimals
     * @uses \App\Entity\Property\Code::setCode
     * @uses \App\State\Currency\CurrencyPutProcessor::getEntity
     */
    public function testCanNotPutACurrencyWithTheCodeOfAnOtherOne(): void
    {
        $params = [
            'headers' => [
                'Accept-Language' => 'en-GB',
            ],
        ];
        $client = static::createClient($params);
        $currencyEUR = new Currency('EUR', 2);
        $currencyGBP = new Currency('GBP', 2);

        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($currencyEUR);
        $entityManager->persist($currencyGBP);
        $entityManager->flush();

        $sameCurrency = json_encode([
            'code' => 'GBP',
            'decimals' => 2
        ]);

        $client->request('PUT', '/currencies/1', content: $sameCurrency);
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(409, 'PUT same currency twice did not failed.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertCount(1, $jsonResponse, 'There must be one violation.');
        self::assertArrayHasKey(0, $jsonResponse);
        self::assertSame('code', $jsonResponse[0]->property);
        self::assertNotSame(
            '',
            $jsonResponse[0]->message,
            'The violation message is empty.'
        );
    }


    /**
     * Tests that a currency can not be updated
     * from an non existant identifier.
     *
     * @uses \App\ApiResource\ApiResponse::__construct
     * @uses \App\ApiResource\ApiResponse::jsonSerialize
     */
    public function testCanNotPutACurrencyFromAnNonExistantId(): void
    {
        $requestParameters = [
            'headers' => [
                'Accept-Language' => 'en-GB',
            ],
        ];
        $client = static::createClient($requestParameters);

        $currency = [
            'code' => 'EUR',
            'decimals' => 2
        ];

        $client->request('PUT', '/currencies/1', content: json_encode($currency));
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(404, 'PUT "/currencies/1" did not failed.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertObjectHasAttribute('message', $jsonResponse);
        self::assertNotSame('', $jsonResponse->message, 'The error message is empty.');
    }


    /**
     * Tests that a currency can not be updated
     * from invalid json.
     *
     * @covers ::respondToBadRequest
     * @uses \App\ApiResource\ApiResponse::__construct
     * @uses \App\ApiResource\ApiResponse::jsonSerialize
     * @uses \App\Entity\Currency::__construct
     */
    public function testCanNotPutInvalidJson(): void
    {
        $params = [
            'headers' => [
                'Accept-Language' => 'en-GB',
            ],
        ];
        $client = static::createClient($params);
        $currency = new Currency('EUR', 2);

        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($currency);
        $entityManager->flush();

        $client->request('PUT', '/currencies/1', content: 'test:');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(400, 'PUT did not failed for invalid json.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertObjectHasAttribute('message', $jsonResponse);
        self::assertNotSame(
            '',
            $jsonResponse->message,
            'The error message is empty.'
        );
    }


    /**
     * Tests that an empty body request
     * can not create a currency.
     *
     * @covers ::respondToBadRequest
     * @uses \App\ApiResource\ApiResponse::__construct
     * @uses \App\ApiResource\ApiResponse::jsonSerialize
     * @uses \App\Entity\Currency::__construct
     */
    public function testCanNotPutAnEmptyBodyRequest(): void
    {
        $params = [
            'headers' => [
                'Accept-Language' => 'en-GB',
            ],
        ];
        $client = static::createClient($params);
        $currency = new Currency('EUR', 2);

        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($currency);
        $entityManager->flush();

        $client->request('PUT', '/currencies/1', content: '[]');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(400, 'PUT did not failed with an empty body request.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertObjectHasAttribute('message', $jsonResponse);
        self::assertNotSame(
            '',
            $jsonResponse->message,
            'There is no error message.'
        );
    }


    /**
     * Returns invalid type values.
     * @return array invalid type values.
     */
    private function getInvalidTypeValues(): array
    {
        return [
            'code is null' => ['code', null],
            'code is an integer (5)' => ['code', 5],
            'code is an array ([])' => ['code', []],
            'decimal is null' => ['decimals', null],
            'decimal is a string ("test")' => ['decimals', 'test'],
            'decimal is a float (1.5)' => ['decimals', 1.5],
            'decimal is an array ([])' => ['decimals', []]
        ];
    }

    /**
     * Tests that an invalid type value
     * can not be updated.
     * @param string $property the property name.
     * @param mixed $invalidTypeValue an invalid type value.
     *
     * @covers ::respondToBadRequest
     * @uses \App\ApiResource\ApiResponse::__construct
     * @uses \App\ApiResource\ApiResponse::jsonSerialize
     * @uses \App\Entity\Currency::__construct
     * @dataProvider getInvalidTypeValues
     */
    public function testCanNotPutAnInvalidTypeValue(string $property, mixed $invalidTypeValue): void
    {
        $params = [
            'headers' => [
                'Accept-Language' => 'en-GB',
            ],
        ];
        $client = static::createClient($params);
        $currency = new Currency('EUR', 2);

        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($currency);
        $entityManager->flush();

        $currencyWithInvalidTypeCode = [
            'code' => 'GBP',
            'decimals' => 3
        ];
        $currencyWithInvalidTypeCode[$property] = $invalidTypeValue;

        $client->request('PUT', '/currencies/1', content: json_encode($currencyWithInvalidTypeCode));
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(400, 'PUT did not failed for invalid ' . $property . '.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertObjectHasAttribute('message', $jsonResponse);
        self::assertNotSame(
            '',
            $jsonResponse->message,
            'There is no error message.'
        );
    }


    /**
     * Tests that an invalid code
     * can not be updated.
     *
     * @covers ::sendViolations
     * @uses \App\ApiResource\CurrencyInput::__construct
     * @uses \App\ApiResource\Violation::__construct
     * @uses \App\ApiResource\Violation::jsonSerialize
     * @uses \App\ApiResource\Violations::__construct
     * @uses \App\ApiResource\Violations::add
     * @uses \App\ApiResource\Violations::jsonSerialize
     * @uses \App\Entity\Currency::__construct
     */
    public function testCanNotPutAnInvalidCode(): void
    {
        $params = [
            'headers' => [
                'Accept-Language' => 'en-GB',
            ],
        ];
        $client = static::createClient($params);
        $currency = new Currency('EUR', 2);

        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($currency);
        $entityManager->flush();

        $currencyWithInvalidCode = [
            'code' => 'aaa_AAA_aaa',
            'decimals' => 2
        ];

        $client->request('PUT', '/currencies/1', content: json_encode($currencyWithInvalidCode));
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(422, 'PUT did not failed for invalid code.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertCount(1, $jsonResponse, 'There must be one violation.');
        self::assertArrayHasKey(0, $jsonResponse);
        self::assertObjectHasAttribute('property', $jsonResponse[0]);
        self::assertObjectHasAttribute('message', $jsonResponse[0]);
        self::assertSame('code', $jsonResponse[0]->property);
        self::assertNotSame(
            '',
            $jsonResponse[0]->message,
            'The violation message is empty.'
        );
    }


    /**
     * Tests that a currency can not be updated
     * with a blank code.
     *
     * @covers ::sendViolations
     * @uses \App\ApiResource\CurrencyInput::__construct
     * @uses \App\ApiResource\Violation::__construct
     * @uses \App\ApiResource\Violation::jsonSerialize
     * @uses \App\ApiResource\Violations::__construct
     * @uses \App\ApiResource\Violations::add
     * @uses \App\ApiResource\Violations::jsonSerialize
     * @uses \App\Entity\Currency::__construct
     */
    public function testCanNotPutABlankCode(): void
    {
        $params = [
            'headers' => [
                'Accept-Language' => 'en-GB',
            ],
        ];
        $client = static::createClient($params);
        $currency = new Currency('EUR', 2);

        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($currency);
        $entityManager->flush();

        $currencyWithABlankCode = [
            'code' => '',
            'decimals' => 2
        ];

        $client->request('PUT', '/currencies/1', content: json_encode($currencyWithABlankCode));
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(422, 'PUT did not failed for invalid code.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertCount(1, $jsonResponse, 'There must be one violation.');
        self::assertArrayHasKey(0, $jsonResponse);
        self::assertObjectHasAttribute('property', $jsonResponse[0]);
        self::assertObjectHasAttribute('message', $jsonResponse[0]);
        self::assertSame('code', $jsonResponse[0]->property);
        self::assertNotSame(
            '',
            $jsonResponse[0]->message,
            'The violation message is empty.'
        );
    }
}
