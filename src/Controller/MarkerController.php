<?php

namespace App\Controller;

use App\Common\Controller\DefaultController;
use App\Entity\Marker;
use App\Repository\MarkerRepository;
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
 * Class MarkerController
 * @package App\Controller
 */
class MarkerController extends DefaultController
{
    /**
     * @var MarkerRepository
     */
    private MarkerRepository $repository;

    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * @var ValidatorInterface
     */
    protected ValidatorInterface $validator;


    /**
     * MarkerController constructor.
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     */
    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        MarkerRepository $repository
    ) {
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->repository = $repository;
    }

    /**
     * Получение списка
     * @Route("/api/marker", name="getMarker", methods={"GET"})
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
                $responseData[] = $this->serializer->normalize($item, false, Marker::getEntityAttributes());
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
     * @Route("/api/marker/{id}", name="editMarker", methods={"POST"})
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function edit(Request $request, int $id): Response
    {
        $data = json_decode($request->getContent(), true);
        $name = filter_var($data['name'] ?? null, FILTER_SANITIZE_STRING);
        $description = filter_var($data['description'] ?? null, FILTER_SANITIZE_STRING);
        $difficulty_id = filter_var($data['difficulty_id'] ?? null, FILTER_SANITIZE_NUMBER_INT, FILTER_NULL_ON_FAILURE);
        $latitude = filter_var($data['latitude'] ?? null, FILTER_SANITIZE_STRING);
        $longitude = filter_var($data['longitude'] ?? null, FILTER_SANITIZE_STRING);
        $photoFile = $request->files->get('photo');


        $violations = $this->validate(
            Marker::getEntityConstraints(),
            [
                'name' => $name,
                'description' => $description,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'photoFile' => $photoFile,
            ],
        );

        if (count($violations) > 0) {
            return $this->returnViolationsErrors($violations);
        }

        $manager = $this->getDoctrine()->getManager();

        $difficulty = null;
        if ($difficulty_id > 0) {
            $difficultyRepository = $manager->getRepository(Difficulty::class);
            $difficulty = $difficultyRepository->find($difficulty_id);
        }


        if ($id == 0) {
            $item = new Marker();
            $createdBy = $this->getUser();
            $item->setCreatedBy($createdBy);
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
        $item->setDescription($description);
        $item->setLatitude($latitude);
        $item->setLongitude($longitude);
        $item->difficulty($difficulty);

        try {
            $manager->persist($item);
            $manager->flush();
        } catch (UniqueConstraintViolationException $e) {
            return $this->json(['success' => false, 'errors' => ["Имя объекта занято"]], Response::HTTP_BAD_REQUEST);
        }

        $responseData = [];
        try {
            $responseData[] = $this->serializer->normalize($item,false, Marker::getEntityAttributes());
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
     * @Route("/api/marker/{id}", name="deleteMarker", methods={"DELETE"})
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
