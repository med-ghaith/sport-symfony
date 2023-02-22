<?php

namespace App\Entity;

use App\Repository\BlogRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use App\Entity\BlogCategory;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=BlogRepository::class)
 */
class Blog
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"blog"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="please enter a title")
     * @Assert\Length(min=5)
     * @Groups({"blog"})
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="please enter a description")
     * @Assert\Length(min=10)
     * @Groups({"blog"})
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"blog"})
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="blog")
      * @Groups({"blog"})
     */
    private $user;



    /**
     * @ORM\OneToMany(targetEntity=BlogReview::class, mappedBy="relation")
     */
    private $blog;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $verified;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"blog"})
     */
    private $createdAt;



    /**
     * @ORM\OneToMany(targetEntity=BlogLike::class, mappedBy="blog")
     */
    private $likes;

    /**
     * @ORM\OneToMany(targetEntity=BlogReview::class, mappedBy="idblog")
     */
    private $blogRe;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"blog"})
     */
    private $view;

    /**
     * @ORM\ManyToOne(targetEntity=BlogCategory::class, inversedBy="blogs")
     * @Groups({"blog"})
     */
    private $blogCat;



    public function __construct()
    {
        $this->blogCategories = new ArrayCollection();
        $this->blog = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->view = 0;

        $this->likes = new ArrayCollection();
        $this->blogRe = new ArrayCollection();

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }
    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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
     * @return Collection<int, BlogReview>
     */
    public function getBlog(): Collection
    {
        return $this->blog;
    }

    public function addBlog(BlogReview $blog): self
    {
        if (!$this->blog->contains($blog)) {
            $this->blog[] = $blog;
            $blog->setRelation($this);
        }

        return $this;
    }

    public function removeBlog(BlogReview $blog): self
    {
        if ($this->blog->removeElement($blog)) {
            // set the owning side to null (unless already changed)
            if ($blog->getRelation() === $this) {
                $blog->setRelation(null);
            }
        }

        return $this;
    }

    public function getVerified(): ?bool
    {
        return $this->verified;
    }

    public function setVerified(?bool $verified): self
    {
        $this->verified = $verified;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
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
            $like->setBlog($this);
        }

        return $this;
    }

    public function removeLike(BlogLike $like): self
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getBlog() === $this) {
                $like->setBlog(null);
            }
        }

        return $this;
    }

    public function isLikedByUser(User $user): bool
    {
        Foreach( $this->likes as $like)
        {
            if ($like->getUser() === $user) return true;

        }
        return false;


    }

    /**
     * @return Collection<int, BlogReview>
     */
    public function getBlogRe(): Collection
    {
        return $this->blogRe;
    }

    public function addBlogRe(BlogReview $blogRe): self
    {
        if (!$this->blogRe->contains($blogRe)) {
            $this->blogRe[] = $blogRe;
            $blogRe->setIdblog($this);
        }

        return $this;
    }

    public function removeBlogRe(BlogReview $blogRe): self
    {
        if ($this->blogRe->removeElement($blogRe)) {
            // set the owning side to null (unless already changed)
            if ($blogRe->getIdblog() === $this) {
                $blogRe->setIdblog(null);
            }
        }

        return $this;
    }

    public function getView(): ?string
    {
        return $this->view;
    }

    public function setView(string $view): self
    {
        $this->view = $view;

        return $this;
    }

    public function getBlogCat(): ?BlogCategory
    {
        return $this->blogCat;
    }

    public function setBlogCat(?BlogCategory $blogCat): self
    {
        $this->blogCat = $blogCat;

        return $this;
    }




}
