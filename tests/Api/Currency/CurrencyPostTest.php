<?php

declare(strict_types=1);

namespace App\Tests\Api\Currency;

use App\Entity\Currency;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the currency POST.
 *
 * @coversDefaultClass \App\Controller\Currency\CurrencyPostController
 * @covers ::__construct
 * @covers ::post
 * @covers \App\Controller\SendErrorController::__construct
 * @uses \App\Repository\Currency\CurrencyPostRepository::__construct
 * @group api
 * @group api_currencies
 * @group api_currencies_post
 * @group currency
 */
final class CurrencyPostTest extends WebTestCase
{
    // Methods :

    /**
     * Tests that a currency can be created.
     *
     * @uses \App\ApiResource\CurrencyInput::__construct
     * @uses \App\ApiResource\CurrencyInput::getCode
     * @uses \App\ApiResource\CurrencyInput::getDecimals
     * @uses \App\ApiResource\ResourceLink::__construct
     * @uses \App\ApiResource\ResourceLink::jsonSerialize
     * @uses \App\Entity\Currency::__construct
     * @uses \App\Repository\Currency\CurrencyPostRepository::save
     * @uses \App\State\Currency\CurrencyPostProcessor::getEntity
     */
    public function testCanPostACurrency(): void
    {
        $client = static::createClient();

        $currency = [
            'code' => 'EUR',
            'decimals' => 2
        ];

        $client->request('POST', '/currencies', content: json_encode($currency));
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(201, 'POST to "/currencies" failed.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertObjectHasAttribute('link', $jsonResponse);
        self::assertSame('/currencies/1', $jsonResponse->link);
    }

    /**
     * Tests that a code of an already existing currency
     * can not be created.
     *
     * @covers \App\Controller\SendErrorController::sendViolations
     * @uses \App\ApiResource\CurrencyInput::__construct
     * @uses \App\ApiResource\CurrencyInput::getCode
     * @uses \App\ApiResource\CurrencyInput::getDecimals
     * @uses \App\ApiResource\Violation::__construct
     * @uses \App\ApiResource\Violation::jsonSerialize
     * @uses \App\ApiResource\Violations::__construct
     * @uses \App\ApiResource\Violations::add
     * @uses \App\ApiResource\Violations::jsonSerialize
     * @uses \App\Entity\Currency::__construct
     * @uses \App\State\Currency\CurrencyPostProcessor::getEntity
     */
    public function testCanNotPostACodeThatAlreadyExist(): void
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

        $sameCurrency = json_encode([
            'code' => 'EUR',
            'decimals' => 2
        ]);

        $client->request('POST', '/currencies', content: $sameCurrency);
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(409, 'POST same currency twice did not failed.');
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
     * Tests that a currency can not be created
     * from invalid json.
     *
     * @covers \App\Controller\SendErrorController::respondToBadRequest
     * @uses \App\ApiResource\ApiResponse::__construct
     * @uses \App\ApiResource\ApiResponse::jsonSerialize
     */
    public function testCanNotPostInvalidJson(): void
    {
        $params = [
            'headers' => [
                'Accept-Language' => 'en-GB',
            ],
        ];
        $client = static::createClient($params);

        $client->request('POST', '/currencies', content: 'test:');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(400, 'POST did not failed for invalid json.');
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
     * @covers \App\Controller\SendErrorController::respondToBadRequest
     * @uses \App\ApiResource\ApiResponse::__construct
     * @uses \App\ApiResource\ApiResponse::jsonSerialize
     */
    public function testCanNotPostAnEmptyBodyRequest(): void
    {
        $params = [
            'headers' => [
                'Accept-Language' => 'en-GB',
            ],
        ];
        $client = static::createClient($params);

        $client->request('POST', '/currencies', content: '[]');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(400, 'POST did not failed with an empty body request.');
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
     * can not be created.
     * @param string $property the property name.
     * @param mixed $invalidTypeValue an invalid type value.
     *
     * @covers \App\Controller\SendErrorController::respondToBadRequest
     * @uses \App\ApiResource\ApiResponse::__construct
     * @uses \App\ApiResource\ApiResponse::jsonSerialize
     * @dataProvider getInvalidTypeValues
     */
    public function testCanNotPostAnInvalidTypeValue(string $property, mixed $invalidTypeValue): void
    {
        $params = [
            'headers' => [
                'Accept-Language' => 'en-GB',
            ],
        ];
        $client = static::createClient($params);

        $currency = [
            'code' => 'EUR',
            'decimals' => 2
        ];
        $currency[$property] = $invalidTypeValue;
        $client->request('POST', '/currencies', content: json_encode($currency));
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(400, 'POST did not failed for invalid ' . $property . '.');
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
     * Returns invalid values.
     * @return array invalid values.
     */
    private function getValidTypeInvalidValues(): array
    {
        return [
            'code is not currency (aaa_AAA_aaa)' => ['code', 'aaa_AAA_aaa'],
            'decimal is negative (-2)' => ['decimals', -2]
        ];
    }

    /**
     * Tests that an invalid value
     * can not be created.
     * @param string $property the property name.
     * @param mixed $value the value.
     *
     * @covers \App\Controller\SendErrorController::sendViolations
     * @uses \App\ApiResource\CurrencyInput::__construct
     * @uses \App\ApiResource\Violation::__construct
     * @uses \App\ApiResource\Violation::jsonSerialize
     * @uses \App\ApiResource\Violations::__construct
     * @uses \App\ApiResource\Violations::add
     * @uses \App\ApiResource\Violations::jsonSerialize
     * @dataProvider getValidTypeInvalidValues
     */
    public function testCanNotPostAValidTypeInvalidValue(string $property, mixed $value): void
    {
        $params = [
            'headers' => [
                'Accept-Language' => 'en-GB',
            ],
        ];
        $client = static::createClient($params);

        $currency = [
            'code' => 'EUR',
            'decimals' => 2
        ];
        $currency[$property] = $value;
        $client->request('POST', '/currencies', content: json_encode($currency));
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(422, 'POST did not failed for invalid code.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertCount(1, $jsonResponse, 'There must be one violation.');
        self::assertArrayHasKey(0, $jsonResponse);
        self::assertObjectHasAttribute('property', $jsonResponse[0]);
        self::assertObjectHasAttribute('message', $jsonResponse[0]);
        self::assertSame($property, $jsonResponse[0]->property);
        self::assertNotSame(
            '',
            $jsonResponse[0]->message,
            'The violation message is empty.'
        );
    }


    /**
     * Tests that a currency can not be created
     * with a blank code.
     *
     * @covers \App\Controller\SendErrorController::sendViolations
     * @uses \App\ApiResource\CurrencyInput::__construct
     * @uses \App\ApiResource\Violation::__construct
     * @uses \App\ApiResource\Violation::jsonSerialize
     * @uses \App\ApiResource\Violations::__construct
     * @uses \App\ApiResource\Violations::add
     * @uses \App\ApiResource\Violations::jsonSerialize
     */
    public function testCanNotPostABlankCode(): void
    {
        $params = [
            'headers' => [
                'Accept-Language' => 'en-GB',
            ],
        ];
        $client = static::createClient($params);

        $currency = [
            'code' => '',
            'decimals' => 2
        ];

        $client->request('POST', '/currencies', content: json_encode($currency));
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(422, 'POST did not failed for invalid code.');
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
