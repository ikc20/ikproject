<?php

namespace App\Controller;

use App\Entity\Review;
use App\Entity\Product;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/reviews')]
class ReviewController extends AbstractController
{
    private ReviewRepository $reviewRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(ReviewRepository $reviewRepository, EntityManagerInterface $entityManager)
    {
        $this->reviewRepository = $reviewRepository;
        $this->entityManager = $entityManager;
    }

    // List all reviews for a specific product
    #[Route('/product/{productId}', name: 'review_list', methods: ['GET'])]
    public function list(int $productId): JsonResponse
    {
        $reviews = $this->reviewRepository->findBy(['product' => $productId]);
        $data = [];

        foreach ($reviews as $review) {
            $data[] = [
                'id' => $review->getId(),
                'rating' => $review->getRating(),
                'comment' => $review->getComment(),
                'createdAt' => $review->getCreatedAt(),
                'productId' => $review->getProduct()->getId(),
            ];
        }

        return $this->json($data);
    }

    // Show a single review
    #[Route('/{id}', name: 'review_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $review = $this->reviewRepository->find($id);

        if (!$review) {
            return $this->json(['message' => 'Review not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'id' => $review->getId(),
            'rating' => $review->getRating(),
            'comment' => $review->getComment(),
            'createdAt' => $review->getCreatedAt(),
            'productId' => $review->getProduct()->getId(),
        ]);
    }

    // Create a new review for a product
    #[Route('/product/{productId}/create', name: 'review_create', methods: ['POST'])]
    public function create(int $productId, Request $request): JsonResponse
    {
        $product = $this->entityManager->getRepository(Product::class)->find($productId);

        if (!$product) {
            return $this->json(['message' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        $review = new Review();
        $review->setRating($data['rating']);
        $review->setComment($data['comment']);
        $review->setCreatedAt(new \DateTimeImmutable());
        $review->setProduct($product);

        $this->entityManager->persist($review);
        $this->entityManager->flush();

        return $this->json(['message' => 'Review created successfully'], Response::HTTP_CREATED);
    }

    // Update an existing review
    #[Route('/{id}/update', name: 'review_update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $review = $this->reviewRepository->find($id);

        if (!$review) {
            return $this->json(['message' => 'Review not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        $review->setRating($data['rating'] ?? $review->getRating());
        $review->setComment($data['comment'] ?? $review->getComment());

        $this->entityManager->flush();

        return $this->json(['message' => 'Review updated successfully']);
    }

    // Delete a review
    #[Route('/{id}/delete', name: 'review_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $review = $this->reviewRepository->find($id);

        if (!$review) {
            return $this->json(['message' => 'Review not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($review);
        $this->entityManager->flush();

        return $this->json(['message' => 'Review deleted successfully']);
    }
}
