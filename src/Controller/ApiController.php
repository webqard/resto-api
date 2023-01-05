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
    ),
    OA\Parameter(
        description: 'The locale in which the messages (not the items) will be translated into.',
        example: 'en-GB, fr-FR',
        in: 'header',
        name: 'Accept-Language'
    ),
    OA\Parameter(
        description: 'The identifier of the item.',
        example : 1,
        in: 'path',
        name: 'id',
        required: true
    ),
    OA\Response(
        content: new OA\JsonContent(ref: '#/components/schemas/ResourceLink'),
        description: 'When the item is added successfully.',
        response: '201'
    ),
    OA\Response(
        description: 'When the item is deleted successfully.',
        response: '204'
    ),
    OA\Response(
        content: new OA\JsonContent(ref: '#/components/schemas/ApiResponse'),
        description: 'When the request body is an invalid or empty json.',
        response: 'InvalidJsonBody'
    ),
    OA\Response(
        content: new OA\JsonContent(ref: '#/components/schemas/ApiResponse'),
        description: 'When item does not exist.',
        response: '404'
    ),
    OA\Response(
        description: "When the method is not allowed.",
        headers: [
            new OA\Header(
                description: 'DELETE, GET, PUT',
                header: 'Allow',
                schema: new OA\Schema(type: 'string')
            )
        ],
        response: 'POSTNotAllowed'
    ),
    OA\Response(
        description: "When the method is not allowed.",
        headers: [
            new OA\Header(
                description: 'POST',
                header: 'Allow',
                schema: new OA\Schema(type: 'string')
            )
        ],
        response: 'DELETE_GET_PUTNotAllowed'
    ),
    OA\Response(
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items('#/components/schemas/Violation')
        ),
        description: 'When the item is already added.',
        response: '409'
    ),
    OA\Response(
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items('#/components/schemas/Violation')
        ),
        description: 'When there is an error in the datas.',
        response: '422'
    ),
    OA\Response(
        description: 'When an unexpected error occured.',
        response: '500'
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
