<?php

namespace App\Controller;

use App\Common\Controller\DefaultController;
use App\Entity\Difficulty;
use App\Repository\DifficultyRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class DifficultyController
 * @package App\Controller
 */
class DifficultyController extends DefaultController
{
    /**
     * @var DifficultyRepository
     */
    private DifficultyRepository $repository;

    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * @var ValidatorInterface
     */
    protected ValidatorInterface $validator;


    /**
     * DifficultyController constructor.
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     */
    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        DifficultyRepository $repository
    ) {
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->repository = $repository;
    }

    /**
     * Получение списка
     * @Route("/api/difficulty", name="getDifficulty", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function getList(Request $request): Response
    {
        list($limit, $offset, $orderBy, $filterBy) = $this->getPaginationParams($request);
        $items = $this->repository->findWithSortAndFilters($filterBy, $orderBy, $limit, $offset);
        $total = $this->repository->countWithFilters($filterBy);

        $responseData = [];
        foreach ($items as $item) {
            try {
                $responseData[] = $this->serializer->normalize($item, false, Difficulty::getEntityAttributes());
            } catch (ExceptionInterface $e) {
                return $this->json([
                    'success' => false,
                    'errors' => [$e->getMessage()],
                ])->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        return $this->json([
            'success' => true,
            'data' => $responseData,
            'total' => $total,
        ]);
    }

    

    /**
     * Редактирование
     * @Route("/api/difficulty/{id}", name="editDifficulty", methods={"POST"})
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function edit(Request $request, int $id): Response
    {
        $data = json_decode($request->getContent(), true);
        $name = filter_var($data['name'] ?? null, FILTER_SANITIZE_STRING);

        $violations = $this->validate(
            Difficulty::getEntityConstraints(),
            [
                'name' => $name
            ],
        );

        if (count($violations) > 0) {
            return $this->returnViolationsErrors($violations);
        }

        $manager = $this->getDoctrine()->getManager();
        if ($id == 0) {
            $item = new Difficulty();
        } else {
            $item = $this->repository->find($id);
            if (!$item) {
                return $this->json([
                    'success' => false,
                    'errors' => ["Объект не найден"],
                ])->setStatusCode(Response::HTTP_NOT_FOUND);
            }
        }
        
        $item->setName($name);

        try {
            $manager->persist($item);
            $manager->flush();
        } catch (UniqueConstraintViolationException $e) {
            return $this->json(['success' => false, 'errors' => ["Имя объекта занято"]], Response::HTTP_BAD_REQUEST);
        }

        $responseData = [];
        try {
            $responseData[] = $this->serializer->normalize($item,false, Difficulty::getEntityAttributes());
        } catch (ExceptionInterface $e) {
            return $this->json([
                'success' => false,
                'errors' => [$e->getMessage()],
            ])->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json([
            'success' => true,
            'data' => $responseData,
        ]);
    }


    /**
     * Удаление
     * @Route("/api/difficulty/{id}", name="deleteDifficulty", methods={"DELETE"})
     * @param int $id
     * @return Response
     */
    public function delete(int $id): Response
    {
        $manager = $this->getDoctrine()->getManager();
        $item = $this->repository->find($id);
        if (!$item) {
            return $this->json([
                'success' => false,
                'errors' => ["Обьект не найден"],
            ])->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        try {
            $manager->remove($item);
            $manager->flush();
        } catch (ForeignKeyConstraintViolationException $e) {
            return $this->json(['success' => false, 'errors' => ["Невозможно удалить связанный обьект"]], Response::HTTP_BAD_REQUEST);
        }

        return $this->json([
            'success' => true,
        ]);
    }
}
