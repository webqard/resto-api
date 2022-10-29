<?php

declare(strict_types=1);

namespace App\Controller\Currency;

use App\ApiResource\ApiResponse;
use App\Repository\Currency\CurrencyGetRepository;
use App\State\Currency\CurrencyProvider;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * A controller to GET a currency.
 */
final class CurrencyGetController extends AbstractController
{
    // Properties :

    /**
     * @var \App\Repository\Currency\CurrencyGetRepository the currency's repository.
     */
    private CurrencyGetRepository $repository;

    /**
     * @var \App\State\Currency\CurrencyProvider the provider.
     */
    private CurrencyProvider $outputProvider;

    /**
     * @var \Symfony\Contracts\Translation\TranslatorInterface the translator.
     */
    private TranslatorInterface $translator;


    // Magic methods :

    /**
     * The constructor.
     * @param \App\Repository\Currency\CurrencyGetRepository $repository the currency's repository.
     * @param \App\State\Currency\CurrencyProvider $outputProvider the provider.
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator the translator.
     */
    public function __construct(
        CurrencyGetRepository $repository,
        CurrencyProvider $outputProvider,
        TranslatorInterface $translator
    ) {
        $this->repository = $repository;
        $this->outputProvider = $outputProvider;
        $this->translator = $translator;
    }


    // Methods :

    /**
     * Gets a currency.
     * @param \Symfony\Component\HttpFoundation\Request $request the request.
     * @param string $id the identifier.
     * @return \Symfony\Component\HttpFoundation\Response the response.
     */
    #[
        /** @infection-ignore-all */
        OA\Get(
            description: 'Fetches a currency.',
            path: '/currencies/{id}',
            summary: 'Fetches a currency.',
            tags: ['Currency']
        ),
        OA\Parameter(ref: '#/components/parameters/id'),
        OA\Parameter(ref: '#/components/parameters/Accept-Language'),
        OA\Response(
            content: new OA\JsonContent(ref: '#/components/schemas/Currency'),
            description: 'When the currency is found.',
            response: '200'
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
        Route('/currencies/{id}', methods: ['GET'], name: 'currency_get')
    ]
    public function get(Request $request, string $id): Response
    {
        /**
         * @var \App\Entity\Currency|null
         */
        $currency = $this->repository->find($id);

        if ($currency === null) {
            $message = $this->translator->trans('currency.notFound', [], 'currency', $request->getLocale());

            return $this->json(
                new ApiResponse($message),
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->json($this->outputProvider->provideCurrencyOutput($currency));
    }
}
