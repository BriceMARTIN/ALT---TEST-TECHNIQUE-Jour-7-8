<?php

namespace App\Service;

use App\Entity\Tool;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ToolService
{
  public function __construct(
    private EntityManagerInterface $entityManager,
    private CategoryRepository $categoryRepository,
    private ValidatorInterface $validator,
    private SerializerInterface $serializer,
    private LoggerInterface $logger,
  ) {
  }

  /**
   * Create a new Tool instance from request data
   */
  public function createTool(array $data): Tool
  {
    $tool = new Tool();

    $tool->setName($data['name'] ?? '');
    $tool->setDescription($data['description'] ?? '');
    $tool->setVendor($data['vendor'] ?? '');
    $tool->setWebsiteUrl($data['websiteUrl'] ?? '');
    $tool->setMonthlyCost($data['monthlyCost'] ?? '0');
    $tool->setOwnerDepartment($data['ownerDepartment'] ?? '');
    $tool->setStatus('active');
    $tool->setActiveUsersCount(0);
    $tool->setCreatedAt(new DateTime());
    $tool->setUpdatedAt(new DateTime());

    $category = $this->validateCategory($data['category'] ?? null);
    $tool->setCategory($category);

    return $tool;
  }

  /**
   * Update an existing Tool from request data
   */
  public function updateTool(Tool $tool, array $data): Tool
  {
    $tool->setMonthlyCost($data['monthlyCost'] ?? $tool->getMonthlyCost());
    $tool->setStatus($data['status'] ?? $tool->getStatus());
    $tool->setDescription($data['description'] ?? $tool->getDescription());
    $tool->setUpdatedAt(new DateTime());

    if (isset($data['category'])) {
      $category = $this->validateCategory($data['category']);
      $tool->setCategory($category);
    }

    return $tool;
  }

  /**
   * Validate a Tool entity
   * Returns an array of error objects if invalid, empty array if valid
   */
  public function validateTool(Tool $tool): array
  {
    $violations = $this->validator->validate($tool);

    if (count($violations) > 0) {
      $errors = [];
      foreach ($violations as $violation) {
        $errors[] = [
          'property' => $violation->getPropertyPath(),
          'message' => $violation->getMessage(),
        ];
      }
      return $errors;
    }

    return [];
  }

  /**
   * Persist a Tool to the database
   */
  public function persistTool(Tool $tool): void
  {
    $this->entityManager->persist($tool);
    $this->entityManager->flush();
  }

  /**
   * Serialize a Tool entity to array for JSON response
   */
  public function serializeToolResponse(Tool $tool): array
  {
    $returnValue = json_decode($this->serializer->serialize($tool, 'json'), true);

    // Replace category object with just its name
    if ($tool->getCategory()) {
      $returnValue['category'] = $tool->getCategory()->getName();
    }

    return $returnValue;
  }

  /**
   * Validate and retrieve a Category by ID
   */
  public function validateCategory(?int $categoryId): Category
  {
    $category = $this->categoryRepository->find($categoryId);
    if (!$category) {
      throw new BadRequestHttpException('Category not found');
    }

    return $category;
  }
}
