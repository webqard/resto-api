<?php

declare(strict_types=1);

namespace App\Controller\Currency;

use App\Controller\DeleteController;
use App\Repository\Currency\CurrencyDeleteRepository;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * A controller to DELETE a currency.
 */
final class CurrencyDeleteController extends DeleteController
{
    // Magic methods :

    /**
     * @param \App\Repository\Currency\CurrencyDeleteRepository $repository the currency's repository.
     */
    public function __construct(
        CurrencyDeleteRepository $repository,
        TranslatorInterface $translator
    ) {
        parent::__construct($repository, $translator);
    }


    // Methods :

    /**
     * Deletes a currency.
     */
    #[
        /** @infection-ignore-all */
        OA\Delete(
            description: 'Deletes a currency.',
            path: '/currencies/{id}',
            summary: 'Deletes a currency.',
            tags: ['Currency']
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
        Route('/currencies/{id}', methods: ['DELETE'], name: 'currency_delete')
    ]
    public function delete(Request $request, string $id): Response
    {
        return parent::delete($request, $id);
    }
}
