<?php

declare(strict_types=1);

namespace App\Tests\Repository\Locale;

use App\Entity\Locale;
use App\Exception\UnexpectedDirectionException;
use App\Exception\UnexpectedFieldException;
use App\Repository\Locale\LocaleCollectionRepository;
use PHPUnit\Framework\Attributes as PA;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the locale's collection GET repository.
 */
#[
    PA\CoversClass(LocaleCollectionRepository::class),
    PA\UsesClass(Locale::class),
    PA\UsesClass(UnexpectedDirectionException::class),
    PA\UsesClass(UnexpectedFieldException::class),
    PA\Group('repositories'),
    PA\Group('repository_locales'),
    PA\Group('repository_locales_collection'),
    PA\Group('locale')
]
final class LocaleCollectionRepositoryTest extends WebTestCase
{
    // Methods :

    /**
     * Generates 30 locales.
     */
    private function generate30Locales(): void
    {
        static::createClient();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        for ($localeIndex = 1; $localeIndex < 31; $localeIndex++) {
            $locale = new Locale('locale' . $localeIndex);
            $entityManager->persist($locale);
        }

        $entityManager->flush();
    }


    /**
     * Tests that a collection can be returned
     * without parameter.
     */
    public function testCanGetACollectionWithoutParameter(): void
    {
        $this->generate30Locales();

        $repository = static::getContainer()->get(LocaleCollectionRepository::class);
        $locales = $repository->findByPartialString();

        self::assertCount(30, $locales);
    }


    /**
     * Tests that an UnexpectedValueException is thrown
     * if the field is not queryable.
     */
    public function testCanThrowAnUnexpectedValueExceptionIfTheFieldIsNotQueryable(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('notQueryableField');

        $repository = static::getContainer()->get(LocaleCollectionRepository::class);
        $repository->findByPartialString(['notAField' => '123']);
    }

    /**
     * Tests that an UnexpectedValueException is thrown
     * if a field is queried with a non scalar value.
     */
    public function testCanThrowAnUnexpectedValueExceptionIfAFieldIsQueriedWithANonScalarValue(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('notAScalarValueInQuery');

        $repository = static::getContainer()->get(LocaleCollectionRepository::class);
        $repository->findByPartialString(['code' => []]);
    }

    /**
     * Tests that a collection can be returned
     * from a partial code.
     */
    public function testCanGetACollectionFromAPartialCode(): void
    {
        $this->generate30Locales();

        $criteria = ['code' => 'cale1'];

        $repository = static::getContainer()->get(LocaleCollectionRepository::class);
        /**
         * @var \App\Entity\Locale[] the found locales.
         */
        $locales = $repository->findByPartialString($criteria);

        self::assertCount(11, $locales);

        foreach ($locales as $locale) {
            self::assertStringContainsString('cale1', $locale->getCode());
        }
    }


    /**
     * Tests that an UnexpectedValueException is thrown
     * if the field is not orderable.
     */
    public function testCanThrowAnUnexpectedValueExceptionIfTheFieldIsNotOrderable(): void
    {
        $this->expectException(UnexpectedFieldException::class);
        $this->expectExceptionMessage('notOrderableField');

        $repository = static::getContainer()->get(LocaleCollectionRepository::class);
        $repository->findByPartialString(orderBy: ['notAField' => 'ASC']);
    }

    /**
     * Tests that an UnexpectedValueException is thrown
     * if the direction is not valid.
     */
    public function testCanThrowAnUnexpectedValueExceptionIfTheDirectionIsNotValid(): void
    {
        $this->expectException(UnexpectedDirectionException::class);
        $this->expectExceptionMessage('notADirection');

        $repository = static::getContainer()->get(LocaleCollectionRepository::class);
        $repository->findByPartialString(orderBy: ['id' => 'notADirection']);
    }


    /**
     * Tests that a collection can be
     * ordered by id asc.
     */
    public function testCanGetACollectionOrderedByIdAsc(): void
    {
        $this->generate30Locales();

        $orderBy = ['id' => 'ASC'];

        $repository = static::getContainer()->get(LocaleCollectionRepository::class);
        /**
         * @var \App\Entity\Locale[] the found locales.
         */
        $locales = $repository->findByPartialString(orderBy: $orderBy);

        self::assertSame(1, $locales[0]->getId());
        self::assertSame(30, $locales[29]->getId());
    }

    /**
     * Tests that a collection can be
     * ordered by id desc.
     */
    public function testCanGetACollectionOrderedByIdDesc(): void
    {
        $this->generate30Locales();

        $orderBy = ['id' => 'DESC'];

        $repository = static::getContainer()->get(LocaleCollectionRepository::class);
        /**
         * @var \App\Entity\Locale[] the found locales.
         */
        $locales = $repository->findByPartialString(orderBy: $orderBy);

        self::assertSame(30, $locales[0]->getId());
        self::assertSame(1, $locales[29]->getId());
    }


    /**
     * Tests that a collection can be
     * ordered by code asc.
     */
    public function testCanGetACollectionOrderedByCodeAsc(): void
    {
        $this->generate30Locales();

        $orderBy = ['code' => 'ASC'];

        $repository = static::getContainer()->get(LocaleCollectionRepository::class);
        /**
         * @var \App\Entity\Locale[] the found locales.
         */
        $locales = $repository->findByPartialString(orderBy: $orderBy);

        self::assertSame('locale1', $locales[0]->getCode());
        self::assertSame('locale9', $locales[29]->getCode());
    }

    /**
     * Tests that a collection can be
     * ordered by code desc.
     */
    public function testCanGetACollectionOrderedByCodeDesc(): void
    {
        $this->generate30Locales();

        $orderBy = ['code' => 'DESC'];

        $repository = static::getContainer()->get(LocaleCollectionRepository::class);
        /**
         * @var \App\Entity\Locale[] the found locales.
         */
        $locales = $repository->findByPartialString(orderBy: $orderBy);

        self::assertSame('locale9', $locales[0]->getCode());
        self::assertSame('locale1', $locales[29]->getCode());
    }


    /**
     * Tests that an UnexpectedValueException is thrown
     * if the limit is negative.
     */
    public function testCanThrowAnUnexpectedValueExceptionIfTheLimitIsNegative(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('limitIsNegative');

        $repository = static::getContainer()->get(LocaleCollectionRepository::class);
        $repository->findByPartialString(limit: -1);
    }

    /**
     * Tests that a collection can be returned
     * with a specified quantity.
     */
    public function testCanGetALimitedCollection(): void
    {
        $this->generate30Locales();

        $repository = static::getContainer()->get(LocaleCollectionRepository::class);
        $locales = $repository->findByPartialString(limit: 10);

        self::assertCount(10, $locales);
    }


    /**
     * Tests that an UnexpectedValueException is thrown
     * if the offset is negative.
     */
    public function testCanThrowAnUnexpectedValueExceptionIfTheOffsetIsNegative(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('offsetIsNegative');

        $repository = static::getContainer()->get(LocaleCollectionRepository::class);
        $repository->findByPartialString(offset: -1);
    }

    /**
     * Tests that a collection can be returned
     * from a specific offset.
     */
    public function testCanGetACollectionFromASpecificOffset(): void
    {
        $this->generate30Locales();

        $repository = static::getContainer()->get(LocaleCollectionRepository::class);
        $locales = $repository->findByPartialString(offset: 5);

        self::assertSame(6, $locales[0]->getId());
    }
}
