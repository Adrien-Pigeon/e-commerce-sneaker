<?php

namespace App\Entity;

use App\Repository\CouponsTypesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CouponsTypesRepository::class)]
class CouponsTypes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'coupons_types', targetEntity: CouponsPromo::class, orphanRemoval: true)]
    private Collection $couponsPromos;

    public function __construct()
    {
        $this->couponsPromos = new ArrayCollection();
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
     * @return Collection<int, CouponsPromo>
     */
    public function getCouponsPromos(): Collection
    {
        return $this->couponsPromos;
    }

    public function addCouponsPromo(CouponsPromo $couponsPromo): self
    {
        if (!$this->couponsPromos->contains($couponsPromo)) {
            $this->couponsPromos->add($couponsPromo);
            $couponsPromo->setCouponsTypes($this);
        }

        return $this;
    }

    public function removeCouponsPromo(CouponsPromo $couponsPromo): self
    {
        if ($this->couponsPromos->removeElement($couponsPromo)) {
            // set the owning side to null (unless already changed)
            if ($couponsPromo->getCouponsTypes() === $this) {
                $couponsPromo->setCouponsTypes(null);
            }
        }

        return $this;
    }
}
