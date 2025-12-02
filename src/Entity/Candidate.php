<?php

namespace App\Entity;

use App\Repository\CandidateRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: CandidateRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Candidate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column]
    private ?bool $hasExperience = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $experienceDetails = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $availabilityDate = null;

    #[ORM\Column]
    private ?bool $isAvailableImmediately = false;

    #[ORM\Column]
    private ?bool $consentRGPD = false;

    #[ORM\Column(length: 20)]
    private ?string $status = 'draft';

    private string $currentStep = 'personal';

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function isHasExperience(): ?bool
    {
        return $this->hasExperience;
    }

    public function setHasExperience(bool $hasExperience): static
    {
        $this->hasExperience = $hasExperience;

        return $this;
    }

    public function getExperienceDetails(): ?string
    {
        return $this->experienceDetails;
    }

    public function setExperienceDetails(?string $experienceDetails): static
    {
        $this->experienceDetails = $experienceDetails;

        return $this;
    }

    public function getAvailabilityDate(): ?\DateTimeInterface
    {
        return $this->availabilityDate;
    }

    public function setAvailabilityDate(?\DateTimeInterface $availabilityDate): static
    {
        $this->availabilityDate = $availabilityDate;

        return $this;
    }

    public function isAvailableImmediately(): ?bool
    {
        return $this->isAvailableImmediately;
    }

    public function setIsAvailableImmediately(bool $isAvailableImmediately): static
    {
        $this->isAvailableImmediately = $isAvailableImmediately;

        return $this;
    }

    public function isConsentRGPD(): ?bool
    {
        return $this->consentRGPD;
    }

    public function setConsentRGPD(bool $consentRGPD): static
    {
        $this->consentRGPD = $consentRGPD;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCurrentStep(): string
    {
        return $this->currentStep;
    }

    public function setCurrentStep(string $currentStep): static
    {
        $this->currentStep = $currentStep;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[Assert\Callback(groups: ['availability'])]
    public function validate(ExecutionContextInterface $context): void
    {
        if (!$this->isAvailableImmediately && null === $this->availabilityDate) {
            $context->buildViolation('Veuillez indiquer une date de disponibilité ou cocher "Disponible immédiatement".')
                ->atPath('availabilityDate')
                ->addViolation();
        }
    }
}
