<?php

declare(strict_types=1);

namespace App\Tests\State\Role;

use App\ApiResource\RoleInput;
use App\ApiResource\RoleTranslationInput;
use App\Exception\LocaleNotFoundException;
use App\Entity\Locale;
use App\Entity\Role;
use App\Entity\RoleTranslation;
use App\Repository\Locale\LocaleGetRepository;
use App\State\Role\RolePostProcessor;
use PHPUnit\Framework\Attributes as PA;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Tests the role post processor.
 */
#[
    PA\CoversClass(RolePostProcessor::class),
    PA\UsesClass(Locale::class),
    PA\UsesClass(Role::class),
    PA\UsesClass(RoleTranslation::class),
    PA\UsesClass(RoleInput::class),
    PA\UsesClass(RoleTranslationInput::class),
    PA\UsesClass(LocaleGetRepository::class),
    PA\UsesClass(LocaleNotFoundException::class),
    PA\Group('state'),
    PA\Group('state_rolePostProcessor'),
    PA\Group('role')
]
final class RolePostProcessorTest extends KernelTestCase
{
    // Methods :

    /**
     * Test that the entity can be returned
     * without translation.
     */
    public function testCanGetEntityWithoutTranslation(): void
    {
        $container = self::getContainer();
        $localeGetRepository = $container->get(LocaleGetRepository::class);
        $rolePostProcessor = new RolePostProcessor($localeGetRepository);
        $roleInput = new RoleInput('name');

        $role = $rolePostProcessor->getEntity($roleInput);

        self::assertInstanceOf(Role::class, $role);
        self::assertSame('name', $role->getName());
    }


    /**
     * Adds an EN locale.
     */
    private function addAnENLocale(): void
    {
        $container = self::getContainer();

        $locale = new Locale('en_GB');

        $manager = $container->get('doctrine')->getManager();

        $manager->persist($locale);
        $manager->flush();
    }

    /**
     * Test that the entity can be returned
     * with translation.
     */
    public function testCanGetEntityWithTranslation(): void
    {
        $this->addAnENLocale();
        $container = self::getContainer();
        $localeGetRepository = $container->get(LocaleGetRepository::class);
        $rolePostProcessor = new RolePostProcessor($localeGetRepository);
        $roleInput = new RoleInput(
            'name',
            [
                new RoleTranslationInput('description', 1)
            ]
        );

        $role = $rolePostProcessor->getEntity($roleInput);

        self::assertInstanceOf(Role::class, $role);
        self::assertSame('name', $role->getName());

        $translations = $role->getTranslations();

        self::assertCount(1, $translations);
        self::assertArrayHasKey(0, $translations);
        self::assertSame('description', $translations[0]->getDescription());
        self::assertSame(1, $translations[0]->getLocale()->getId());
    }


    /**
     * Test that a LocaleNotFoundException is thrown
     * when the locale does not exist.
     */
    public function testThrowsALocaleNotFoundExceptionWhenTheLocaleDoesNotExist(): void
    {
        $this->expectException(LocaleNotFoundException::class);
        $this->expectExceptionMessage('translationLocale.notFound');

        $container = self::getContainer();
        $localeGetRepository = $container->get(LocaleGetRepository::class);
        $rolePostProcessor = new RolePostProcessor($localeGetRepository);
        $roleInput = new RoleInput(
            'name',
            [
                new RoleTranslationInput('description', 1)
            ]
        );

        $rolePostProcessor->getEntity($roleInput);
    }
}
