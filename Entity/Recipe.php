<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RecipeRepository")
 */
class Recipe
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
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="float")
     */
    private $weight;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RecipeProduct", mappedBy="recipe", orphanRemoval=true)
     * @ORM\OrderBy({"quantity" = "DESC"})
     */
    private $products;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sale", mappedBy="product")
     */
    private $sales;

    /**
     * @ORM\Column(type="integer")
     */
    private $stock;

    /**
     * @ORM\Column(type="integer")
     */
    private $prestashop;

    /**
     * @ORM\Column(type="integer")
     */
    private $brewTemp;

    /**
     * @ORM\Column(type="float")
     */
    private $brewTime;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->sales = new ArrayCollection();
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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(float $weight): self
    {
        $this->weight = $weight;

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

    /**
     * @return Collection|RecipeProduct[]
     */
    public function getProducts(): Collection
    {
        $products = new ArrayCollection();

        return $this->products;
    }

    /**
     * @return Collection|RecipeProduct[]
     */
    public function getProductsByUnity($type = 'weight'): Collection
    {
        $products = new ArrayCollection();

        foreach ($this->getProducts() as $key => $product) {
            if($product->getProduct()->getUnity() == $type)
                $products[] = $product;
        }

        return $products;
    }

    public function addProduct(RecipeProduct $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setRecipe($this);
        }

        return $this;
    }

    public function removeProduct(RecipeProduct $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            // set the owning side to null (unless already changed)
            if ($product->getRecipe() === $this) {
                $product->setRecipe(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Sale[]
     */
    public function getSales(): Collection
    {
        return $this->sales;
    }

    public function addSale(Sale $sale): self
    {
        if (!$this->sales->contains($sale)) {
            $this->sales[] = $sale;
            $sale->setProduct($this);
        }

        return $this;
    }

    public function removeSale(Sale $sale): self
    {
        if ($this->sales->contains($sale)) {
            $this->sales->removeElement($sale);
            // set the owning side to null (unless already changed)
            if ($sale->getProducts() === $this) {
                $sale->setProduct(null);
            }
        }

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getWeightRecipe(): ?float
    {
        $weightRecipe = 0;

        foreach ($this->getProducts() as $product) {
            if($product->getProduct()->getUnity() == 'weight')
                $weightRecipe += $product->getQuantity();
        }

        return $weightRecipe;
    }

    public function getPriceOrder(): ?float
    {
        $priceOrder = 0;
        
        foreach ($this->getProducts() as $product) {
            $priceOrder += $product->getPriceByRecipe();
        }

        return $priceOrder;
    }

    public function getPriceProposed(): ?float
    {
        $priceProposed = $this->getPriceOrder() * 3;

        return $priceProposed;
    }

    public function getMarginCoefficient(): ?float
    {
        if(!$this->getPriceOrder())
            return 0;
        
        $marginCoefficient = $this->getPrice() / $this->getPriceOrder();

        return $marginCoefficient;
    }

    public function getMargin(): ?float
    {
        $margin = $this->getPrice() - $this->getPriceOrder();

        return $margin;
    }

    public function getValorization(): ?float
    {
        return $this->stock * $this->getPrice();
    }

    public function getPrimary(): ?RecipeProduct
    {
        foreach ($this->getProducts() as $product) {
            if($product->getProduct()->getProduct()->getType() == 'primaire')
                return $product;
        }

        return null;
    }

    public function getPrestashop(): ?int
    {
        return $this->prestashop;
    }

    public function setPrestashop(int $prestashop): self
    {
        $this->prestashop = $prestashop;

        return $this;
    }

    public function getBrewTemp(): ?int
    {
        return $this->brewTemp;
    }

    public function setBrewTemp(int $brewTemp): self
    {
        $this->brewTemp = $brewTemp;

        return $this;
    }

    public function getBrewTime(): ?float
    {
        return $this->brewTime;
    }

    public function getBrewTimeString(): ?string
    {
        $minutes = intval(($this->getBrewTime() % 3600) / 60);
        $secondes = intval((($this->getBrewTime() % 3600) % 60));

        $string = $minutes.($secondes ? ','.$secondes : '').' minute'.($minutes > 1 ? 's' : '');

        return $string;
    }

    public function setBrewTime(float $brewTime): self
    {
        $this->brewTime = $brewTime;

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
}
