<?php

namespace App\Controller;

use App\Entity\Tool;
use App\Entity\UserToolAccess;
use App\Repository\CategoryRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class CreateToolAction
{
    public function __invoke(
      Request $request,
      EntityManagerInterface $entityManager,
      CategoryRepository $categoryRepository,
      SerializerInterface $serializer,
      ): Tool
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

        $entityManager->persist($tool);
        $entityManager->flush();

        $returnValue = $serializer->normalize($tool, null);
        // Replace category by its name alone
        $returnValue['category'] = $tool->getCategory()->getName();

        return json_encode($returnValue);
    }
}
