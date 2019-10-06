<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductDeclensionRepository")
 */
class ProductDeclension
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $reference;

    /**
     * @ORM\Column(type="float")
     */
    private $priceTe;

    /**
     * @ORM\Column(type="float")
     */
    private $priceTi;

    /**
     * @ORM\Column(type="float")
     */
    private $tax;

    /**
     * @ORM\Column(type="float")
     */
    private $weightQuantity;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $unity;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="declension")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RecipeProduct", mappedBy="product")
     */
    private $recipeProducts;

    public function __construct()
    {
        $this->recipeProducts = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getProduct()->__toString();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getPriceTe(): ?float
    {
        return $this->priceTe;
    }

    public function setPriceTe(float $priceTe): self
    {
        $this->priceTe = $priceTe;

        return $this;
    }

    public function getPriceTi(): ?float
    {
        return $this->priceTi;
    }

    public function setPriceTi(float $priceTi): self
    {
        $this->priceTi = $priceTi;

        return $this;
    }

    public function getTax(): ?float
    {
        return $this->tax;
    }

    public function setTax(float $tax): self
    {
        $this->tax = $tax;

        return $this;
    }

    public function getWeightQuantity(): ?float
    {
        return $this->weightQuantity;
    }

    public function setWeightQuantity(float $weightQuantity): self
    {
        $this->weightQuantity = $weightQuantity;

        return $this;
    }

    public function getUnity(): ?string
    {
        return $this->unity;
    }

    public function setUnity(string $unity): self
    {
        $this->unity = $unity;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return Collection|RecipeProduct[]
     */
    public function getRecipeProducts(): Collection
    {
        return $this->recipeProducts;
    }

    public function addRecipeProduct(RecipeProduct $recipeProduct): self
    {
        if (!$this->recipeProducts->contains($recipeProduct)) {
            $this->recipeProducts[] = $recipeProduct;
            $recipeProduct->setProduct($this);
        }

        return $this;
    }

    public function removeRecipeProduct(RecipeProduct $recipeProduct): self
    {
        if ($this->recipeProducts->contains($recipeProduct)) {
            $this->recipeProducts->removeElement($recipeProduct);
            // set the owning side to null (unless already changed)
            if ($recipeProduct->getProduct() === $this) {
                $recipeProduct->setProduct(null);
            }
        }

        return $this;
    }
}
