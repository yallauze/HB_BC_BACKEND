<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PhotoRepository")
 */
class Photo
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"getPhotos"})
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Groups({"getPhotos"})
     * @Groups({"getAdvertByID"})
     */
    private $data_base64;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Advert", inversedBy="photos")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"getPhotos"})
     */
    private $advert;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDataBase64(): ?string
    {
        return $this->data_base64;
    }

    public function setDataBase64(string $data_base64): self
    {
        $this->data_base64 = $data_base64;

        return $this;
    }

    public function getAdvert(): ?Advert
    {
        return $this->advert;
    }

    public function setAdvert(?Advert $advert): self
    {
        $this->advert = $advert;

        return $this;
    }
}
