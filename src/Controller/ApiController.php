<?php

declare(strict_types=1);

namespace App\Controller;

use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * A default controller to generate the API.
 */
#[
    OA\Info(
        description: "A restaurant API to manage courses, drinks and set menus.",
        title: "Resto API",
        version: "alpha"
    )
]
final class ApiController extends AbstractController
{
    /**
     * The openapi documentation.
     * @return \Symfony\Component\HttpFoundation\Response the response.
     */
    #[Route('/', methods: 'GET', name: 'api')]
    public function api(): Response
    {
        return $this->redirect('/index.html', Response::HTTP_MOVED_PERMANENTLY);
    }
}
