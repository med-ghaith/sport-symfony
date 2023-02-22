<?php

namespace App\Entity;

use App\Repository\CourseRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Entity(repositoryClass=CourseRepository::class)
 */
class Course
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"courseread"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Gym::class, inversedBy="courses")
     * @Groups({"courseread"})
     */
    private $gym;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"courseread"})
     * @Assert\NotBlank(
     *    message = "name ne peut pas etre vide")
     * @Assert\Length(
     *      min = 10,
     *      max = 150,
     *      minMessage = "name doit au moins depasser {{ limit }} caractere ",
     *      maxMessage = "name ne doit pas  depasser {{ limit }} caractere")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"courseread"})
     * @Assert\NotBlank( message = "description ne peut pas etre vide")
     * @Assert\Length(
     *      min = 20,
     *      max = 255,
     *      minMessage = "description doit au moins depasser {{ limit }} caractere ",
     *      maxMessage = "description ne doit pas  depasser {{ limit }} caractere")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"courseread"})
     */
    private $video;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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


    public function getVideo(): ?string
    {
        return $this->video;
    }

    public function setVideo(?string $video): self
    {
        $this->video = $video;

        return $this;
    }
}
