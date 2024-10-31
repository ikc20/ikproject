<?php

namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartRepository::class)]
class Cart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, cartitem>
     */
    #[ORM\OneToMany(targetEntity: cartitem::class, mappedBy: 'cartitems')]
    private Collection $cartitems;

    #[ORM\ManyToOne]
    private ?User $users = null;

    public function __construct()
    {
        $this->cartitems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, cartitem>
     */
    public function getCartitems(): Collection
    {
        return $this->cartitems;
    }

    public function addCartitem(cartitem $cartitem): static
    {
        if (!$this->cartitems->contains($cartitem)) {
            $this->cartitems->add($cartitem);
            $cartitem->setCartitems($this);
        }

        return $this;
    }

    public function removeCartitem(cartitem $cartitem): static
    {
        if ($this->cartitems->removeElement($cartitem)) {
            // set the owning side to null (unless already changed)
            if ($cartitem->getCartitems() === $this) {
                $cartitem->setCartitems(null);
            }
        }

        return $this;
    }

    public function getUsers(): ?User
    {
        return $this->users;
    }

    public function setUsers(?User $users): static
    {
        $this->users = $users;

        return $this;
    }
}
