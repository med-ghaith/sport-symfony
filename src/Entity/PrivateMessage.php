<?php

namespace App\Entity;

use App\Repository\PrivateMessageRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass=PrivateMessageRepository::class)
 * @ORM\Table(name="private_message")
 */
class PrivateMessage
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("message:read")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="privateMessagesFirst", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false,onDelete="CASCADE")
     * @ORM\Column(name="id_first_user_id")
     * @Groups("message:read")
     */
    private $idFirstUser;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="privateMessagesSecond", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false,onDelete="CASCADE")
     * @ORM\Column(name="id_second_user_id")
     * @Groups("message:read")
     */
    private $idSecondUser;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("message:read")
     */
    private $content;

    /**
     * @ORM\Column(name="created_at", type="datetime", options={"default": "CURRENT_TIMESTAMP"},nullable="true")
     * @Groups("message:read")
     */
    private $createdAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdFirstUser()//: ?User
    {
        return $this->idFirstUser;
    }

    public function setIdFirstUser(int $idFirstUser): self
    {
        $this->idFirstUser = $idFirstUser;

        return $this;
    }

    public function getIdSecondUser()//: ?User
    {
        return $this->idSecondUser;
    }

    public function setIdSecondUser(int $idSecondUser): self
    {
        $this->idSecondUser = $idSecondUser;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }



    public function setCreatedAt(): self
    {
        $this->createdAt = new \DateTime("now");
        return $this;
    }



//    public function getCreatedAt(): ?\DateTimeImmutable
//    {
//        return $this->createdAt;
//    }
//
//    public function setCreatedAt(\DateTimeImmutable $createdAt): self
//    {
//        $this->createdAt = $createdAt;
//
//        return $this;
//    }
}
