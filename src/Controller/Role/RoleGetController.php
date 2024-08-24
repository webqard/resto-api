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
     * @param string $id the identifier.
     * @return \Symfony\Component\HttpFoundation\Response the response.
     */
    #[
        /** @infection-ignore-all */
        OA\Get(
            description: 'Fetches a role.',
            path: '/roles/{id}',
            summary: 'Fetches a role.',
            tags: ['Role']
        ),
        OA\Parameter(ref: '#/components/parameters/id'),
        OA\Parameter(ref: '#/components/parameters/Accept-Language'),
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
    public function get(Request $request, string $id): Response
    {
        $role = $this->repository->find($id);

        if ($role === null) {
            $message = $this->translator->trans('notFound', locale: $request->getLocale());

            return $this->json(
                new ApiResponse($message),
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->json($this->provider->provideRoleOutput($role));
    }
}
