<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ReclamationRepository::class)
 */
class Reclamation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
      * @Groups({"rec"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="reclamations")
      * @Groups({"rec"})
     */
    private $user;

    /**
     * @ORM\Column(type="datetime", nullable=true , options={"default": "CURRENT_TIMESTAMP"} )
      * @Groups({"rec"})
     */
    private $reclamationDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
      * @Groups({"rec"})
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * * @Assert\NotBlank
      * @Groups({"rec"})
     */
    private $typeReclamation;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="please enter an object")
     * @Assert\Length(min=10)
      * @Groups({"rec"})
     */
    private $object;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * * @Assert\NotBlank(message="please enter a description")
     * @Assert\Length(min=10)
      * @Groups({"rec"})
     */
    private $description;

    /**
     * @ORM\OneToOne(targetEntity=Response::class, mappedBy="reclamation", cascade={"persist", "remove"})
     */
    private $response;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getReclamationDate(): ?\DateTimeInterface
    {
        return $this->reclamationDate;
    }

    public function setReclamationDate(?\DateTimeInterface $reclamationDate): self
    {
        $this->reclamationDate = $reclamationDate;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getTypeReclamation(): ?string
    {
        return $this->typeReclamation;
    }

    public function setTypeReclamation(?string $typeReclamation): self
    {
        $this->typeReclamation = $typeReclamation;

        return $this;
    }

    public function getObject(): ?string
    {
        return $this->object;
    }

    public function setObject(string $object): self
    {
        $this->object = $object;

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

    public function getResponse(): ?Response
    {
        return $this->response;
    }

    public function setResponse(?Response $response): self
    {
        // unset the owning side of the relation if necessary
        if ($response === null && $this->response !== null) {
            $this->response->setReclamation(null);
        }

        // set the owning side of the relation if necessary
        if ($response !== null && $response->getReclamation() !== $this) {
            $response->setReclamation($this);
        }

        $this->response = $response;

        return $this;
    }
    public function __construct()
    {
        $this->reclamationDate = new \DateTime();
    }
}
