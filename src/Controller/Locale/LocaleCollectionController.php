<?php

declare(strict_types=1);

namespace App\Controller\Locale;

use App\ApiResource\ApiResponse;
use App\Exception\UnexpectedFieldException;
use App\Repository\Locale\LocaleCollectionRepository;
use App\State\Locale\LocaleProvider;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * A controller to GET a collection.
 */
final class LocaleCollectionController extends AbstractController
{
    // Properties :

    /**
     * @var \App\Repository\Locale\LocaleCollectionRepository the repository.
     */
    private LocaleCollectionRepository $repository;

    /**
     * @var \App\State\Locale\LocaleProvider the provider.
     */
    private LocaleProvider $provider;

    /**
     * @var \Symfony\Contracts\Translation\TranslatorInterface the translator.
     */
    private TranslatorInterface $translator;


    // Magic methods :

    /**
     * The constructor.
     * @param \App\Repository\Locale\LocaleCollectionRepository $repository the repository.
     * @param \App\State\Locale\LocaleProvider $provider the provider.
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator the translator.
     */
    public function __construct(
        LocaleCollectionRepository $repository,
        LocaleProvider $provider,
        TranslatorInterface $translator
    ) {
        $this->repository = $repository;
        $this->provider = $provider;
        $this->translator = $translator;
    }


    // Methods :

    /**
     * Gets a collection of locales.
     * @param \Symfony\Component\HttpFoundation\Request the request.
     * @param string[] $criterias the criterias.
     * @param string[]|null $orderBy the orders.
     * @param ?int $limit the limit.
     * @param int $offset the offset.
     * @return \Symfony\Component\HttpFoundation\Response the response.
     */
    #[
        /** @infection-ignore-all */
        OA\Get(
            description: 'Fetches a collection of locales.',
            path: '/locales',
            summary: 'Fetches a collection of locales.',
            tags: ['Locale']
        ),
        OA\Parameter(ref: '#/components/parameters/offset'),
        OA\Parameter(ref: '#/components/parameters/limit'),
        OA\Parameter(ref: '#/components/parameters/criterias'),
        OA\Parameter(ref: '#/components/parameters/orderby'),
        OA\Parameter(ref: '#/components/parameters/Accept-Language'),
        OA\Response(
            content: new OA\JsonContent(
                type: 'array',
                items: new OA\Items('#/components/schemas/LocaleOutput')
            ),
            description: 'When the request is successful.',
            response: '200'
        ),
        OA\Response(
            ref: '#/components/responses/DELETE_PUTNotAllowed',
            response: '405'
        ),
        OA\Response(
            ref: '#/components/responses/500',
            response: '500'
        ),
        /** @infection-ignore-all */
        Route('/locales', methods: ['GET'], name: 'locales_get'),
        IsGranted('ROLE_GET_LOCALE_COLLECTION')
    ]
    public function get(
        Request $request,
        #[MapQueryParameter] array $criterias = [],
        #[MapQueryParameter] ?array $orderBy = null,
        #[MapQueryParameter] ?int $limit = null,
        #[MapQueryParameter] int $offset = 0
    ): Response {

        try {
            $locales = $this->repository->findByPartialString(
                $criterias,
                $orderBy,
                $limit,
                $offset
            );
        } catch (UnexpectedFieldException | \UnexpectedValueException $exception) {
            $parameters = [];

            if (($exception instanceof UnexpectedFieldException) === true) {
                $parameters = [
                    'givenField' => $exception->getGivenField(),
                    'availableFields' => $exception->getAvailableFields()
                ];
            }

            $response = new ApiResponse(
                $this->translator->trans(
                    $exception->getMessage(),
                    $parameters,
                    locale: $request->getLocale()
                )
            );

            return $this->json($response, Response::HTTP_BAD_REQUEST);
        }

        $localesOutput = [];

        foreach ($locales as $locale) {
            $localesOutput[] = $this->provider->provideLocaleOutput($locale);
        }

        return $this->json($localesOutput);
    }
}
