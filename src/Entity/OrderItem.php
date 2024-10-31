<?php

namespace App\Entity;

use App\Repository\OrderItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column]
    private ?float $unitPrice = null;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'orderItems')]
    #[ORM\JoinColumn(nullable: false)]
    private Order $order;

    #[ORM\ManyToOne(inversedBy: 'product')]
    private ?product $product = null;

    #[ORM\ManyToOne(inversedBy: 'orderitems')]
    private ?Order $orderitems = null;
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?order
    {
        return $this->product;
    }

    public function setProduct(?order $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getUnitPrice(): ?float
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(float $unitPrice): static
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    public function getOrderitems(): ?Order
    {
        return $this->orderitems;
    }

    public function setOrderitems(?Order $orderitems): static
    {
        $this->orderitems = $orderitems;

        return $this;
    }
}
