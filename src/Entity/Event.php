<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=EventRepository::class)
 */
class Event
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"event","reservation"})
     */
    private $id;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Assert\NotBlank(message="You cannot leave this field Empty")
     * @Assert\GreaterThan("today" ,message="Input Date must be in the future")
     * @Groups({"event"})

     */
    private $startDate;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Assert\NotBlank(message="You cannot leave this field Empty")
     * @Assert\GreaterThan("today" ,message="Input Date must be in the future")
     * @Groups({"event"})

     */
    private $endDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="You cannot leave this field Empty")
     * @Groups({"event"})

     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Planning::class, inversedBy="event")
     * @Assert\NotBlank(message="You cannot leave this field Empty")
     * @Groups({"event"})

     */
    private $planning;

    /**
     * @ORM\OneToMany(targetEntity=Reservation::class, mappedBy="event")
     * @Groups({"event"})

     */
    private $reservations;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\NotBlank(message="You cannot leave this field Empty")
     * @Groups({"event"})

     */
    private $nombreReservation;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank
     * @Assert\Positive(message="Please enter a valid number")
     * @Groups({"event"})

     */
    private $fees;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="You cannot leave this field Empty")
     *  * @Assert\Regex(
     *     pattern="/\d/",
     *     match=false,
     *     message="Your Category cannot contain a number"
     * )
     * @Groups({"event"})

     */
    private $category;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"event"})

     */
    private $imageUrl;


    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
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

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPlanning(): ?Planning
    {
        return $this->planning;
    }

    public function setPlanning(?Planning $planning): self
    {
        $this->planning = $planning;

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations[] = $reservation;
            $reservation->setEvent($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getEvent() === $this) {
                $reservation->setEvent(null);
            }
        }

        return $this;
    }

    public function getNombreReservation(): ?int
    {
        return $this->nombreReservation;
    }

    public function setNombreReservation(?int $nombreReservation): self
    {
        $this->nombreReservation = $nombreReservation;

        return $this;
    }

    public function getFees(): ?string
    {
        return $this->fees;
    }

    public function setFees(?string $fees): self
    {
        $this->fees = $fees;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }
    public function __toString() {
        return (string) $this->id;
    }
}
