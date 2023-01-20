<?php

declare(strict_types=1);

namespace App\Controller\Locale;

use App\Controller\DeleteController;
use App\Repository\Locale\LocaleDeleteRepository;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * A controller to DELETE a locale.
 */
final class LocaleDeleteController extends DeleteController
{
    // Magic methods :

    /**
     * @param \App\Repository\Locale\LocaleDeleteRepository $repository the locale's repository.
     */
    public function __construct(
        LocaleDeleteRepository $repository,
        TranslatorInterface $translator
    ) {
        parent::__construct($repository, $translator);
    }


    // Methods :

    /**
     * Deletes a locale.
     */
    #[
        /** @infection-ignore-all */
        OA\Delete(
            description: 'Deletes a locale.',
            path: '/locales/{id}',
            summary: 'Deletes a locale.',
            tags: ['Locale']
        ),
        OA\Parameter(ref: '#/components/parameters/id'),
        OA\Parameter(ref: '#/components/parameters/Accept-Language'),
        OA\Response(
            ref: '#/components/responses/204',
            response: '204'
        ),
        OA\Response(
            ref: '#/components/responses/404',
            response: '404'
        ),
        OA\Response(
            ref: '#/components/responses/POSTNotAllowed',
            response: '405'
        ),
        OA\Response(
            ref: '#/components/responses/500',
            response: '500'
        ),
        /** @infection-ignore-all */
        Route('/locales/{id}', methods: ['DELETE'], name: 'locale_delete')
    ]
    public function delete(Request $request, string $id): Response
    {
        return parent::delete($request, $id);
    }
}
