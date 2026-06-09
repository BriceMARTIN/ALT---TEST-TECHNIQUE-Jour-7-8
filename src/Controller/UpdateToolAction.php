<?php

namespace App\Controller;

use App\Entity\Tool;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Exception;
use App\Repository\CategoryRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UpdateToolAction
{
    public function __invoke(
        Tool $tool,
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        CategoryRepository $categoryRepository,
        LoggerInterface $logger,
    ): JsonResponse {
        try {
            $data = $request->toArray();

            // Only allow updating specific fields, and keep existing values if not provided
            $tool->setMonthlyCost($data['monthlyCost'] ?? $tool->getMonthlyCost());
            $tool->setStatus($data['status'] ?? $tool->getStatus());
            $tool->setDescription($data['description'] ?? $tool->getDescription());
            $tool->setUpdatedAt(new DateTime());

            $violations = $validator->validate($tool);
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[] = [
                        'property' => $violation->getPropertyPath(),
                        'message' => $violation->getMessage(),
                    ];
                }
                return new JsonResponse(['errors' => $errors], Response::HTTP_BAD_REQUEST);
            }

            $category = $categoryRepository->find($data['category'] ?? null);
            if (!$category) {
                throw new BadRequestHttpException('Category not found');
            }

            $entityManager->persist($tool);
            $entityManager->flush();

            $returnValue = json_decode($serializer->serialize($tool, 'json'), true);
            if ($tool->getCategory()) {
                $returnValue['category'] = $tool->getCategory()->getName();
            }

            return new JsonResponse($returnValue); // Return the updated Tool entity to be persisted by API Platform
        } catch (UniqueConstraintViolationException $e) {
            $logger->error('Tool update failed due to unique constraint violation', ['exception' => $e]);
            return new JsonResponse(['error' => 'Tool update was failed due to a unique constraint violation'], 400);
        } catch (Exception $e) {
            $logger->error('Tool update failed', ['exception' => $e]);
            return new JsonResponse(['error' => 'An error occurred while processing your request'], 500);
        }
    }
}
