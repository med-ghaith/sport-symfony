<?php

namespace App\Entity;

use App\Repository\SubscriptionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Entity(repositoryClass=SubscriptionRepository::class)
 */
class Subscription
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"gymread","subread"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="subscriptions")
     * @Groups({"gymread","subread"})
     */
    private $member;

    /**
     * @ORM\ManyToOne(targetEntity=Gym::class, inversedBy="subscriptions")
     * @Groups({"subread"})
     */
    private $gym;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"gymread","subread"})
     */
    private $startDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"gymread","subread"})
     * @Assert\NotBlank( message = "Ne peut pas etre vide")
     */
    private $validity;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMember(): ?User
    {
        return $this->member;
    }

    public function setMember(?User $member): self
    {
        $this->member = $member;

        return $this;
    }

    public function getGym(): ?Gym
    {
        return $this->gym;
    }

    public function setGym(?Gym $gym): self
    {
        $this->gym = $gym;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getValidity(): ?string
    {
        return $this->validity;
    }

    public function setValidity(?string $validity): self
    {
        $this->validity = $validity;

        return $this;
    }
}
