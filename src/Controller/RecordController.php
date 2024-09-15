<?php

namespace App\Controller;

use App\Entity\OtpRecord;
use App\Repository\OtpRecordRepository;
use App\Service\RecordService;
use App\ValueObject\ApiResponse\ErrorResponse;
use App\ValueObject\ApiResponse\VersionOneBase;
use App\ValueObject\RepoResponse\OtpRecord\AccountManifest;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/records', name: 'record_')]
class RecordController extends AbstractController
{
    public function __construct(
        private readonly OtpRecordRepository $recordRepository,
        private readonly RecordService $recordService,
    ) {
    }

    #[Route('/{id}', name: 'fetch', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function fetch(int $id): Response
    {
        $record = $this->recordRepository->find($id);

        if (!$record instanceof OtpRecord) {
            return $this->json(new ErrorResponse(ErrorResponse::generateNotFoundErrorMessage(OtpRecord::class)), 404);
        }

        return $this->json(new VersionOneBase($record->formattedResponse()));
    }

    #[Route('', name: 'post', methods: ['POST'])]
    public function post(
        #[MapRequestPayload] OtpRecord $record
    ): Response {
        $hash = $this->recordService->generateRecordHash($record);

        $existingHash = $this->recordRepository->findExistingAccountHash($hash);
        if ($existingHash) {
            return $this->json(new VersionOneBase($existingHash->formatResponse()));
        }

        $record->syncHash = $hash;
        $this->recordRepository->save($record);

        if (!$record->id) {
            throw new \RuntimeException('Record not returned correctly');
        }

        $hashedRecord = $this->recordRepository->getSingleAccountHash($record->id);

        if (!$hashedRecord) {
            return $this->json(new ErrorResponse(ErrorResponse::generateNotFoundErrorMessage(OtpRecord::class)), Response::HTTP_NOT_FOUND);
        }

        return $this->json(new VersionOneBase($hashedRecord->formatResponse()));
    }

    #[Route('/{id}', name: 'put', requirements: ['id' => '\d+'], methods: ['PUT'])]
    public function put(
        int $id,
        #[MapRequestPayload] OtpRecord $record
    ): Response {
        $existingRecord = $this->recordRepository->find($id);

        if (!$existingRecord instanceof OtpRecord) {
            return $this->json(new ErrorResponse(ErrorResponse::generateNotFoundErrorMessage(OtpRecord::class)), 404);
        }

        $updatedRecord = $this->recordService->updateExistingRecord($existingRecord, $record);
        $this->recordRepository->save($updatedRecord);
        $hashedRecord = $this->recordRepository->getSingleAccountHash($updatedRecord->id);

        if (!$hashedRecord) {
            return $this->json(new ErrorResponse(ErrorResponse::generateNotFoundErrorMessage(OtpRecord::class)), Response::HTTP_NOT_FOUND);
        }

        return $this->json(new VersionOneBase($hashedRecord->formatResponse()));
    }

    #[Route('/{id}', name: 'delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        $existingRecord = $this->recordRepository->find($id);

        if (!$existingRecord instanceof OtpRecord) {
            return $this->json(new ErrorResponse(ErrorResponse::generateNotFoundErrorMessage(OtpRecord::class)), 404);
        }

        $this->recordRepository->delete($existingRecord);

        return $this->json(new VersionOneBase($existingRecord->formattedResponse()));
    }

    #[Route('/manifest', name: 'manifest', methods: ['GET'])]
    public function getAccountManifest(): Response
    {
        return $this->json(
            new VersionOneBase(
                array_map(static fn (AccountManifest $manifest) => $manifest->formatResponse(), $this->recordRepository->getAccountManifest())
            )
        );
    }
}
