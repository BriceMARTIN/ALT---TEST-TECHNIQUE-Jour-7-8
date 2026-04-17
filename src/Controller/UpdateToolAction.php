<?php

namespace App\Controller;

use App\Entity\Tool;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class UpdateToolAction
{
    public function __invoke(
      Tool $tool,
      Request $request,
      EntityManagerInterface $entityManager,
      SerializerInterface $serializer
      ): Tool
    {
        $data = $request->toArray();

        // Only allow updating specific fields, and keep existing values if not provided
        $tool->setMonthlyCost($data['monthlyCost'] ?? $tool->getMonthlyCost());
        $tool->setStatus($data['status'] ?? $tool->getStatus());
        $tool->setDescription($data['description'] ?? $tool->getDescription());
        $tool->setUpdatedAt(new DateTime());

        $entityManager->persist($tool);
        $entityManager->flush();

        $returnValue = $serializer->normalize($tool, null);
        $returnValue['category'] = $tool->getCategory()->getName();

        return $returnValue; // Return the updated Tool entity to be persisted by API Platform
    }
}
