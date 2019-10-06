<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 */
class Product
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProductDeclension", mappedBy="product", orphanRemoval=true)
     */
    private $declensions;

    /**
     * @ORM\Column(type="boolean")
     */
    private $bio;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    public function __construct()
    {
        $this->declensions = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getName();
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

    /**
     * @return Collection|ProductDeclension[]
     */
    public function getDeclensions(): Collection
    {
        return $this->declensions;
    }

    public function addDeclension(ProductDeclension $declension): self
    {
        if (!$this->declensions->contains($declension)) {
            $this->declensions[] = $declension;
            $declension->setProduct($this, 'test');
        }

        return $this;
    }

    public function removeDeclension(ProductDeclension $declension): self
    {
        if ($this->declensions->contains($declension)) {
            $this->declensions->removeElement($declension);
            // set the owning side to null (unless already changed)
            if ($declension->getProduct() === $this) {
                $declension->setProduct(null);
            }
        }

        return $this;
    }

    public function getBio(): ?bool
    {
        return $this->bio;
    }

    public function setBio(bool $bio): self
    {
        $this->bio = $bio;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
