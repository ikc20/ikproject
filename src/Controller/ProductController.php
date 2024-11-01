<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

#[Route('/api/products')]
class ProductController extends AbstractController
{
    private ProductRepository $productRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(ProductRepository $productRepository, EntityManagerInterface $entityManager)
    {
        $this->productRepository = $productRepository;
        $this->entityManager = $entityManager;
    }

    // List all products
    #[Route('/', name: 'product_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $products = $this->productRepository->findAll();
        $data = [];

        foreach ($products as $product) {
            $data[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'price' => $product->getPrice(),
                'rating' => $product->getRating(),
                'imageUrl' => $product->getImageUrl(),
                'gender' => $product->getGender(), // Add gender
                'category' => $product->getCategory(),
                'promoprice' => $product->getPromoprice(),
                'createdAt' => $product->getCreatedAt()->format('Y-m-d H:i:s'), // Format the date
            ];
        }

        return $this->json($data);
    }

    // Create a new product
    #[Route('/create', name: 'product_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validate required fields (add your own validation logic as needed)
        if (!isset($data['name'], $data['price'], $data['gender'], $data['description'])) {
            return $this->json(['message' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        $product = new Product();
        $product->setName($data['name']);
        $product->setDescription($data['description']);
        $product->setPrice($data['price']);
        $product->setRating($data['rating'] ?? 0); // Default rating if not provided
        $product->setImageUrl($data['imageUrl']); // Adjust based on your data source
        $product->setGender($data['gender']); // Set gender
        $product->setCategory($data['category']);
        $product->setPromoprice($data['promoprice'] ?? null);
        $product->setCreatedAt(new \DateTimeImmutable());
        $product->setUpdatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $this->json(['message' => 'Product created successfully', 'id' => $product->getId()], Response::HTTP_CREATED);
    }

    // Update a product
    #[Route('/{id}/update', name: 'product_update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            return $this->json(['message' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        // Update only fields that are provided in the request
        if (isset($data['name'])) {
            $product->setName($data['name']);
        }
        if (isset($data['description'])) {
            $product->setDescription($data['description']);
        }
        if (isset($data['price'])) {
            $product->setPrice($data['price']);
        }
        if (isset($data['rating'])) {
            $product->setRating($data['rating']);
        }
        if (isset($data['imageUrl'])) {
            $product->setImageUrl($data['imageUrl']);
        }
        if (isset($data['gender'])) {
            $product->setGender($data['gender']);
        }
        if (isset($data['category'])) {
            $product->setCategory($data['category']);
        }
        if (isset($data['promoprice'])) {
            $product->setPromoprice($data['promoprice']);
        }
        $product->setUpdatedAt(new \DateTimeImmutable());

        $this->entityManager->flush();

        return $this->json(['message' => 'Product updated successfully']);
    }

    // Delete a product
    #[Route('/{id}/delete', name: 'product_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            return $this->json(['message' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($product);
        $this->entityManager->flush();

        return $this->json(['message' => 'Product deleted successfully']);
    }
}
