<?php

declare(strict_types=1);

namespace App\Controller\Locale;

use App\ApiResource\ApiResponse;
use App\ApiResource\LocaleInput;
use App\ApiResource\Violation;
use App\ApiResource\Violations;
use App\Repository\Locale\LocaleSaveRepository;
use App\State\Locale\LocalePutProcessor;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\MissingConstructorArgumentsException;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * A controller to PUT a locale.
 */
final class LocalePutController extends AbstractController
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
    private LocalePutProcessor $putProcessor;

    /**
     * @var \App\Repository\Locale\LocaleSaveRepository the locale's repository.
     */
    private LocaleSaveRepository $repository;

    /**
     * @var \Symfony\Contracts\Translation\TranslatorInterface the translator.
     */
    private TranslatorInterface $translator;


    // Magic methods :

    /**
     * The constructor.
     * @param \Symfony\Component\Serializer\SerializerInterface $serializer the serializer.
     * @param \Symfony\Component\Validator\Validator\ValidatorInterface $validator the validator.
     * @param \App\State\Locale\LocalePutProcessor $putProcessor the processor.
     * @param \App\Repository\Locale\LocaleSaveRepository $repository the locale's repository.
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator the translator.
     */
    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        LocalePutProcessor $putProcessor,
        LocaleSaveRepository $repository,
        TranslatorInterface $translator
    ) {
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->putProcessor = $putProcessor;
        $this->repository = $repository;
        $this->translator = $translator;
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
        /**
         * @var \App\Entity\Locale|null
         */
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

        $updatedLocale = $this->putProcessor->getEntity($locale, $localeInput);
        $unicityViolations = $this->validator->validate($updatedLocale);

        if (count($unicityViolations) > 0) {
            return $this->sendViolations($unicityViolations, $request->getLocale(), Response::HTTP_CONFLICT);
        }

        $this->repository->save($updatedLocale);

        return new Response(status: Response::HTTP_NO_CONTENT);
    }

    /**
     * Respond to a syntactically erroneous request.
     * @param \Exception $exception
     * @param string $locale the locale.
     * @return \Symfony\Component\HttpFoundation\Response the response.
     */
    private function respondToBadRequest(\Exception $exception, string $locale): Response
    {
        $message = 'invalidJson'; // NotEncodableValueException

        switch (get_class($exception)) {
            case MissingConstructorArgumentsException::class:
                $message = 'missingProperties';
                break;
            case NotNormalizableValueException::class:
                $message = 'typeError';
        }

        return $this->json(
            new ApiResponse($this->translator->trans($message, locale: $locale)),
            Response::HTTP_BAD_REQUEST
        );
    }

    /**
     * Sends the violations.
     * @param \Symfony\Component\Validator\ConstraintViolationListInterface $violations the violations.
     * @param string $locale the locale.
     * @param int $httpStatusCode the http status code.
     * @return \Symfony\Component\HttpFoundation\Response the response.
     */
    private function sendViolations(
        ConstraintViolationListInterface $violations,
        string $locale,
        int $httpStatusCode = Response::HTTP_UNPROCESSABLE_ENTITY
    ): Response {
        $violationMessages = new Violations();

        foreach ($violations as $violation) {
            /** @var string */
            $violationMessage = $violation->getMessage();

            $violationMessages->add(new Violation(
                $violation->getPropertyPath(),
                $this->translator->trans($violationMessage, [], 'locale', $locale)
            ));
        }

        return $this->json($violationMessages, $httpStatusCode);
    }
}
