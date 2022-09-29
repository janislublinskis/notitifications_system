<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\NotificationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Validator as CustomAssert;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
    ],
    normalizationContext: ['groups' => ['notification.read']],
    denormalizationContext: ['groups' => ['notification.write']],
    paginationClientEnabled: true,
    paginationItemsPerPage: 5
)]
#[ApiFilter(SearchFilter::class, properties: ['clientId' => 'exact'])]
#[CustomAssert\NotificationContentLength]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['notification.read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['notification.read', 'notification.write'])]
    private ?Client $clientId = null;

    #[ORM\Column(length: 255)]
    #[Assert\Choice(['sms', 'email'])]
    #[Groups(['notification.read', 'notification.write'])]
    private ?string $channel = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['notification.read', 'notification.write'])]
    private ?string $content = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClientId(): ?Client
    {
        return $this->clientId;
    }

    public function setClientId(?Client $clientId): self
    {
        $this->clientId = $clientId;

        return $this;
    }

    public function getChannel(): ?string
    {
        return $this->channel;
    }

    public function setChannel(string $channel): self
    {
        $this->channel = $channel;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }
}
