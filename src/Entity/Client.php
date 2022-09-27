<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ClientRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
#[UniqueEntity('email')]
#[ApiResource(
    normalizationContext: ['groups' => ['client.read']],
    denormalizationContext: ['groups' => ['client.write']],
    paginationClientEnabled: true,
    paginationItemsPerPage: 5
)]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['client.read'])]
    private ?int $id = null;

    #[ORM\Column(length: 32)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 32)]
    #[Assert\Regex(
        pattern: '/^[a-z]+$/i',
        htmlPattern: '^[a-zA-Z]+$'
    )]
    #[Groups(['client.read', 'client.write'])]
    private ?string $firstName = null;

    #[ORM\Column(length: 32)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 32)]
    #[Assert\Regex(
        pattern: '/^[a-z]+$/i',
        htmlPattern: '^[a-zA-Z]+$'
    )]
    #[Groups(['client.read', 'client.write'])]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Groups(['client.read', 'client.write'])]
    private ?string $email = null;

    #[ORM\Column(type: 'integer', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 12)]
    #[Groups(['client.read', 'client.write'])]
    private ?int $phoneNumber = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return '+' . $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }
}
