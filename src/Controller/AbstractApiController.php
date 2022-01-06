<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\EntityInterface;
use App\Repository\Result\PaginatedItemsResult;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractApiController extends AbstractController
{
    protected const DEFAULT_PAGE = 1;
    protected const DEFAULT_LIST_LIMIT = 20;

    protected function buildJsonSuccessResponse(EntityInterface $entity, int $status = Response::HTTP_OK, array $headers = []): JsonResponse
    {
        return $this->json([
            'success' => true,
            'data' => $entity,
        ],
            $status,
            $headers,
            [
                'groups' => $entity->getSerializationGroups(),
                'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS | \JSON_UNESCAPED_UNICODE,
            ]
        );
    }

    protected function buildJsonPaginationResponse(PaginatedItemsResult $result, array $groups, int $status = Response::HTTP_OK, array $headers = []): JsonResponse
    {
        $data = [
            'items' => $result->getPaginator()->getIterator(),
            'pagination' => [
                'currentPage' => $result->getCurrentPage(),
                'itemsPerPage' => $result->getItemsPerPage(),
                'totalItems' => $result->getTotalItems(),
                'totalPages' => $result->getTotalPages(),
            ],
        ];

        return $this->json([
            'success' => true,
            'data' => $data,
        ],
            $status,
            $headers,
            [
                'groups' => $groups,
                'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS | \JSON_UNESCAPED_UNICODE,
            ]
        );
    }

    protected function getPayloadFromRequest(Request $request): array
    {
        try {
            return $request->toArray() ?? [];
        } catch (\Throwable $e) {
            return [];
        }
    }
}
