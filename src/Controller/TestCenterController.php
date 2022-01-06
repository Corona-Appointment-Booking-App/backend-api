<?php

declare(strict_types=1);

namespace App\Controller;

use App\DataTransferObject\TestCenterDto;
use App\Entity\TestCenter;
use App\Entity\User;
use App\Service\Hydrator\TestCenterHydratorInterface;
use App\Service\TestCenterServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestCenterController extends AbstractApiController
{
    private TestCenterServiceInterface $testCenterService;

    private TestCenterHydratorInterface $testCenterHydrator;

    public function __construct(
        TestCenterServiceInterface $testCenterService,
        TestCenterHydratorInterface $testCenterHydrator
    ) {
        $this->testCenterService = $testCenterService;
        $this->testCenterHydrator = $testCenterHydrator;
    }

    #[Route('/api/test-center/seoSlug/{seoSlug}', name: 'api.test-center.get.seo-slug')]
    public function getTestCenterBySeoSlug(string $seoSlug): Response
    {
        $testCenter = $this->testCenterService->getTestCenterBySeoSlug($seoSlug);

        return $this->buildJsonSuccessResponse($testCenter);
    }

    #[Route('/api/test-center/id/{id}', name: 'api.test-center.get.id', methods: ['GET'])]
    public function getTestCenterById(string $id): Response
    {
        $testCenter = $this->testCenterService->getTestCenterByUuid($id);

        return $this->buildJsonSuccessResponse($testCenter);
    }

    #[Route('/api/test-center/list/paginated/{page}/{perPage}', name: 'api.test-center.get.all.paginated', methods: ['GET'])]
    public function getAllTestCentersWithPagination(int $page = self::DEFAULT_PAGE, int $perPage = self::DEFAULT_LIST_LIMIT): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        $result = $this->testCenterService->getRecentTestCentersWithPagination($page, $perPage);

        return $this->buildJsonPaginationResponse($result, [TestCenter::GROUP_READ]);
    }

    #[Route('/api/test-center/create', name: 'api.test-center.create', methods: ['POST'])]
    public function createTestCenter(Request $request): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        $testCenterDto = $this->createTestCenterDto($request);
        $createdTestCenter = $this->testCenterService->createTestCenter($testCenterDto);

        return $this->buildJsonSuccessResponse($createdTestCenter);
    }

    #[Route('/api/test-center/update', name: 'api.test-center.update', methods: ['POST'])]
    public function updateTestCenter(Request $request): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        $testCenterDto = $this->createTestCenterDto($request);
        $updatedTestCenter = $this->testCenterService->updateTestCenter($testCenterDto);

        return $this->buildJsonSuccessResponse($updatedTestCenter);
    }

    private function createTestCenterDto(Request $request): TestCenterDto
    {
        $payload = $this->getPayloadFromRequest($request);

        return $this->testCenterHydrator->hydrateFromArray($payload);
    }
}
