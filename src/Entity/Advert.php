<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AdvertRepository")
 */
class Advert
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"getAdverts"})
     * @Groups({"getPhotos"})
     * @Groups({"getAdvertByID"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"getAdverts"})
     * @Groups({"getAdvertByID"})
     */
    private $title;

    /**
     * @ORM\Column(type="text", length=255)
     * @Groups({"getAdvertByID"})
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"getAdvertByID"})
     */
    private $year_started_at;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"getAdvertByID"})
     */
    private $km;

    /**
     * @ORM\Column(type="float")
     * @Groups({"getAdverts"})
     * @Groups({"getAdvertByID"})
     */
    private $price;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"getAdverts"})
     * @Groups({"getAdvertByID"})
     */
    private $created_at;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Garage", inversedBy="adverts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $garage;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Fuel", inversedBy="adverts")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"getAdvertByID"})
     */
    private $fuel;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Photo", mappedBy="advert", orphanRemoval=true)
     * @Groups({"getAdvertByID"})
     */
    private $photos;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Model", inversedBy="adverts")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"getAdvertByID"})
     */
    private $model;

    public function __construct()
    {
        $this->photos = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getYearStartedAt(): ?int
    {
        return $this->year_started_at;
    }

    public function setYearStartedAt(int $year_started_at): self
    {
        $this->year_started_at = $year_started_at;

        return $this;
    }

    public function getKm(): ?int
    {
        return $this->km;
    }

    public function setKm(int $km): self
    {
        $this->km = $km;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getGarage(): ?Garage
    {
        return $this->garage;
    }

    public function setGarage(?Garage $garage): self
    {
        $this->garage = $garage;

        return $this;
    }

    public function getFuel(): ?Fuel
    {
        return $this->fuel;
    }

    public function setFuel(?Fuel $fuel): self
    {
        $this->fuel = $fuel;

        return $this;
    }

    /**
     * @return Collection|Photo[]
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(Photo $photo): self
    {
        if (!$this->photos->contains($photo)) {
            $this->photos[] = $photo;
            $photo->setAdvert($this);
        }
        return $this;
    }

    public function removePhoto(Photo $photo): self
    {
        if ($this->photos->contains($photo)) {
            $this->photos->removeElement($photo);
            // set the owning side to null (unless already changed)
            if ($photo->getAdvert() === $this) {
                $photo->setAdvert(null);
            }
        }

        return $this;
    }

    public function getModel(): ?Model
    {
        return $this->model;
    }

    public function setModel(?Model $model): self
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get the value of created_at
     */ 
    public function getCreatedAt(): ?DateTime
    {
        return $this->created_at;
    }

    /**
     * Set the value of created_at
     *
     * @return  self
     */ 
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;

        return $this;
    }
}
