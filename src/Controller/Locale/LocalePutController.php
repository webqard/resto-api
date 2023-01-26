<?php

declare(strict_types=1);

namespace App\Controller\Locale;

use App\ApiResource\ApiResponse;
use App\ApiResource\LocaleInput;
use App\Controller\SendErrorController;
use App\Repository\Locale\LocalePutRepository;
use App\State\Locale\LocalePutProcessor;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\MissingConstructorArgumentsException;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * A controller to PUT a locale.
 */
final class LocalePutController extends SendErrorController
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
     * @var \App\State\Locale\LocalePutProcessor the processor.
     */
    private LocalePutProcessor $processor;

    /**
     * @var \App\Repository\Locale\LocalePutRepository the locale's repository.
     */
    private LocalePutRepository $repository;


    // Magic methods :

    /**
     * @param \Symfony\Component\Serializer\SerializerInterface $serializer the serializer.
     * @param \Symfony\Component\Validator\Validator\ValidatorInterface $validator the validator.
     * @param \App\State\Locale\LocalePutProcessor $processor the processor.
     * @param \App\Repository\Locale\LocalePutRepository $repository the locale's repository.
     */
    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        LocalePutProcessor $processor,
        LocalePutRepository $repository,
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
     * Puts a locale.
     * @param \Symfony\Component\HttpFoundation\Request $request the request.
     * @param string $id the identifier.
     * @return \Symfony\Component\HttpFoundation\Response the response.
     */
    #[
        OA\Put(
            description: 'Updates a locale.',
            path: '/locales/{id}',
            summary: 'Updates a locale.',
            /** @infection-ignore-all */
            tags: ['Locale']
        ),
        OA\Parameter(ref: '#/components/parameters/id'),
        OA\Parameter(ref: '#/components/parameters/Accept-Language'),
        OA\RequestBody(
            content: new OA\JsonContent(ref: '#/components/schemas/LocaleInput'),
            description: 'The locale that needs to be added.',
            /** @infection-ignore-all */
            required: true
        ),
        OA\Response(
            ref: '#/components/responses/InvalidJsonBody',
            response: '400'
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
        Route('/locales/{id}', methods: ['PUT'], name: 'locale_put')
    ]
    public function put(Request $request, string $id): Response
    {
        $locale = $this->repository->find($id);

        if ($locale === null) {
            $message = $this->translator->trans('notFound', locale: $request->getLocale());

            return $this->json(
                new ApiResponse($message),
                Response::HTTP_NOT_FOUND
            );
        }

        try {
            $localeInput = $this->serializer->deserialize($request->getContent(), LocaleInput::class, 'json');
        } catch (
            NotEncodableValueException | MissingConstructorArgumentsException | NotNormalizableValueException $exception
        ) {
            return $this->respondToBadRequest($exception, $request->getLocale());
        }

        $violations = $this->validator->validate($localeInput);

        if (count($violations) > 0) {
            return $this->sendViolations($violations, $request->getLocale());
        }

        $updatedLocale = $this->processor->getEntity($locale, $localeInput);
        $unicityViolations = $this->validator->validate($updatedLocale);

        if (count($unicityViolations) > 0) {
            return $this->sendViolations($unicityViolations, $request->getLocale(), Response::HTTP_CONFLICT);
        }

        $this->repository->save($updatedLocale);

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
