<?php

namespace App\Entity;

use App\Repository\ExerciceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ExerciceRepository::class)
 */
class Exercice
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("exercise:read")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message=" Name shouldn't be blank ")
     * @Groups("exercise:read")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("exercise:read")
     */
    private $imageUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank
     * @Groups("exercise:read")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, columnDefinition="ENUM('easy', 'medium','hard')")
     * @Assert\NotBlank
     * @Groups("exercise:read")
     */
    private $difficultyLevel;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Range(
     *      min = 3,
     *      max = 10,
     *      notInRangeMessage = "Number of sets should be between {{ min }} and {{ max }}",
     * )
     * @Groups("exercise:read")
     */
    private $numberOfSets;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Range(
     *      min = 5,
     *      max = 45,
     *      notInRangeMessage = "Number of repetition should be between {{ min }} and {{ max }}",
     * )
     * @Groups("exercise:read")
     */
    private $numberOfRepetition;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank
     * @Groups("exercise:read")
     */
    private $restTime;

    /**
     * @ORM\ManyToMany(targetEntity=Muscle::class, inversedBy="exercices")
     * @Groups("exercise:read")
     */
    private $muscles;

    /**
     * @ORM\ManyToMany(targetEntity=Equipment::class, mappedBy="exercices")
     * @Assert\NotBlank
     * @Groups("exercise:read")
     */
    private $equipments;

    public function __construct()
    {
        $this->muscles = new ArrayCollection();
        $this->equipments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDifficultyLevel(): ?string
    {
        return $this->difficultyLevel;
    }

    public function setDifficultyLevel(?string $difficultyLevel): self
    {
        $this->difficultyLevel = $difficultyLevel;

        return $this;
    }

    public function getNumberOfSets(): ?int
    {
        return $this->numberOfSets;
    }

    public function setNumberOfSets(?int $numberOfSets): self
    {
        $this->numberOfSets = $numberOfSets;

        return $this;
    }

    public function getNumberOfRepetition(): ?int
    {
        return $this->numberOfRepetition;
    }

    public function setNumberOfRepetition(?int $numberOfRepetition): self
    {
        $this->numberOfRepetition = $numberOfRepetition;

        return $this;
    }

    public function getRestTime(): ?string
    {
        return $this->restTime;
    }

    public function setRestTime(?string $restTime): self
    {
        $this->restTime = $restTime;

        return $this;
    }

    /**
     * @return Collection<int, Muscle>
     */
    public function getMuscles(): Collection
    {
        return $this->muscles;
    }

    public function addMuscle(Muscle $muscle): self
    {
        if (!$this->muscles->contains($muscle)) {
            $this->muscles[] = $muscle;
        }

        return $this;
    }

    public function removeMuscle(Muscle $muscle): self
    {
        $this->muscles->removeElement($muscle);

        return $this;
    }

    /**
     * @return Collection<int, Equipment>
     */
    public function getEquipments(): Collection
    {
        return $this->equipments;
    }

    public function addEquipment(Equipment $equipment): self
    {
        if (!$this->equipments->contains($equipment)) {
            $this->equipments[] = $equipment;
            $equipment->addExercice($this);
        }

        return $this;
    }

    public function removeEquipment(Equipment $equipment): self
    {
        if ($this->equipments->removeElement($equipment)) {
            $equipment->removeExercice($this);
        }

        return $this;
    }
}
