<?php

declare(strict_types=1);

namespace App\State\Role;

use App\ApiResource\RoleOutput;
use App\ApiResource\RoleTranslationOutput;
use App\Entity\Role;

/**
 * A role provider.
 */
class RoleProvider
{
    // Methodes :

    /**
     * Returns a role to output.
     * @param \App\Entity\Role $role the role.
     * @return \App\ApiResource\RoleOutput the role to output.
     * @throws \DomainException if the role has a null id.
     */
    public function provideRoleOutput(Role $role): RoleOutput
    {
        $roleId = $role->getId();

        if ($roleId === null) {
            throw new \DomainException('entityNotPersisted');
        }

        $translations = [];

        /**
         * @var \App\Entity\RoleTranslation $translation the translation.
         */
        foreach ($role->getTranslations() as $translation) {
            $translationId = $translation->getId();
            $localeId = $translation->getLocale()->getId();

            if (
                $translationId === null ||
                $localeId === null
            ) {
                throw new \DomainException('entityNotPersisted');
            }

            $translations[] = new RoleTranslationOutput(
                $translationId,
                $translation->getDescription(),
                $localeId
            );
        }

        return new RoleOutput(
            $roleId,
            $role->getName(),
            $translations
        );
    }
}
