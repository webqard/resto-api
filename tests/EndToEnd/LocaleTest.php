<?php

declare(strict_types=1);

namespace App\Tests\EndToEnd;

use App\Entity\Locale;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the database CRUD for the locale.
 *
 * @uses \App\Entity\Locale::__construct
 * @group e2e
 * @group e2e_locale
 * @group locale
 */
final class LocaleTest extends WebTestCase
{
    // Methods :

    /**
     * Tests that a locale can be created in the database.
     *
     * @covers \App\Controller\Locale\LocalePostController::post
     * @uses \App\ApiResource\LocaleInput::__construct
     * @uses \App\ApiResource\LocaleInput::getCode
     * @uses \App\ApiResource\LocaleOutput::__construct
     * @uses \App\ApiResource\LocaleOutput::jsonSerialize
     * @uses \App\ApiResource\ResourceLink::__construct
     * @uses \App\ApiResource\ResourceLink::jsonSerialize
     * @uses \App\Controller\Locale\LocaleGetController::__construct
     * @uses \App\Controller\Locale\LocaleGetController::get
     * @uses \App\Controller\Locale\LocalePostController::__construct
     * @uses \App\Entity\Property\Code::getCode
     * @uses \App\Repository\Locale\LocaleGetRepository::__construct
     * @uses \App\Repository\Locale\LocalePostRepository::__construct
     * @uses \App\Repository\Locale\LocalePostRepository::save
     * @uses \App\State\Locale\LocalePostProcessor::getEntity
     * @uses \App\State\Locale\LocaleProvider::provideLocaleOutput
     */
    public function testIsCreatedInTheDatabaseWithPost(): void
    {
        $params = [
            'headers' => [
                'Accept-Language' => 'en-GB',
            ],
        ];
        $client = static::createClient($params);

        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $noLocale = $entityManager->find(Locale::class, 1);

        self::assertNull($noLocale);

        $localeToPost = [
            'code' => 'en_GB'
        ];

        $client->request('POST', '/locales', content: json_encode($localeToPost));
        $postResponse = $client->getResponse()->getContent();
        $jsonPostResponse = json_decode($postResponse, false);

        $client->request('GET', $jsonPostResponse->link);
        $getResponse = $client->getResponse()->getContent();
        $jsonGetResponse = json_decode($getResponse, false);

        $localeLink = explode('/', $jsonPostResponse->link);
        $localeLinkLastKey = array_key_last($localeLink);
        $localeLinkId = (int)$localeLink[$localeLinkLastKey];

        $postedLocale = $entityManager->find(Locale::class, 1);
        $entityManager->refresh($postedLocale);

        self::assertSame(1, $localeLinkId);
        self::assertSame(1, $postedLocale->getId());
        self::assertSame('en_GB', $jsonGetResponse->code);
        self::assertSame('en_GB', $postedLocale->getCode());
    }


    /**
     * Tests that a locale can be updated in the database.
     *
     * @covers \App\Controller\Locale\LocalePutController::put
     * @uses \App\ApiResource\LocaleInput::__construct
     * @uses \App\ApiResource\LocaleInput::getCode
     * @uses \App\ApiResource\LocaleOutput::__construct
     * @uses \App\ApiResource\LocaleOutput::jsonSerialize
     * @uses \App\Controller\Locale\LocaleGetController::__construct
     * @uses \App\Controller\Locale\LocaleGetController::get
     * @uses \App\Controller\Locale\LocalePutController::__construct
     * @uses \App\Entity\Property\Code::getCode
     * @uses \App\Entity\Property\Code::setCode
     * @uses \App\Repository\Locale\LocaleGetRepository::__construct
     * @uses \App\Repository\Locale\LocalePutRepository::__construct
     * @uses \App\Repository\Locale\LocalePutRepository::save
     * @uses \App\State\Locale\LocaleProvider::provideLocaleOutput
     * @uses \App\State\Locale\LocalePutProcessor::getEntity
     */
    public function testIsUpdatedInTheDatabaseWithPut(): void
    {
        $params = [
            'headers' => [
                'Accept-Language' => 'en-GB',
            ],
        ];
        $client = static::createClient($params);

        $locale = new Locale('en_GB');
        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($locale);
        $entityManager->flush();

        self::assertSame(1, $locale->getId());

        $client->request('GET', '/locales/1');
        $getResponse = $client->getResponse()->getContent();
        $jsonGetResponse = json_decode($getResponse, false);

        self::assertSame('en_GB', $jsonGetResponse->code);


        $localeToPut = [
            'code' => 'fr_FR'
        ];
        $client->request('PUT', '/locales/1', content: json_encode($localeToPut));


        $client->request('GET', '/locales/1');
        $getResponseAfterPut = $client->getResponse()->getContent();
        $jsonGetResponseAfterPut = json_decode($getResponseAfterPut, false);

        self::assertSame('fr_FR', $jsonGetResponseAfterPut->code);

        $localeAfterPut = $entityManager->find(Locale::class, 1);
        $entityManager->refresh($localeAfterPut);
        self::assertSame(1, $localeAfterPut->getId());
        self::assertSame('fr_FR', $localeAfterPut->getCode());
    }


    /**
     * Tests that a locale can be deleted in the database.
     *
     * @covers \App\Controller\Locale\LocaleDeleteController::delete
     * @uses \App\Controller\Locale\LocaleDeleteController::__construct
     * @uses \App\Repository\Locale\LocaleDeleteRepository::__construct
     * @uses \App\Repository\Locale\LocaleDeleteRepository::delete
     */
    public function testIsDeletedInTheDatabaseWithDelete(): void
    {
        $params = [
            'headers' => [
                'Accept-Language' => 'en-GB',
            ],
        ];
        $client = static::createClient($params);

        $locale = new Locale('en_GB');
        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($locale);
        $entityManager->flush();

        self::assertSame(1, $locale->getId());

        $client->request('DELETE', '/locales/1');

        $localeAfterDelete = $entityManager->find(Locale::class, 1);

        self::assertNull($localeAfterDelete, 'The Locale has not been deleted.');
    }
}
