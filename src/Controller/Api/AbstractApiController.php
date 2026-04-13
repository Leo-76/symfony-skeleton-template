<?php

declare(strict_types=1);

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;

abstract class AbstractApiController extends AbstractController
{
    /**
     * Return a successful JSON response.
     *
     * @param mixed $data
     */
    protected function success(mixed $data, int $status = Response::HTTP_OK): JsonResponse
    {
        return $this->json([
            'success' => true,
            'data'    => $data,
        ], $status);
    }

    /**
     * Return a created (201) response.
     */
    protected function created(mixed $data): JsonResponse
    {
        return $this->success($data, Response::HTTP_CREATED);
    }

    /**
     * Return a no-content (204) response.
     */
    protected function noContent(): JsonResponse
    {
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Return a validation error response from a ConstraintViolationList.
     */
    protected function validationError(ConstraintViolationListInterface $violations): JsonResponse
    {
        $errors = [];
        foreach ($violations as $violation) {
            $field            = ltrim((string) $violation->getPropertyPath(), '.');
            $errors[$field][] = $violation->getMessage();
        }

        return $this->json([
            'success' => false,
            'errors'  => $errors,
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Return a generic error response.
     */
    protected function error(string $message, int $status = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return $this->json([
            'success' => false,
            'error'   => $message,
        ], $status);
    }

    /**
     * Return a not-found error.
     */
    protected function notFound(string $message = 'Resource not found.'): JsonResponse
    {
        return $this->error($message, Response::HTTP_NOT_FOUND);
    }
}
