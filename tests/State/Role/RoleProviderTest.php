<?php

declare(strict_types=1);

namespace App\Tests\State\Role;

use App\ApiResource\RoleOutput;
use App\ApiResource\RoleTranslationOutput;
use App\Entity\Locale;
use App\Entity\Role;
use App\Entity\RoleTranslation;
use Doctrine\Common\Collections\ArrayCollection;
use App\State\Role\RoleProvider;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the role provider.
 */
#[
    PA\CoversClass(RoleProvider::class),
    PA\UsesClass(Locale::class),
    PA\UsesClass(Role::class),
    PA\UsesClass(RoleOutput::class),
    PA\UsesClass(RoleTranslation::class),
    PA\UsesClass(RoleTranslationOutput::class),
    PA\Group('state'),
    PA\Group('state_roleProvider'),
    PA\Group('role')
]
final class RoleProviderTest extends TestCase
{
    // Methods :

    /**
     * Test that a DomainException is thrown
     * if the role is not persisted.
     */
    public function testCanThrowADomainExceptionIfTheRoleIsNotPersisted(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('entityNotPersisted');

        $role = new Role('ROLE_GET_ROLE');
        $roleProvider = new RoleProvider();
        $roleProvider->provideRoleOutput($role);
    }

    /**
     * Test that a DomainException is thrown
     * if the translation is not persisted.
     */
    public function testCanThrowADomainExceptionIfTheTranslationIsNotPersisted(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('entityNotPersisted');

        $locale = $this->createStub(Locale::class);
        $locale->method('getId')
            ->willReturn(1);

        $translation = $this->createStub(RoleTranslation::class);
        $translation->method('getId')
            ->willReturn(null);
        $translation->method('getLocale')
            ->willReturn($locale);

        $role = $this->createStub(Role::class);
        $role->method('getId')
            ->willReturn(1);
        $role->method('getTranslations')
            ->willReturn(new ArrayCollection([$translation]));

        $roleProvider = new RoleProvider();
        $roleProvider->provideRoleOutput($role);
    }

    /**
     * Test that a DomainException is thrown
     * if the locale is not persisted.
     */
    public function testCanThrowADomainExceptionIfTheLocaleIsNotPersisted(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('entityNotPersisted');

        $locale = $this->createStub(Locale::class);
        $locale->method('getId')
            ->willReturn(null);

        $translation = $this->createStub(RoleTranslation::class);
        $translation->method('getId')
            ->willReturn(1);
        $translation->method('getLocale')
            ->willReturn($locale);

        $role = $this->createStub(Role::class);
        $role->method('getId')
            ->willReturn(1);
        $role->method('getTranslations')
            ->willReturn(new ArrayCollection([$translation]));

        $roleProvider = new RoleProvider();
        $roleProvider->provideRoleOutput($role);
    }


    /**
     * Test that the role output can be provided
     * without translation.
     */
    public function testCanProvideRoleOutputWithoutTranslation(): void
    {
        $role = $this->createStub(Role::class);
        $role->method('getId')
            ->willReturn(1);
        $role->method('getName')
            ->willReturn('name');
        $role->method('getTranslations')
            ->willReturn(new ArrayCollection());

        $roleProvider = new RoleProvider();
        $roleOutput = $roleProvider->provideRoleOutput($role);

        $serialisedRoleOutput = $roleOutput->jsonSerialize();

        self::assertIsArray($serialisedRoleOutput);
        self::assertArrayHasKey('id', $serialisedRoleOutput);
        self::assertSame(1, $serialisedRoleOutput['id']);
        self::assertArrayHasKey('name', $serialisedRoleOutput);
        self::assertSame('name', $serialisedRoleOutput['name']);
        self::assertArrayHasKey('translations', $serialisedRoleOutput);
        self::assertSame([], $serialisedRoleOutput['translations']);
    }


