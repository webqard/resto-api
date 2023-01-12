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
     * @uses \App\ApiResource\ResourceLink::__construct
     * @uses \App\ApiResource\ResourceLink::jsonSerialize
     * @uses \App\Controller\Locale\LocalePostController::__construct
     * @uses \App\Entity\Property\Code::getCode
     * @uses \App\Repository\Locale\LocalePostRepository::__construct
     * @uses \App\Repository\Locale\LocalePostRepository::save
     * @uses \App\State\Locale\LocalePostProcessor::getEntity
     */
    public function testIsCreatedInTheDatabaseWithPost(): void
    {
        $params = [
            'headers' => [
                'Accept-Language' => 'en-GB',
            ],
        ];
        $client = static::createClient($params);

        $localeToPost = [
            'code' => 'en_GB'
        ];

        $client->request('POST', '/locales', content: json_encode($localeToPost));
        $jsonResponse = json_decode($client->getResponse()->getContent(), false);

        $localeLink = explode('/', $jsonResponse->link);
        $localeLinkLastKey = array_key_last($localeLink);
        $localeLinkId = (int)$localeLink[$localeLinkLastKey];

        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $postedLocale = $entityManager->find(Locale::class, $localeLinkId);

        self::assertSame($localeLinkId, $postedLocale->getId());
        self::assertSame('en_GB', $postedLocale->getCode());
    }


    /**
     * Tests that a locale can be read from the database.
     *
     * @covers \App\Controller\Locale\LocaleGetController::get
     * @uses \App\ApiResource\LocaleOutput::__construct
     * @uses \App\ApiResource\LocaleOutput::jsonSerialize
     * @uses \App\Controller\Locale\LocaleGetController::__construct
     * @uses \App\Entity\Property\Code::getCode
     * @uses \App\Repository\Locale\LocaleGetRepository::__construct
     * @uses \App\State\Locale\LocaleProvider::provideLocaleOutput
     */
    public function testIsReadFromTheDatabaseWithGet(): void
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

        $client->request('GET', '/locales/' . $locale->getId());
        $jsonResponse = json_decode($client->getResponse()->getContent(), false);

        self::assertSame('en_GB', $jsonResponse->code);
    }


    /**
     * Tests that a locale can be updated in the database.
     *
     * @covers \App\Controller\Locale\LocalePutController::put
     * @uses \App\ApiResource\LocaleInput::__construct
     * @uses \App\ApiResource\LocaleInput::getCode
     * @uses \App\Controller\Locale\LocalePutController::__construct
     * @uses \App\Entity\Property\Code::getCode
     * @uses \App\Entity\Property\Code::setCode
     * @uses \App\Repository\Locale\LocalePutRepository::__construct
     * @uses \App\Repository\Locale\LocalePutRepository::save
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

        $localeToPut = [
            'code' => 'fr_FR'
        ];
        $client->request('PUT', '/locales/' . $locale->getId(), content: json_encode($localeToPut));

        $entityManager->refresh($locale);

        self::assertSame('fr_FR', $locale->getCode());
    }


    /**
     * Tests that a locale can be deleted from the database.
     *
     * @covers \App\Controller\Locale\LocaleDeleteController::delete
     * @uses \App\Controller\Locale\LocaleDeleteController::__construct
     * @uses \App\Repository\Locale\LocaleDeleteRepository::__construct
     * @uses \App\Repository\Locale\LocaleDeleteRepository::delete
     */
    public function testIsDeletedFromTheDatabaseWithDelete(): void
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
        $localeId = $locale->getId();

        $client->request('DELETE', '/locales/' . $locale->getId());

        $localeAfterDelete = $entityManager->find(Locale::class, $localeId);

        self::assertNull($localeAfterDelete, 'The Locale has not been deleted.');
    }
}
