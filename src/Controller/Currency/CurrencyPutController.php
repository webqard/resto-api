<?php

declare(strict_types=1);

namespace App\Controller\Currency;

use App\ApiResource\ApiResponse;
use App\ApiResource\CurrencyInput;
use App\ApiResource\Violation;
use App\ApiResource\Violations;
use App\Repository\Currency\CurrencyPutRepository;
use App\State\Currency\CurrencyPutProcessor;
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
 * A controller to PUT a currency.
 */
final class CurrencyPutController extends AbstractController
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
     * @var \App\State\Currency\CurrencyPutProcessor the processor.
     */
    private CurrencyPutProcessor $putProcessor;

    /**
     * @var \App\Repository\Currency\CurrencyPutRepository the currency's repository.
     */
    private CurrencyPutRepository $repository;

    /**
     * @var \Symfony\Contracts\Translation\TranslatorInterface the translator.
     */
    private TranslatorInterface $translator;


    // Magic methods :

    /**
     * The constructor.
     * @param \Symfony\Component\Serializer\SerializerInterface $serializer the serializer.
     * @param \Symfony\Component\Validator\Validator\ValidatorInterface $validator the validator.
     * @param \App\State\Currency\CurrencyPutProcessor $putProcessor the processor.
     * @param \App\Repository\Currency\CurrencyPutRepository $repository the currency's repository.
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator the translator.
     */
    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        CurrencyPutProcessor $putProcessor,
        CurrencyPutRepository $repository,
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
     * Puts a currency.
     * @param \Symfony\Component\HttpFoundation\Request $request the request.
     * @param string $id the identifier.
     * @return \Symfony\Component\HttpFoundation\Response the response.
     */
    #[
        OA\Put(
            description: 'Updates a currency.',
            path: '/currencies/{id}',
            summary: 'Updates a currency.',
            /** @infection-ignore-all */
            tags: ['Currency']
        ),
        OA\Parameter(ref: '#/components/parameters/id'),
        OA\Parameter(ref: '#/components/parameters/Accept-Language'),
        OA\RequestBody(
            content: new OA\JsonContent(ref: '#/components/schemas/CurrencyInput'),
            description: 'The currency that needs to be added.',
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
        Route('/currencies/{id}', methods: ['PUT'], name: 'currency_put')
    ]
    public function put(Request $request, string $id): Response
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

        $updatedCurrency = $this->putProcessor->getEntity($currency, $currencyInput);
        $unicityViolations = $this->validator->validate($updatedCurrency);

        if (count($unicityViolations) > 0) {
            return $this->sendViolations($unicityViolations, $request->getLocale(), Response::HTTP_CONFLICT);
        }

        $this->repository->save($updatedCurrency);

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
                $this->translator->trans($violationMessage, [], 'currency', $locale)
            ));
        }

        return $this->json($violationMessages, $httpStatusCode);
    }
}
