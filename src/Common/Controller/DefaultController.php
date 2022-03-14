<?php

namespace App\Common\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DefaultController extends AbstractController
{
    use ValidatorTrait;

    protected function returnViolationsErrors($violations): \Symfony\Component\HttpFoundation\JsonResponse
    {
        return $this->json(
            ['success' => false, 'errors' => $this->violationsToArray($violations)],
            Response::HTTP_BAD_REQUEST
        );
    }

    /**
     * Получение стандартных параметров пагинации
     * @param Request $request
     * @param array $default_order_by
     * @param int $default_limit
     * @return array [$limit, $offset, $orderBy, $filterBy]
     */
    protected function getPaginationParams(Request $request, array $default_order_by = [] , int $default_limit = 10): array
    {
        $limit = intval(filter_var($request->get('limit', $default_limit), FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE));
        $offset = intval(filter_var($request->get('offset', 0), FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE));
        $orderBy = $request->get('orderBy') ?? $default_order_by;
        $filterBy = $request->get('filterBy') ?? [];
        if ($limit <= 0) {
            $limit = 900000000;
        }
        return [$limit, $offset, $orderBy, $filterBy];
    }
}
