<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RecipeProductRepository")
 */
class RecipeProduct
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Recipe", inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $recipe;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ProductDeclension", inversedBy="recipeProducts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @ORM\Column(type="float")
     */
    private $quantity;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRecipe(): ?Recipe
    {
        return $this->recipe;
    }

    public function setRecipe(?Recipe $recipe): self
    {
        $this->recipe = $recipe;

        return $this;
    }

    public function getProduct(): ?ProductDeclension
    {
        return $this->product;
    }

    public function setProduct(?ProductDeclension $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function setQuantity(float $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getQuantityByRecipe(): ?float
    {
        if($this->getProduct()->getUnity() == 'quantity')
            $quantityProductByRecipe = $this->getQuantity();
        else
            $quantityProductByRecipe = ($this->getRecipe()->getWeight() * $this->getQuantity()) / $this->getRecipe()->getWeightRecipe();

        return $quantityProductByRecipe;
    }

    public function getPriceByRecipe(): ?float
    {
        $priceByRecipe = $this->getProduct()->getPriceTi() / $this->getProduct()->getWeightQuantity() * $this->getQuantityByRecipe();

        return $priceByRecipe;
    }
}
