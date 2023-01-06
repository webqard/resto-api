<?php

declare(strict_types=1);

namespace App\Controller\Currency;

use App\ApiResource\ApiResponse;
use App\ApiResource\CurrencyInput;
use App\ApiResource\ResourceLink;
use App\ApiResource\Violation;
use App\ApiResource\Violations;
use App\Repository\Currency\CurrencyPostRepository;
use App\State\Currency\CurrencyPostProcessor;
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
 * A controller to POST a currency.
 */
final class CurrencyPostController extends AbstractController
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
     * @var \App\State\Currency\CurrencyPostProcessor the processor.
     */
    private CurrencyPostProcessor $postProcessor;

    /**
     * @var \App\Repository\Currency\CurrencyPostRepository the currency's repository.
     */
    private CurrencyPostRepository $repository;

    /**
     * @var \Symfony\Contracts\Translation\TranslatorInterface the translator.
     */
    private TranslatorInterface $translator;


    // Magic methods :

    /**
     * The constructor.
     * @param \Symfony\Component\Serializer\SerializerInterface $serializer the serializer.
     * @param \Symfony\Component\Validator\Validator\ValidatorInterface $validator the validator.
     * @param \App\State\Currency\CurrencyPostProcessor $postProcessor the processor.
     * @param \App\Repository\Currency\CurrencyPostRepository $repository the currency's repository.
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator the translator.
     */
    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        CurrencyPostProcessor $postProcessor,
        CurrencyPostRepository $repository,
        TranslatorInterface $translator
    ) {
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->postProcessor = $postProcessor;
        $this->repository = $repository;
        $this->translator = $translator;
    }


    // Methods :

    /**
     * Posts a currency.
     * @param \Symfony\Component\HttpFoundation\Request $request the request.
     * @return \Symfony\Component\HttpFoundation\Response the response.
     */
    #[
        OA\Post(
            description: 'Adds a currency.',
            path: '/currencies',
            summary: 'Adds a currency.',
            /** @infection-ignore-all */
            tags: ['Currency']
        ),
        OA\Parameter(ref: '#/components/parameters/Accept-Language'),
        OA\RequestBody(
            content: new OA\JsonContent(ref: '#/components/schemas/CurrencyInput'),
            description: 'The currency that needs to be added.',
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
            ref: '#/components/responses/DELETE_GET_PUTNotAllowed',
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
        Route('/currencies', methods: ['POST'], name: 'currency_post')
    ]
    public function post(Request $request): Response
    {
        try {
            $currencyInput = $this->serializer->deserialize($request->getContent(), CurrencyInput::class, 'json');
        } catch (
            NotEncodableValueException | MissingConstructorArgumentsException | NotNormalizableValueException $exception
        ) {
            return $this->respondToBadRequest($exception, $request->getLocale());
        }

        $violations = $this->validator->validate($currencyInput);

        if (count($violations) > 0) {
            return $this->sendViolations($violations, $request->getLocale());
        }

        $currency = $this->postProcessor->getEntity($currencyInput);
        $unicityViolations = $this->validator->validate($currency);

        if (count($unicityViolations) > 0) {
            return $this->sendViolations($unicityViolations, $request->getLocale(), Response::HTTP_CONFLICT);
        }

        $this->repository->save($currency);

        return $this->json(
            new ResourceLink($this->generateUrl('currency_get', ['id' => $currency->getId()])),
            Response::HTTP_CREATED
        );
    }

    /**
     * Respond to a syntactically erroneous request.
     * @param \Exception $exception
     * @param string $currency the currency.
     * @return \Symfony\Component\HttpFoundation\Response the response.
     */
    private function respondToBadRequest(\Exception $exception, string $currency): Response
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
            new ApiResponse($this->translator->trans($message, locale: $currency)),
            Response::HTTP_BAD_REQUEST
        );
    }

    /**
     * Sends the violations.
     * @param \Symfony\Component\Validator\ConstraintViolationListInterface $violations the violations.
     * @param string $currency the currency.
     * @param int $httpStatusCode the http status code.
     * @return \Symfony\Component\HttpFoundation\Response the response.
     */
    private function sendViolations(
        ConstraintViolationListInterface $violations,
        string $currency,
        int $httpStatusCode = Response::HTTP_UNPROCESSABLE_ENTITY
    ): Response {
        $violationMessages = new Violations();

        foreach ($violations as $violation) {
            /** @var string */
            $violationMessage = $violation->getMessage();

            $violationMessages->add(new Violation(
                $violation->getPropertyPath(),
                $this->translator->trans($violationMessage, [], 'currency', $currency)
            ));
        }

        return $this->json($violationMessages, $httpStatusCode);
    }
}
