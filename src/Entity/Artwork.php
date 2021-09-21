<?php

namespace App\Entity;

use App\Repository\ArtworkRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
/**
 * @ORM\Entity(repositoryClass=ArtworkRepository::class)
 */
class Artwork
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=3000)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $picture;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $artist;

    /**
     * @ORM\Column(type="datetime")
     */
    private $publicationDate;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Gedmo\Slug(fields={"title"})
     */
    private $slug;

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="artworks")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity=ArtworkComment::class, mappedBy="artwork", orphanRemoval=true)
     */
    private $artworkComments;

    /**
     * @ORM\Column(type="integer", length=4, nullable=true)
     */
    private $yearOfCreation;

    public function __construct()
    {
        $this->artworkComments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

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

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getArtist(): ?string
    {
        return $this->artist;
    }

    public function setArtist(?string $artist): self
    {
        $this->artist = $artist;

        return $this;
    }

    public function getPublicationDate(): ?\DateTimeInterface
    {
        return $this->publicationDate;
    }

    public function setPublicationDate(\DateTimeInterface $publicationDate): self
    {
        $this->publicationDate = $publicationDate;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection|ArtworkComment[]
     */
    public function getArtworkComments(): Collection
    {
        return $this->artworkComments;
    }

    public function addArtworkComment(ArtworkComment $artworkComment): self
    {
        if (!$this->artworkComments->contains($artworkComment)) {
            $this->artworkComments[] = $artworkComment;
            $artworkComment->setArtwork($this);
        }

        return $this;
    }

    public function removeArtworkComment(ArtworkComment $artworkComment): self
    {
        if ($this->artworkComments->removeElement($artworkComment)) {
            // set the owning side to null (unless already changed)
            if ($artworkComment->getArtwork() === $this) {
                $artworkComment->setArtwork(null);
            }
        }

        return $this;
    }

    public function getYearOfCreation(): ?string
    {
        return $this->yearOfCreation;
    }

    public function setYearOfCreation(?string $yearOfCreation): self
    {
        $this->yearOfCreation = $yearOfCreation;

        return $this;
    }
}
