<?php

namespace App\Controller;

use App\Entity\OtpRecord;
use App\Repository\OtpRecordRepository;
use App\ValueObject\ApiResponse\VersionOneBase;
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
        return $this->json(new VersionOneBase($this->recordRepository->find($id)));
    }

    #[Route('', name: 'post', methods: ['POST'])]
    public function post(
        #[MapRequestPayload] OtpRecord $record
    ): Response {

        $this->recordRepository->save($record);
        return $this->json(new VersionOneBase($record));
    }

    #[Route('/hashes', name: 'hashes', methods: ['GET'])]
    public function getHashedRecords(): Response
    {
        return $this->json(new VersionOneBase($this->recordRepository->getAccountHashes()));
    }
}
