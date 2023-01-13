<?php

declare(strict_types=1);

namespace App\Controller\Locale;

use App\ApiResource\ApiResponse;
use App\Repository\Locale\LocaleGetRepository;
use App\State\Locale\LocaleProvider;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * A controller to GET a locale.
 */
final class LocaleGetController extends AbstractController
{
    // Properties :

    /**
     * @var \App\Repository\Locale\LocaleGetRepository the locale's repository.
     */
    private LocaleGetRepository $repository;

    /**
     * @var \App\State\Locale\LocaleProvider the provider.
     */
    private LocaleProvider $outputProvider;

    /**
     * @var \Symfony\Contracts\Translation\TranslatorInterface the translator.
     */
    private TranslatorInterface $translator;


    // Magic methods :

    /**
     * The constructor.
     * @param \App\Repository\Locale\LocaleGetRepository $repository the locale's repository.
     * @param \App\State\Locale\LocaleProvider $outputProvider the provider.
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator the translator.
     */
    public function __construct(
        LocaleGetRepository $repository,
        LocaleProvider $outputProvider,
        TranslatorInterface $translator
    ) {
        $this->repository = $repository;
        $this->outputProvider = $outputProvider;
        $this->translator = $translator;
    }


    // Methods :

    /**
     * Gets a locale.
     * @param \Symfony\Component\HttpFoundation\Request $request the request.
     * @param string $id the identifier.
     * @return \Symfony\Component\HttpFoundation\Response the response.
     */
    #[
        /** @infection-ignore-all */
        OA\Get(
            description: 'Fetches a locale.',
            path: '/locales/{id}',
            summary: 'Fetches a locale.',
            tags: ['Locale']
        ),
        OA\Parameter(ref: '#/components/parameters/id'),
        OA\Parameter(ref: '#/components/parameters/Accept-Language'),
        OA\Response(
            content: new OA\JsonContent(ref: '#/components/schemas/LocaleOutput'),
            description: 'When the locale is found.',
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
        Route('/locales/{id}', methods: ['GET'], name: 'locale_get')
    ]
    public function get(Request $request, string $id): Response
    {
        /**
         * @var \App\Entity\Locale|null
         */
        $locale = $this->repository->find($id);

        if ($locale === null) {
            $message = $this->translator->trans('notFound', [], 'locale', $request->getLocale());

            return $this->json(
                new ApiResponse($message),
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->json($this->outputProvider->provideLocaleOutput($locale));
    }
}
