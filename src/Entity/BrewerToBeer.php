<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BrewerToBeerRepository")
 */
class BrewerToBeer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $beerId;

    /**
     * @ORM\Column(type="integer")
     */
    private $brewerId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBeerId(): ?int
    {
        return $this->beerId;
    }

    public function setBeerId(int $beerId): self
    {
        $this->beerId = $beerId;

        return $this;
    }

    public function getBrewerId(): ?int
    {
        return $this->brewerId;
    }

    public function setBrewerId(int $brewerId): self
    {
        $this->brewerId = $brewerId;

        return $this;
    }
}
