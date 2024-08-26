<?php

declare(strict_types=1);

namespace App\Controller\Role;

use App\ApiResource\RoleInput;
use App\ApiResource\ResourceLink;
use App\Controller\UnprocessableTranslationController;
use App\Exception\LocaleNotFoundException;
use App\Repository\Role\RolePostRepository;
use App\State\Role\RolePostProcessor;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Exception\MissingConstructorArgumentsException;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * A controller to POST a role.
 */
final class RolePostController extends UnprocessableTranslationController
{
    // Properties :

    /**
     * @var \Symfony\Component\Serializer\SerializerInterface the serializer.
     */
    private SerializerInterface $serializer;

    /**
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface the validator.
     */
    private ValidatorInterface $validator;

    /**
     * @var \App\State\Role\RolePostProcessor the processor.
     */
    private RolePostProcessor $processor;

    /**
     * @var \App\Repository\Role\RolePostRepository the role repository.
     */
    private RolePostRepository $repository;


    // Magic methods :

    /**
     * @param \Symfony\Component\Serializer\SerializerInterface $serializer the serializer.
     * @param \Symfony\Component\Validator\Validator\ValidatorInterface $validator the validator.
     * @param \App\State\Role\RolePostProcessor $processor the processor.
     * @param \App\Repository\Role\RolePostRepository $repository the role repository.
     */
    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        RolePostProcessor $processor,
        RolePostRepository $repository,
        TranslatorInterface $translator
    ) {
        parent::__construct($translator);

        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->processor = $processor;
        $this->repository = $repository;
    }


    // Methods :

    /**
     * Posts a role.
     * @param \Symfony\Component\HttpFoundation\Request $request the request.
     * @return \Symfony\Component\HttpFoundation\Response the response.
     */
    #[
        OA\Post(
            description: 'Adds a role.',
            path: '/roles',
            security: [
                new OA\SecurityScheme(
                    ref: '#/components/securitySchemes'
                )
            ],
            summary: 'Adds a role.',
            /** @infection-ignore-all */
            tags: ['Role']
        ),
        OA\Parameter(ref: '#/components/parameters/Accept-Language'),
        OA\RequestBody(
            content: new OA\JsonContent(ref: '#/components/schemas/RoleInput'),
            description: 'The role that needs to be added.',
            /** @infection-ignore-all */
            required: true
        ),
        OA\Response(
            ref: '#/components/responses/201',
            response: '201'
        ),
        OA\Response(
            ref: '#/components/responses/InvalidJsonBody',
            response: '400'
        ),
        OA\Response(
            ref: '#/components/responses/DELETE_PUTNotAllowed',
            response: '405'
        ),
        OA\Response(
            ref: '#/components/responses/409',
            response: '409'
        ),
        OA\Response(
            ref: '#/components/responses/422',
            response: '422'
        ),
        OA\Response(
            ref: '#/components/responses/500',
            response: '500'
        ),
        /** @infection-ignore-all */
        Route('/roles', methods: ['POST'], name: 'role_post', format: 'json'),
        IsGranted('ROLE_POST_ROLE')
    ]
    public function post(Request $request): Response
    {
        try {
            $roleInput = $this->serializer->deserialize($request->getContent(), RoleInput::class, 'json');
        } catch (
            NotEncodableValueException | MissingConstructorArgumentsException | NotNormalizableValueException $exception
        ) {
            return $this->respondToBadRequest($exception, $request->getLocale());
        }

        $violations = $this->validator->validate($roleInput);

        if (count($violations) > 0) {
            return $this->sendViolations($violations, 'role', $request->getLocale());
        }

        try {
            $role = $this->processor->getEntity($roleInput);
        } catch (LocaleNotFoundException $exception) {
            return $this->sendUnprocessableTranslationError(
                $exception->getMessage(),
                'role',
                $request->getLocale(),
                ['indentifier' => $exception->getIndentifier()]
            );
        }

        $unicityViolations = $this->validator->validate($role);

        if (count($unicityViolations) > 0) {
            return $this->sendViolations($unicityViolations, 'role', $request->getLocale(), Response::HTTP_CONFLICT);
        }

        try {
            $this->repository->save($role);
        } catch (UniqueConstraintViolationException $exception) {
            return $this->sendUnprocessableTranslationError(
                'translation.alreadyExist',
                'role',
                $request->getLocale()
            );
        }

        return $this->json(
            new ResourceLink($this->generateUrl('role_get', ['id' => $role->getId()])),
            Response::HTTP_CREATED
        );
    }
}
