<?php

namespace App\Controller;

use App\Entity\Tool;
use App\Repository\CategoryRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CreateToolAction
{
    public function __invoke(
      Request $request,
      EntityManagerInterface $entityManager,
      CategoryRepository $categoryRepository,
      SerializerInterface $serializer,
      ValidatorInterface $validator,
      ): JsonResponse
    {
        $data = $request->toArray();
        $tool = new Tool();

        $tool->setName($data['name'] ?? '');
        $tool->setDescription($data['description'] ?? '');
        $tool->setVendor($data['vendor'] ?? '');
        $tool->setWebsiteUrl($data['websiteUrl'] ?? '');
        $tool->setCategory($categoryRepository->find($data['category'] ?? null));
        $tool->setMonthlyCost($data['monthlyCost'] ?? '0');
        $tool->setOwnerDepartment($data['ownerDepartment'] ?? '');

        $tool->setStatus('active');
        $tool->setActiveUsersCount(0);
        $tool->setCreatedAt(new DateTime());
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
        // Replace category by its name alone
        if ($tool->getCategory()) {
            $returnValue['category'] = $tool->getCategory()->getName();
        }

        return new JsonResponse($returnValue);
    }
}
