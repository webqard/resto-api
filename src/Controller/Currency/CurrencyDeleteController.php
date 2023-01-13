<?php

declare(strict_types=1);

namespace App\Controller\Currency;

use App\ApiResource\ApiResponse;
use App\Repository\Currency\CurrencyDeleteRepository;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * A controller to DELETE a currency.
 */
final class CurrencyDeleteController extends AbstractController
{
    // Properties :

    /**
     * @var \App\Repository\Currency\CurrencyDeleteRepository the currency's repository.
     */
    private CurrencyDeleteRepository $repository;

    /**
     * @var \Symfony\Contracts\Translation\TranslatorInterface the translator.
     */
    private TranslatorInterface $translator;


    // Magic methods :

    /**
     * The constructor.
     * @param \App\Repository\Currency\CurrencyDeleteRepository $repository the currency's repository.
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator the translator.
     */
    public function __construct(
        CurrencyDeleteRepository $repository,
        TranslatorInterface $translator
    ) {
        $this->repository = $repository;
        $this->translator = $translator;
    }


    // Methods :

    /**
     * Deletes a currency.
     * @param \Symfony\Component\HttpFoundation\Request $request the request.
     * @param string $id the identifier.
     * @return \Symfony\Component\HttpFoundation\Response the response.
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
        $currency = $this->repository->find($id);

        if ($currency === null) {
            $message = $this->translator->trans('notFound', locale: $request->getLocale());

            return $this->json(
                new ApiResponse($message),
                Response::HTTP_NOT_FOUND
            );
        }

        $this->repository->delete($currency);

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
