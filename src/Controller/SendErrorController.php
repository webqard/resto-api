<?php

declare(strict_types=1);

namespace App\Controller;

use App\ApiResource\ApiResponse;
use App\ApiResource\Violation;
use App\ApiResource\Violations;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\MissingConstructorArgumentsException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * A base controller to send errors
 * from POST and PUT methods.
 */
abstract class SendErrorController extends AbstractController
{
    // Properties :

    /**
     * @var \Symfony\Contracts\Translation\TranslatorInterface the translator.
     */
    protected TranslatorInterface $translator;


    // Magic methods :

    /**
     * The constructor.
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator the translator.
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }


    // Methods :

    /**
     * Respond to a syntactically erroneous request.
     * @param \Exception $exception
     * @param string $locale the locale.
     * @return \Symfony\Component\HttpFoundation\Response the response.
     */
    protected function respondToBadRequest(\Exception $exception, string $locale): Response
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
    protected function sendViolations(
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
