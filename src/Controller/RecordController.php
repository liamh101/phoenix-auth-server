<?php

namespace App\Controller;

use App\Entity\OtpRecord;
use App\Repository\OtpRecordRepository;
use App\ValueObject\ApiResponse\ErrorResponse;
use App\ValueObject\ApiResponse\VersionOneBase;
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
    ) {
    }

    #[Route('/{id}', name: 'fetch', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function fetch(int $id): Response
    {
        $record = $this->recordRepository->find($id);

        if (!$record instanceof OtpRecord) {
            return $this->json(new ErrorResponse(ErrorResponse::generateNotFoundErrorMessage(OtpRecord::class)), 404);
        }

        return $this->json(new VersionOneBase($record));
    }

    #[Route('', name: 'post', methods: ['POST'])]
    public function post(
        #[MapRequestPayload] OtpRecord $record
    ): Response {
        $this->recordRepository->save($record);

        if (!$record->id) {
            throw new \RuntimeException('Record not returned correctly');
        }

        $hashedRecord = $this->recordRepository->getSingleAccountHash($record->id);

        if (!$hashedRecord) {
            return $this->json(new ErrorResponse(ErrorResponse::generateNotFoundErrorMessage(OtpRecord::class)), 404);
        }

        return $this->json(new VersionOneBase($hashedRecord));
    }

    #[Route('/hashes', name: 'hashes', methods: ['GET'])]
    public function getHashedRecords(): Response
    {
        return $this->json(new VersionOneBase($this->recordRepository->getAccountHashes()));
    }
}
