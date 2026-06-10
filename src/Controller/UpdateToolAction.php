<?php

namespace App\Controller;

use App\Entity\Tool;
use App\Service\ToolService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UpdateToolAction
{
    public function __invoke(
        Tool $tool,
        Request $request,
        ToolService $toolService,
        LoggerInterface $logger,
    ): JsonResponse {
        try {
            $data = $request->toArray();

            $tool = $toolService->updateTool($tool, $data);

            $errors = $toolService->validateTool($tool);
            if (!empty($errors)) {
                return new JsonResponse(['errors' => $errors], Response::HTTP_BAD_REQUEST);
            }

            $toolService->persistTool($tool);
            $returnValue = $toolService->serializeToolResponse($tool);

            return new JsonResponse($returnValue);
        } catch (UniqueConstraintViolationException $e) {
            $logger->error('Tool update failed due to unique constraint violation', ['exception' => $e]);
            return new JsonResponse(['error' => 'Tool update was failed due to a unique constraint violation'], 400);
        } catch (BadRequestHttpException $e) {
            $logger->error('Tool update failed', ['exception' => $e]);
            return new JsonResponse(['error' => 'Invalid request data'], 400);
        } catch (Exception $e) {
            $logger->error('Tool update failed', ['exception' => $e]);
            return new JsonResponse(['error' => 'An error occurred while processing your request'], 500);
        }
    }
}
