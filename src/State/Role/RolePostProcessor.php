<?php

declare(strict_types=1);

namespace App\State\Role;

use App\Entity\Role;
use App\Entity\RoleTranslation;
use App\Exception\LocaleNotFoundException;
use App\Repository\Locale\LocaleGetRepository;
use App\ApiResource\RoleInput;

/**
 * A processor for post role.
 */
class RolePostProcessor
{
    // Properties :

    /**
     * @var \App\Repository\Locale\LocaleGetRepository the locale repository.
     */
    private readonly LocaleGetRepository $localeRepository;


    // Magic methods :

    /**
     * The constructor.
     * @param \App\Repository\Locale\LocaleGetRepository $localeRepository the locale repository.
     */
    public function __construct(LocaleGetRepository $localeRepository)
    {
        $this->localeRepository = $localeRepository;
    }



    // Methodes :

    /**
     * Returns a new role filled with input datas.
     * @param \App\ApiResource\RoleInput $roleInput the input datas.
     * @return \App\Entity\Role the role.
     */
    public function getEntity(RoleInput $roleInput): Role
    {
        $role = new Role($roleInput->getName());
        $translations = $roleInput->getTranslations();

        if ($translations === null) {
            return $role;
        }

        foreach ($translations as $translationInput) {
            $locale = $this->localeRepository->find($translationInput->getLocaleId());

            if ($locale === null) {
                throw new LocaleNotFoundException(
                    'translationLocale.notFound',
                    $translationInput->getLocaleId()
                );
            }

            $translation = new RoleTranslation(
                $role,
                $locale,
                $translationInput->getDescription()
            );
            $role->addTranslation($translation);
        }

        return $role;
    }
}
