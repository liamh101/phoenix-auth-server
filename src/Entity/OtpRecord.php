<?php

namespace App\Entity;

use App\Enum\Digit;
use App\Enum\Step;
use Symfony\Component\Validator\Constraints as Assert;
use App\Enum\Algorithm;
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
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 1,
        max: 255,
    )]
    public ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 1,
        max: 255,
    )]
    public ?string $secret = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Choice(callback: [Step::class, 'choiceValidation'])]
    public ?int $totpStep = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Choice(callback: [Digit::class, 'choiceValidation'])]
    public ?int $otpDigits = null;

    #[ORM\Column(length: 10, nullable: true)]
    #[Assert\Choice(callback: [Algorithm::class, 'choiceValidation'])]
    public ?string $totpAlgorithm = null;

    #[ORM\Column(
        insertable: false,
        updatable: false,
        generated: "ALWAYS"
    )]
    public string $syncHash;
}
