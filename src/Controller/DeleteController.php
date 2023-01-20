<?php

declare(strict_types=1);

namespace App\Controller;

use App\ApiResource\ApiResponse;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * A controller to DELETE a resource.
 */
abstract class DeleteController extends AbstractController
{
    // Properties :

    /**
     * @var \Doctrine\ORM\EntityRepository the repository.
     */
    private EntityRepository $repository;

    /**
     * @var \Symfony\Contracts\Translation\TranslatorInterface the translator.
     */
    private TranslatorInterface $translator;


    // Magic methods :

    /**
     * The constructor.
     * @param \Doctrine\ORM\EntityRepository $repository the entity's repository.
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator the translator.
     */
    protected function __construct(
        EntityRepository $repository,
        TranslatorInterface $translator
    ) {
        $this->repository = $repository;
        $this->translator = $translator;
    }


    // Methods :

    /**
     * Deletes a resource.
     * @param \Symfony\Component\HttpFoundation\Request $request the request.
     * @param string $id the identifier.
     * @return \Symfony\Component\HttpFoundation\Response the response.
     */
    protected function delete(Request $request, string $id): Response
    {
        $entity = $this->repository->find($id);

        if ($entity === null) {
            $message = $this->translator->trans('notFound', locale: $request->getLocale());

            return $this->json(
                new ApiResponse($message),
                Response::HTTP_NOT_FOUND
            );
        }

        $this->repository->delete($entity);

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
