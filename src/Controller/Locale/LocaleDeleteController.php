<?php

declare(strict_types=1);

namespace App\Controller\Locale;

use App\ApiResource\ApiResponse;
use App\Repository\Locale\LocaleDeleteRepository;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * A controller to DELETE a locale.
 */
final class LocaleDeleteController extends AbstractController
{
    // Properties :

    /**
     * @var \App\Repository\Locale\LocaleDeleteRepository the locale's repository.
     */
    private LocaleDeleteRepository $repository;

    /**
     * @var \Symfony\Contracts\Translation\TranslatorInterface the translator.
     */
    private TranslatorInterface $translator;


    // Magic methods :

    /**
     * The constructor.
     * @param \App\Repository\Locale\LocaleDeleteRepository $repository the locale's repository.
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator the translator.
     */
    public function __construct(
        LocaleDeleteRepository $repository,
        TranslatorInterface $translator
    ) {
        $this->repository = $repository;
        $this->translator = $translator;
    }


    // Methods :

    /**
     * Deletes a locale.
     * @param \Symfony\Component\HttpFoundation\Request $request the request.
     * @param string $id the identifier.
     * @return \Symfony\Component\HttpFoundation\Response the response.
     */
    #[
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
        Route('/locales/{id}', methods: ['DELETE'], name: 'locale_delete')
    ]
    public function delete(Request $request, string $id): Response
    {
        $locale = $this->repository->find($id);

        if ($locale === null) {
            $message = $this->translator->trans('notFound', domain: 'locale', locale: $request->getLocale());

            return $this->json(
                new ApiResponse($message),
                Response::HTTP_NOT_FOUND
            );
        }

        $this->repository->delete($locale);

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
