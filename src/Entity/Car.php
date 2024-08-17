<?php

namespace App\Entity;

use App\Repository\CarRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity(repositoryClass: CarRepository::class)]
class Car
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\Column(type: 'string', length: 10)]
    private ?string $registration;

    #[ORM\Column(type: 'string', length: 25)]
    private ?string $make;

    #[ORM\Column(type: 'string', length: 45)]
    private ?string $model;

    #[ORM\Column(type: 'string', length: 15)]
    private ?string $colour;

    #[ORM\Column(type: 'integer')]
    private ?int $year;

    #[ORM\Column(type: 'integer')]
    private ?int $mileage;

    #[ORM\Column(type: 'string', length: 30)]
    private ?string $transmission;

    #[ORM\Column(type: 'string', length: 40)]
    private ?string $fuel;

    #[ORM\Column(type: 'integer')]
    private ?int $engine_size;

    #[ORM\Column(type: 'string', length: 20)]
    private ?string $emission_class;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isSold = false;

    #[ORM\Column(type: 'integer')]
    private ?int $doors;

    #[ORM\Column(type: 'integer')]
    private ?int $price;

    #[ORM\Column(type: 'integer')]
    private ?int $horsepower;

    #[ORM\OneToMany(targetEntity: CarImage::class, mappedBy: 'car', cascade: ['persist', 'remove'])]
    private Collection $carImage;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $DateAdded;

    #[ORM\Column(length: 20)]
    private ?string $hpi;

    #[ORM\Column(nullable: true)]
    private ?array $features = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateSold = null;

    #[Pure] public function __construct()
    {
        $this->carImage = new ArrayCollection();
        $this->features = [];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRegistration(): ?string
    {
        return $this->registration;
    }

    public function setRegistration(string $registration): self
    {
        $this->registration = $registration;
        return $this;
    }

    public function getMake(): ?string
    {
        return $this->make;
    }

    public function setMake(string $make): self
    {
        $this->make = $make;
        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;
        return $this;
    }

    public function getColour(): ?string
    {
        return $this->colour;
    }

    public function setColour(string $colour): self
    {
        $this->colour = $colour;
        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): self
    {
        $this->year = $year;
        return $this;
    }

    public function getMileage(): ?int
    {
        return $this->mileage;
    }

    public function setMileage(int $mileage): self
    {
        $this->mileage = $mileage;
        return $this;
    }

    public function getTransmission(): ?string
    {
        return $this->transmission;
    }

    public function setTransmission(string $transmission): self
    {
        $this->transmission = $transmission;
        return $this;
    }

    public function getFuel(): ?string
    {
        return $this->fuel;
    }

    public function setFuel(string $fuel): self
    {
        $this->fuel = $fuel;
        return $this;
    }

    public function getEngineSize(): ?int
    {
        return $this->engine_size;
    }

    public function setEngineSize(int $engineSize): self
    {
        $this->engine_size = $engineSize;
        return $this;
    }

    public function getEmissionClass(): ?string
    {
        return $this->emission_class;
    }

    public function setEmissionClass(string $emission_class): self
    {
        $this->emission_class = $emission_class;
        return $this;
    }

    public function isSold(): bool
    {
        return $this->isSold;
    }

    public function setIsSold(bool $isSold): self
    {
        $this->isSold = $isSold;
        return $this;
    }

    public function getDoors(): ?int
    {
        return $this->doors;
    }

    public function setDoors(int $doors): self
    {
        $this->doors = $doors;
        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getHorsepower(): ?int
    {
        return $this->horsepower;
    }

    public function setHorsepower(int $horsepower): self
    {
        $this->horsepower = $horsepower;
        return $this;
    }

    /**
     * @return Collection<int, CarImage>
     */
    public function getCarImage(): Collection
    {
        return $this->carImage;
    }

    public function addCarImage(CarImage $carImage): self
    {
        if (!$this->carImage->contains($carImage)) {
            $this->carImage->add($carImage);
            $carImage->setCar($this);
        }

        return $this;
    }

    public function removeCarImage(CarImage $carImage): self
    {
        if ($this->carImage->removeElement($carImage)) {
            // set the owning side to null (unless already changed)
            if ($carImage->getCar() === $this) {
                $carImage->setCar(null);
            }
        }

        return $this;
    }

    public function getPrimaryImage(): ?CarImage
    {
        return $this->carImage->isEmpty() ? null : $this->carImage->first();
    }

    public function getDateAdded(): ?\DateTimeInterface
    {
        return $this->DateAdded;
    }

    public function setDateAdded(\DateTimeInterface $DateAdded): static
    {
        $this->DateAdded = $DateAdded;

        return $this;
    }

    public function getHpi(): ?string
    {
        return $this->hpi;
    }

    public function setHpi(string $hpi): static
    {
        $this->hpi = $hpi;

        return $this;
    }

    public function getFeatures(): ?array
    {
        return $this->features;
    }

    public function setFeatures(?array $features): static
    {
        $this->features = $features;

        return $this;
    }

    public function addFeature(string $feature): self
    {
        if ($this->features === null) {
            $this->features = [];
        }

        if (!in_array($feature, $this->features, true)) {
            $this->features[] = $feature;
        }

        return $this;
    }

    public function removeFeature(string $feature): self
    {
        if ($this->features === null) {
            return $this;
        }

        $this->features = array_filter($this->features, fn($f) => $f !== $feature);
        return $this;
    }

    public function getDateSold(): ?\DateTimeInterface
    {
        return $this->dateSold;
    }

    public function setDateSold(?\DateTimeInterface $dateSold): static
    {
        $this->dateSold = $dateSold;

        return $this;
    }
}
