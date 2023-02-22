<?php

namespace App\Entity;

use App\Repository\GymRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Entity(repositoryClass=GymRepository::class)
 */
class Gym
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"gymread","subread","courseread"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"gymread","subread","courseread"})
     * @Assert\NotBlank(
     *    message = "Name ne peut pas etre vide")
     * @Assert\Length(
     *      min = 10,
     *      max = 255,
     *      minMessage = "Name doit au moins depasser {{ limit }} caractere ",
     *      maxMessage = "Name ne doit pas  depasser {{ limit }} caractere")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"gymread","subread","courseread"})
     * @Assert\NotBlank( message = "Description ne peut pas etre vide")
     * @Assert\Length(
     *      min = 20,
     *      max = 10000,
     *      minMessage = "Description doit au moins depasser {{ limit }} caractere ",
     *      maxMessage = "Description ne doit pas  depasser {{ limit }} caractere")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"gymread","subread","courseread"})
     * @Assert\NotBlank( message = "Adresse ne peut pas etre vide")
     * @Assert\Length(
     *      min = 20,
     *      max = 255,
     *      minMessage = "Adresse doit au moins depasser {{ limit }} caractere ",
     *      maxMessage = "Adresse ne doit pas  depasser {{ limit }} caractere")
     */
    private $location;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="gym", cascade={"persist", "remove"})
     * @Groups({"gymread","subread","courseread"})
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=Subscription::class, mappedBy="gym")
     * @Groups({"gymread"})
     */
    private $subscriptions;

    /**
     * @ORM\OneToMany(targetEntity=Course::class, mappedBy="gym")
     */
    private $courses;

    /**
     * @ORM\OneToMany(targetEntity=Review::class, mappedBy="gym")
     */
    private $reviews;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="gym")
     */
    private $comments;

    public function __construct()
    {
        $this->subscriptions = new ArrayCollection();
        $this->courses = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
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

    /**
     * @return Collection<int, Subscription>
     */
    public function getSubscriptions(): Collection
    {
        return $this->subscriptions;
    }

    public function addSubscription(Subscription $subscription): self
    {
        if (!$this->subscriptions->contains($subscription)) {
            $this->subscriptions[] = $subscription;
            $subscription->setGym($this);
        }

        return $this;
    }

    public function removeSubscription(Subscription $subscription): self
    {
        if ($this->subscriptions->removeElement($subscription)) {
            // set the owning side to null (unless already changed)
            if ($subscription->getGym() === $this) {
                $subscription->setGym(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Course>
     */
    public function getCourses(): Collection
    {
        return $this->courses;
    }

    public function addCourse(Course $course): self
    {
        if (!$this->courses->contains($course)) {
            $this->courses[] = $course;
            $course->setGym($this);
        }

        return $this;
    }

    public function removeCourse(Course $course): self
    {
        if ($this->courses->removeElement($course)) {
            // set the owning side to null (unless already changed)
            if ($course->getGym() === $this) {
                $course->setGym(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews[] = $review;
            $review->setGym($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getGym() === $this) {
                $review->setGym(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setGym($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getGym() === $this) {
                $comment->setGym(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }


}
