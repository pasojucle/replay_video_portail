<?php

namespace App\Entity;

use App\Repository\ChannelRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ChannelRepository::class)
 */
class Channel
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $idRaspberry;

    public function __toString(): ?string
    {
        return $this->title;
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

    public function getIdRaspberry(): ?int
    {
        return $this->idRaspberry;
    }

    public function setIdRaspberry(?int $idRaspberry): self
    {
        $this->idRaspberry = $idRaspberry;

        return $this;
    }
}
