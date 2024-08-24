<?php

declare(strict_types=1);

namespace App\Controller;

use App\ApiResource\ApiResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * A base controller to send translation errors
 * from POST and PUT methods.
 */
abstract class UnprocessableTranslationController extends SendErrorController
{
    // Methods :

    /**
     * Sends the violations.
     * @param string $unTranslatedMessage the untranslated message.
     * @param string $translationDomain the translation domain.
     * @param string $locale the locale.
     * @param array $parameters the parameters.
     * @return \Symfony\Component\HttpFoundation\Response the response.
     */
    protected function sendUnprocessableTranslationError(
        string $unTranslatedMessage,
        string $translationDomain,
        string $locale,
        array $parameters = []
    ): Response {
        $message = $this->translator->trans(
            $unTranslatedMessage,
            $parameters,
            $translationDomain,
            $locale
        );

        return $this->json(new ApiResponse($message), Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