    /**
     * Test that the role output can be provided
     * with a translation.
     */
    public function testCanProvideRoleOutputWithATranslation(): void
    {
        $localeEN = $this->createStub(Locale::class);
        $localeEN->method('getId')
            ->willReturn(1);

        $roleTranslationEN = $this->createStub(RoleTranslation::class);
        $roleTranslationEN->method('getId')
            ->willReturn(1);
        $roleTranslationEN->method('getDescription')
            ->willReturn('en-description');
        $roleTranslationEN->method('getLocale')
            ->willReturn($localeEN);

        $role = $this->createStub(Role::class);
        $role->method('getId')
            ->willReturn(1);
        $role->method('getName')
            ->willReturn('name');
        $role->method('getTranslations')
            ->willReturn(new ArrayCollection([
                $roleTranslationEN
            ]));

        $roleProvider = new RoleProvider();
        $roleOutput = $roleProvider->provideRoleOutput($role);

        $serialisedRoleOutput = $roleOutput->jsonSerialize();

        self::assertIsArray($serialisedRoleOutput);
        self::assertArrayHasKey('id', $serialisedRoleOutput);
        self::assertSame(1, $serialisedRoleOutput['id']);
        self::assertArrayHasKey('name', $serialisedRoleOutput);
        self::assertSame('name', $serialisedRoleOutput['name']);
        self::assertArrayHasKey('translations', $serialisedRoleOutput);

        $translations = $serialisedRoleOutput['translations'];
        self::assertArrayHasKey(0, $translations);

        $serialisedTranslationEN = $translations[0]->jsonSerialize();
        self::assertSame(1, $serialisedTranslationEN['id']);
        self::assertSame('en-description', $serialisedTranslationEN['description']);
        self::assertSame(1, $serialisedTranslationEN['localeId']);
    }


    /**
     * Test that the role output can be provided
     * with many translations.
     */
    public function testCanProvideRoleOutputWithManyTranslations(): void
    {
        $localeEN = $this->createStub(Locale::class);
        $localeEN->method('getId')
            ->willReturn(1);

        $roleTranslationEN = $this->createStub(RoleTranslation::class);
        $roleTranslationEN->method('getId')
            ->willReturn(1);
        $roleTranslationEN->method('getDescription')
            ->willReturn('en-description');
        $roleTranslationEN->method('getLocale')
            ->willReturn($localeEN);

        $localeFR = $this->createStub(Locale::class);
        $localeFR->method('getId')
            ->willReturn(2);

        $roleTranslationFR = $this->createStub(RoleTranslation::class);
        $roleTranslationFR->method('getId')
            ->willReturn(2);
        $roleTranslationFR->method('getDescription')
            ->willReturn('fr-description');
        $roleTranslationFR->method('getLocale')
            ->willReturn($localeFR);

        $role = $this->createStub(Role::class);
        $role->method('getId')
            ->willReturn(1);
        $role->method('getName')
            ->willReturn('name');
        $role->method('getTranslations')
            ->willReturn(new ArrayCollection([
                $roleTranslationEN,
                $roleTranslationFR
            ]));

        $roleProvider = new RoleProvider();
        $roleOutput = $roleProvider->provideRoleOutput($role);

        $serialisedRoleOutput = $roleOutput->jsonSerialize();

        self::assertIsArray($serialisedRoleOutput);
        self::assertArrayHasKey('id', $serialisedRoleOutput);
        self::assertSame(1, $serialisedRoleOutput['id']);
        self::assertArrayHasKey('name', $serialisedRoleOutput);
        self::assertSame('name', $serialisedRoleOutput['name']);
        self::assertArrayHasKey('translations', $serialisedRoleOutput);

        $translations = $serialisedRoleOutput['translations'];
        self::assertArrayHasKey(0, $translations);
        self::assertArrayHasKey(1, $translations);

        $serialisedTranslationEN = $translations[0]->jsonSerialize();
        self::assertSame(1, $serialisedTranslationEN['id']);
        self::assertSame('en-description', $serialisedTranslationEN['description']);
        self::assertSame(1, $serialisedTranslationEN['localeId']);

        $serialisedTranslationFR = $translations[1]->jsonSerialize();
        self::assertSame(2, $serialisedTranslationFR['id']);
        self::assertSame('fr-description', $serialisedTranslationFR['description']);
        self::assertSame(2, $serialisedTranslationFR['localeId']);
    }
}
