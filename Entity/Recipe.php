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

    /**
     * @ORM\Column(type="float")
     */
    private $price20;

    /**
     * @ORM\Column(type="float")
     */
    private $price150;

    /**
     * @ORM\Column(type="integer")
     */
    private $stock20;

    /**
     * @ORM\Column(type="integer")
     */
    private $stock45;

    /**
     * @ORM\Column(type="integer")
     */
    private $stock150;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

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

    public function getPrice($weight = null): ?float
    {
		if($weight == 20)
		{
			$price = $this->price20;
		}
		elseif($weight == 45)
		{
			$price = $this->price;
		}
		elseif($weight == 150)
		{
			$price = $this->price150;
		}
		else
			$price = $this->price;
		
        return $price;
    }

    public function getPrice20(): ?float
    {		
        return $this->price20;
    }

    public function getPrice150(): ?float
    {		
        return $this->price150;
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

    public function getStock($weight = null): ?int
    {
		if($weight == 20)
		{
			$stock = $this->stock20;
		}
		elseif($weight == 45)
		{
			$stock = $this->stock45;
		}
		elseif($weight == 150)
		{
			$stock = $this->stock150;
		}
		else
			$stock = $this->stock;
		
        return $stock;
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

    public function getPriceOrder($weight = null): ?float
    {
        $priceOrder = 0;
        
        foreach ($this->getProducts() as $product) {
            $priceOrder += $product->getPriceByRecipe($weight);
        }
		
		$priceOrder = $priceOrder + self::getPackagingPrice($weight);

        return $priceOrder;
    }

    public function getPriceProposed($weight = null): ?float
    {
        $priceProposed = $this->getPriceOrder($weight) * 3;

        return $priceProposed;
    }

    public function getMarginCoefficient($weight = null): ?float
    {
        if(!$this->getPriceOrder($weight))
            return 0;
        
        $marginCoefficient = $this->getPrice($weight) / $this->getPriceOrder($weight);

        return $marginCoefficient;
    }

    public function getMargin($weight = null): ?float
    {
        $margin = $this->getPrice($weight) - $this->getPriceOrder($weight);

        return $margin;
    }

    public function getValorization($weight = null): ?float
    {
        return $this->getStock($weight) * $this->getPrice($weight);
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

    public function getPriceByWeight($weight = null): ?float
    {
		if(!$weight)
			$realWeight = $this->getWeight();
		else
			$realWeight = $weight;
		
		if(!$realWeight)
			return 0;
		
		$priceByWeight = ($this->getPrice($weight) / $realWeight) * 1000;

        return $priceByWeight;
    }

    public function getPriceByWeightInit(): ?float
    {
		if($this->getWeight())
			return $this->getPriceByWeight($this->getWeight());

        return 0;
    }

    public function setPrice20(float $price20): self
    {
        $this->price20 = $price20;

        return $this;
    }

    public function setPrice150(float $price150): self
    {
        $this->price150 = $price150;

        return $this;
    }
	
	public static function getPackagingPrice($weight): ?float
         	{
         		$packagingPrice = 0;
         		
         		if($weight == 20)
         		{
         			$boite = 13.99 / 24;
         			$etiquettes = 0.2;
         			$packagingPrice = $boite + $etiquettes;
         		}
         		elseif($weight == 45)
         		{
         			$boite = 22.56 / 24;
         			$etiquettes = 0.25;
         			$packagingPrice = $boite + $etiquettes;
         		}
         		elseif($weight == 70)
         		{
         			$sachet = 95.08 / 1000;
         			$tirette = 10.59 / 1000;
         			$etiquettes = 0.25;
         			$packagingPrice = $sachet + $tirette + $etiquettes;
         		}
         		elseif($weight == 150)
         		{
         			$boite = 21.41 / 12;
         			$etiquettes = 0.35;
         			$packagingPrice = $boite + $etiquettes;
         		}
         		
         		return $packagingPrice;
         	}

    public function getStock20(): ?int
    {
        return $this->stock20;
    }

    public function setStock20(int $stock20): self
    {
        $this->stock20 = $stock20;

        return $this;
    }

    public function getStock45(): ?int
    {
        return $this->stock45;
    }

    public function setStock45(int $stock45): self
    {
        $this->stock45 = $stock45;

        return $this;
    }

    public function getStock150(): ?int
    {
        return $this->stock150;
    }

    public function setStock150(int $stock150): self
    {
        $this->stock150 = $stock150;

        return $this;
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
}
