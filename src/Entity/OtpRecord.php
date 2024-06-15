<?php

namespace App\Entity;

use App\DoctrineType\Algorithm;
use App\Enum\Algorithm as TotpAlgorithm;
use App\Repository\OtpRecordRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OtpRecordRepository::class)]
class OtpRecord
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(length: 255)]
    public ?string $name = null;

    #[ORM\Column(length: 255)]
    public ?string $secret = null;

    #[ORM\Column]
    public ?int $totpStep = null;

    #[ORM\Column]
    public ?int $otpDigits = null;

    #[ORM\Column(length: 10, nullable: true)]
    public ?string $totpAlgorithm = null;

    #[ORM\Column(
        insertable: false,
        updatable: false,
        generated: "ALWAYS")]
    public string $syncHash;
}
