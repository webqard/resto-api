<?php

declare(strict_types=1);

namespace App\State\Role;

use App\ApiResource\LocaleOutput;
use App\ApiResource\RoleOutput;
use App\ApiResource\RoleTranslationOutput;
use App\Entity\Role;
use App\Entity\RoleTranslation;

/**
 * A role provider.
 */
class RoleProvider
{
    // Methodes :

    /**
     * Returns a role to output.
     * @param \App\Entity\Role $role the role.
     * @param string[]|null $localeCodes the locale codes.
     * @return \App\ApiResource\RoleOutput the role to output.
     * @throws \DomainException if the role has a null id.
     */
    public function provideRoleOutput(Role $role, ?array $localeCodes = null): RoleOutput
    {
        $roleId = $role->getId();

        if ($roleId === null) {
            throw new \DomainException('entityNotPersisted');
        }

        return new RoleOutput(
            $roleId,
            $role->getName(),
            $this->getTranslationOutputs($role, $localeCodes)
        );
    }

    /**
     * Returns the translation outputs.
     * @param \App\Entity\Role $role the role.
     * @param string[]|null $localeCodes
     * @return \App\ApiResource\RoleTranslationOutput[]|null the translations.
     */
    private function getTranslationOutputs(Role $role, ?array $localeCodes = null): ?array
    {
        $translationOutputs = null;

        /**
         * @var \App\Entity\RoleTranslation $translation the translation.
         */
        foreach ($role->getTranslations() as $translation) {
            if (
                $localeCodes === null ||
                in_array($translation->getLocale()->getCode(), $localeCodes) === true
            ) {
                $translationOutputs[] = $this->getTranslationOutput($translation);
            }
        }

        return $translationOutputs;
    }

    /**
     * Returns the translation output.
     * @param \App\Entity\RoleTranslation $translation the translation.
     * @return \App\ApiResource\RoleTranslationOutput the translation output.
     * @throws \DomainException if the translation has a null id.
     * @throws \DomainException if the locale has a null id.
     */
    private function getTranslationOutput(RoleTranslation $translation): RoleTranslationOutput
    {
        $translationId = $translation->getId();
        $locale = $translation->getLocale();
        $localeId = $locale->getId();

        if (
            $translationId === null ||
            $localeId === null
        ) {
            throw new \DomainException('entityNotPersisted');
        }
        $localeOutput = new LocaleOutput(
            $localeId,
            $locale->getCode()
        );

        return new RoleTranslationOutput(
            $translationId,
            $translation->getDescription(),
            $localeOutput
        );
    }
}
