<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\InheritanceType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="role", type="string")
 * @DiscriminatorMap({"user" = "User", "ADHERENT" = "Member" , "ADMIN" = "Admin", "COACH" = "Coach", "GYMMANGER" = "GymManger"})
 * @UniqueEntity(
 *     fields={"email"},
 *     message="Your mail is already used"
 * )
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("user:read")
     * @Groups({"blog"})
     * @Groups({"gymread","subread","courseread"})
     * @Groups({"reservation"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Groups("user:read")
     * @Groups({"rec"})
     * @Groups({"blog"})
     * @Groups({"gymread","subread","courseread"})
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Groups("user:read")
     * @Groups({"rec"})
     * @Groups({"gymread","subread","courseread"})
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=100,unique=true)
     * @Assert\Email()
     * @Groups("user:read")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length (min="8", minMessage="Password should be at least 8 car")
     */
    private $password;

    /**
     * @Assert\EqualTo(propertyPath="password",message="Not the same password")
     */
    private $confirm_password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $securityQuestion;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $securityAnswer;

    /**
     * @ORM\OneToMany(targetEntity=PrivateMessage::class, mappedBy="idFirstUser",orphanRemoval="true")
     */
    private $privateMessagesFirst;

    /**
     * @ORM\OneToMany(targetEntity=PrivateMessage::class, mappedBy="idSecondUser",orphanRemoval="true")
     */
    private $privateMessagesSecond;

    /**
     * @ORM\OneToMany(targetEntity=Reclamation::class, mappedBy="user")
     */
    private $reclamations;

    /**
     * @ORM\OneToMany(targetEntity=Response::class, mappedBy="user")
     */
    private $responses;

    /**
     * @ORM\OneToOne(targetEntity=Gym::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $gym;

    /**
     * @ORM\OneToMany(targetEntity=Subscription::class, mappedBy="member")
     */
    private $subscriptions;


    /**
     * @ORM\OneToMany(targetEntity=Reservation::class, mappedBy="user")
     */
    private $reservations;

    /**
     * @ORM\OneToMany(targetEntity=Certification::class, mappedBy="user")
     */
    private $certifications;

    /**
     * @ORM\OneToOne(targetEntity=Review::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $review;

    /**
     * @ORM\OneToMany(targetEntity=Review::class, mappedBy="coach")
     */
    private $reviews;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="user")
     */
    private $comments;


    /**
     * @ORM\OneToMany(targetEntity=Command::class, mappedBy="user")
     */
    private $commands;

    /**
     * @ORM\OneToMany(targetEntity=ProductReview::class, mappedBy="user")
     */
    private $productReviews;

    /**
     * @ORM\OneToMany(targetEntity=Planning::class, mappedBy="user")
     */
    private $plannings;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("user:read")
     */
    private $imgUrl;

    /**
     * @ORM\OneToMany(targetEntity=Cart::class, mappedBy="user")
     */
    private $carts;

    /**
     * @ORM\OneToMany(targetEntity=Blog::class, mappedBy="user")
     */
    private $blog;

    /**
     * @ORM\OneToMany(targetEntity=BlogReview::class, mappedBy="user")
     */
    private $blogReviews;

    /**
     * @ORM\OneToMany(targetEntity=BlogLike::class, mappedBy="user")
     */
    private $likes;


    public function __construct()
    {
        $this->privateMessagesFirst = new ArrayCollection();
        $this->privateMessagesSecond = new ArrayCollection();
        $this->reclamations = new ArrayCollection();
        $this->responses = new ArrayCollection();
        $this->subscriptions = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->certifications = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->commands = new ArrayCollection();
        $this->productReviews = new ArrayCollection();
        $this->plannings = new ArrayCollection();
        $this->carts = new ArrayCollection();
        $this->blog = new ArrayCollection();
        $this->blogReviews = new ArrayCollection();
        $this->likes = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getConfirmPassword()
    {
        return $this->confirm_password;
    }

    /**
     * @param mixed $confirm_password
     */
    public function setConfirmPassword(string $confirm_password): self
    {
        $this->confirm_password = $confirm_password;

        return $this;
    }


    public function getSecurityQuestion(): ?string
    {
        return $this->securityQuestion;
    }

    public function setSecurityQuestion(?string $securityQuestion): self
    {
        $this->securityQuestion = $securityQuestion;

        return $this;
    }

    public function getSecurityAnswer(): ?string
    {
        return $this->securityAnswer;
    }

    public function setSecurityAnswer(?string $securityAnswer): self
    {
        $this->securityAnswer = $securityAnswer;

        return $this;
    }

    /**
     * @return Collection<int, PrivateMessage>
     */
    public function getPrivateMessagesFirst(): Collection
    {
        return $this->privateMessagesFirst;
    }

    public function addPrivateMessagesFirst(PrivateMessage $privateMessagesFirst): self
    {
        if (!$this->privateMessagesFirst->contains($privateMessagesFirst)) {
            $this->privateMessagesFirst[] = $privateMessagesFirst;
            $privateMessagesFirst->setIdFirstUser($this);
        }

        return $this;
    }

    public function removePrivateMessagesFirst(PrivateMessage $privateMessagesFirst): self
    {
        if ($this->privateMessagesFirst->removeElement($privateMessagesFirst)) {
            // set the owning side to null (unless already changed)
            if ($privateMessagesFirst->getIdFirstUser() === $this) {
                $privateMessagesFirst->setIdFirstUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PrivateMessage>
     */
    public function getPrivateMessagesSecond(): Collection
    {
        return $this->privateMessagesSecond;
    }

    public function addPrivateMessagesSecond(PrivateMessage $privateMessagesSecond): self
    {
        if (!$this->privateMessagesSecond->contains($privateMessagesSecond)) {
            $this->privateMessagesSecond[] = $privateMessagesSecond;
            $privateMessagesSecond->setIdSecondUser($this);
        }

        return $this;
    }

    public function removePrivateMessagesSecond(PrivateMessage $privateMessagesSecond): self
    {
        if ($this->privateMessagesSecond->removeElement($privateMessagesSecond)) {
            // set the owning side to null (unless already changed)
            if ($privateMessagesSecond->getIdSecondUser() === $this) {
                $privateMessagesSecond->setIdSecondUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Reclamation>
     */
    public function getReclamations(): Collection
    {
        return $this->reclamations;
    }

    public function addReclamation(Reclamation $reclamation): self
    {
        if (!$this->reclamations->contains($reclamation)) {
            $this->reclamations[] = $reclamation;
            $reclamation->setUser($this);
        }

        return $this;
    }

    public function removeReclamation(Reclamation $reclamation): self
    {
        if ($this->reclamations->removeElement($reclamation)) {
            // set the owning side to null (unless already changed)
            if ($reclamation->getUser() === $this) {
                $reclamation->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Response>
     */
    public function getResponses(): Collection
    {
        return $this->responses;
    }

    public function addResponse(Response $response): self
    {
        if (!$this->responses->contains($response)) {
            $this->responses[] = $response;
            $response->setUser($this);
        }

        return $this;
    }

    public function removeResponse(Response $response): self
    {
        if ($this->responses->removeElement($response)) {
            // set the owning side to null (unless already changed)
            if ($response->getUser() === $this) {
                $response->setUser(null);
            }
        }

        return $this;
    }

    public function getGym(): ?Gym
    {
        return $this->gym;
    }

    public function setGym(?Gym $gym): self
    {
        // unset the owning side of the relation if necessary
        if ($gym === null && $this->gym !== null) {
            $this->gym->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($gym !== null && $gym->getUser() !== $this) {
            $gym->setUser($this);
        }

        $this->gym = $gym;

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
            $subscription->setMember($this);
        }

        return $this;
    }

    public function removeSubscription(Subscription $subscription): self
    {
        if ($this->subscriptions->removeElement($subscription)) {
            // set the owning side to null (unless already changed)
            if ($subscription->getMember() === $this) {
                $subscription->setMember(null);
            }
        }

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
            $reservation->setUser($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getUser() === $this) {
                $reservation->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Certification>
     */
    public function getCertifications(): Collection
    {
        return $this->certifications;
    }

    public function addCertification(Certification $certification): self
    {
        if (!$this->certifications->contains($certification)) {
            $this->certifications[] = $certification;
            $certification->setUser($this);
        }

        return $this;
    }

    public function removeCertification(Certification $certification): self
    {
        if ($this->certifications->removeElement($certification)) {
            // set the owning side to null (unless already changed)
            if ($certification->getUser() === $this) {
                $certification->setUser(null);
            }
        }

        return $this;
    }

    public function getReview(): ?Review
    {
        return $this->review;
    }

    public function setReview(?Review $review): self
    {
        // unset the owning side of the relation if necessary
        if ($review === null && $this->review !== null) {
            $this->review->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($review !== null && $review->getUser() !== $this) {
            $review->setUser($this);
        }

        $this->review = $review;

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
            $review->setCoach($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getCoach() === $this) {
                $review->setCoach(null);
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
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection<int, Command>
     */
    public function getCommands(): Collection
    {
        return $this->commands;
    }

    public function addCommand(Command $command): self
    {
        if (!$this->commands->contains($command)) {
            $this->commands[] = $command;
            $command->setUser($this);
        }

        return $this;
    }

    public function removeCommand(Command $command): self
    {
        if ($this->commands->removeElement($command)) {
            // set the owning side to null (unless already changed)
            if ($command->getUser() === $this) {
                $command->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ProductReview>
     */
    public function getProductReviews(): Collection
    {
        return $this->productReviews;
    }

    public function addProductReview(ProductReview $productReview): self
    {
        if (!$this->productReviews->contains($productReview)) {
            $this->productReviews[] = $productReview;
            $productReview->setUser($this);
        }

        return $this;
    }

    public function removeProductReview(ProductReview $productReview): self
    {
        if ($this->productReviews->removeElement($productReview)) {
            // set the owning side to null (unless already changed)
            if ($productReview->getUser() === $this) {
                $productReview->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Planning>
     */
    public function getPlannings(): Collection
    {
        return $this->plannings;
    }

    public function addPlanning(Planning $planning): self
    {
        if (!$this->plannings->contains($planning)) {
            $this->plannings[] = $planning;
            $planning->setUser($this);
        }

        return $this;
    }

    public function removePlanning(Planning $planning): self
    {
        if ($this->plannings->removeElement($planning)) {
            // set the owning side to null (unless already changed)
            if ($planning->getUser() === $this) {
                $planning->setUser(null);
            }
        }

        return $this;
    }

    public function getImgUrl(): ?string
    {
        return $this->imgUrl;
    }

    public function setImgUrl(?string $imgUrl): self
    {
        $this->imgUrl = $imgUrl;

        return $this;
    }

    /**
     * @return Collection<int, Cart>
     */
    public function getCarts(): Collection
    {
        return $this->carts;
    }

    public function addCart(Cart $cart): self
    {
        if (!$this->carts->contains($cart)) {
            $this->carts[] = $cart;
            $cart->setUser($this);
        }

        return $this;
    }

    public function removeCart(Cart $cart): self
    {
        if ($this->carts->removeElement($cart)) {
            // set the owning side to null (unless already changed)
            if ($cart->getUser() === $this) {
                $cart->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Blog>
     */
    public function getBlog(): Collection
    {
        return $this->blog;
    }

    public function addBlog(Blog $blog): self
    {
        if (!$this->blog->contains($blog)) {
            $this->blog[] = $blog;
            $blog->setUser($this);
        }

        return $this;
    }

    public function removeBlog(Blog $blog): self
    {
        if ($this->blog->removeElement($blog)) {
            // set the owning side to null (unless already changed)
            if ($blog->getUser() === $this) {
                $blog->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BlogReview>
     */
    public function getBlogReviews(): Collection
    {
        return $this->blogReviews;
    }

    public function addBlogReview(BlogReview $blogReview): self
    {
        if (!$this->blogReviews->contains($blogReview)) {
            $this->blogReviews[] = $blogReview;
            $blogReview->setUser($this);
        }

        return $this;
    }

    public function removeBlogReview(BlogReview $blogReview): self
    {
        if ($this->blogReviews->removeElement($blogReview)) {
            // set the owning side to null (unless already changed)
            if ($blogReview->getUser() === $this) {
                $blogReview->setUser(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        //return $this->getEmail()+$this->getId();
        return "";
    }


    public function getRoles()
    {
        return array('ADHERENT');
        // TODO: Implement getRoles() method.
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername()
    {
        return $this->firstName;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->firstName,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->firstName,
            $this->password,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized, array('allowed_classes' => false));
    }

    /**
     * @return Collection<int, BlogLike>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(BlogLike $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->setUser($this);
        }

        return $this;
    }

    public function removeLike(BlogLike $like): self
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getUser() === $this) {
                $like->setUser(null);
            }
        }

        return $this;
    }
}
