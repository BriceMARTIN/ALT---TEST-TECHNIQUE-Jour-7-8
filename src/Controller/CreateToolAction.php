<?php

namespace App\Controller;

use App\Service\ToolService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CreateToolAction
{
    public function __invoke(
        Request $request,
        ToolService $toolService,
        LoggerInterface $logger,
    ): JsonResponse {
        try {
            $data = $request->toArray();
            $tool = $toolService->createTool($data);

            $errors = $toolService->validateTool($tool);
            if (!empty($errors)) {
                return new JsonResponse(['errors' => $errors], Response::HTTP_BAD_REQUEST);
            }

            $toolService->persistTool($tool);
            $returnValue = $toolService->serializeToolResponse($tool);

            return new JsonResponse($returnValue);
        } catch (UniqueConstraintViolationException $e) {
            $logger->error('Tool creation failed due to unique constraint violation', ['exception' => $e]);
            return new JsonResponse(['error' => 'Tool creation was failed due to a unique constraint violation'], 400);
        } catch (BadRequestHttpException $e) {
            $logger->error('Tool creation failed', ['exception' => $e]);
            return new JsonResponse(['error' => 'Invalid request data'], 400);
        } catch (Exception $e) {
            $logger->error('Tool creation failed', ['exception' => $e]);
            return new JsonResponse(['error' => 'An error occurred while processing your request'], 500);
        }
    }
}
