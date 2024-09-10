<?php

declare(strict_types=1);

namespace App\Controller\Role;

use App\ApiResource\ApiResponse;
use App\Repository\Role\RoleGetRepository;
use App\State\Role\RoleProvider;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * A controller to GET a role.
 */
final class RoleGetController extends AbstractController
{
    // Properties :

    /**
     * @var \App\Repository\Role\RoleGetRepository the role's repository.
     */
    private RoleGetRepository $repository;

    /**
     * @var \App\State\Role\RoleProvider the provider.
     */
    private RoleProvider $provider;

    /**
     * @var \Symfony\Contracts\Translation\TranslatorInterface the translator.
     */
    private TranslatorInterface $translator;


    // Magic methods :

    /**
     * The constructor.
     * @param \App\Repository\Role\RoleGetRepository $repository the role's repository.
     * @param \App\State\Role\RoleProvider $provider the provider.
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator the translator.
     */
    public function __construct(
        RoleGetRepository $repository,
        RoleProvider $provider,
        TranslatorInterface $translator
    ) {
        $this->repository = $repository;
        $this->provider = $provider;
        $this->translator = $translator;
    }


    // Methods :

    /**
     * Gets a role.
     * @param \Symfony\Component\HttpFoundation\Request $request the request.
     * @param int $id the identifier.
     * @param string[]|null $translations the translation to be filtered.
     * @return \Symfony\Component\HttpFoundation\Response the response.
     */
    #[
        /** @infection-ignore-all */
        OA\Get(
            description: 'Fetches a role.',
            path: '/roles/{id}',
            security: [
                new OA\SecurityScheme(
                    ref: '#/components/securitySchemes'
                )
            ],
            summary: 'Fetches a role.',
            tags: ['Role']
        ),
        OA\Parameter(ref: '#/components/parameters/id'),
        OA\Parameter(ref: '#/components/parameters/Accept-Language'),
        OA\Parameter(ref: '#/components/parameters/translations'),
        OA\Response(
            content: new OA\JsonContent(ref: '#/components/schemas/RoleOutput'),
            description: 'When the role is found.',
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
        Route('/roles/{id}', methods: ['GET'], name: 'role_get', format: 'json'),
        IsGranted('ROLE_GET_ROLE')
    ]
    public function get(
        Request $request,
        int $id,
        #[MapQueryParameter] ?array $translations = null
    ): Response {
        $role = $this->repository->find($id);

        if ($role === null) {
            $message = $this->translator->trans('notFound', locale: $request->getLocale());

            return $this->json(
                new ApiResponse($message),
                Response::HTTP_NOT_FOUND
            );
        }

        $roleOutput = $this->provider->provideRoleOutput(
            $role,
            $translations
        );

        return $this->json($roleOutput);
    }
}
