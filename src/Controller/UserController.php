<?php

namespace App\Controller;

use App\Common\Auth\UserRoles;
use App\Common\Controller\DefaultController;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Services\PasswordChecker;

/**
 * Class UserController
 * @package App\Controller
 */
class UserController  extends DefaultController
{
    /**
     * @var UserRepository
     */
    private UserRepository $user_repository;

    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * @var ValidatorInterface
     */
    protected ValidatorInterface $validator;

    /**
     * @var UserPasswordHasherInterface
     */
    private UserPasswordHasherInterface $passwordHasher;


    /**
     * UserController constructor.
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @param UserRepository $repository
     * @param UserPasswordHasherInterface $passwordHasher
     */
    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        UserRepository $repository,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->user_repository = $repository;
        $this->passwordHasher = $passwordHasher;
    }


    /**
     * Получение списка пользователей
     * @Route("/api/users", name="getUsers", methods={"GET"})
     */
    public function getUsers(Request $request): Response
    {
        list($limit, $offset, $orderBy, $filterBy) = $this->getPaginationParams($request);
        $users = $this->user_repository->findWithSortAndFilters($filterBy, $orderBy, $limit, $offset);
        $total = $this->user_repository->countWithFilters($filterBy);

        $responseData = [];
        foreach ($users as $user) {
            try {
                $itemData = $this->serializer->normalize($item, false, User::getEntityAttributes());
                $responseData[] = $itemData;
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
     * Получение конкретного пользователя
     * @Route("/api/users/{id}", name="getOneUser", methods={"GET"})
     */
    public function getOneUser(int $id): Response
    {
        $user = $this->user_repository->find($id);
        if (!$user) {
            return $this->json([
                'success' => false,
                'errors' => ["Пользователь не найден"],
            ])->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        $responseData = [];
        try {
            $itemData = $this->serializer->normalize($item, false, User::getEntityAttributes());
            $responseData[] = $itemData;
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
     * Создание пользователя
     * @Route("/api/users", name="createUser", methods={"POST"})
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function createUser(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $username = filter_var($data['username'] ?? null, FILTER_SANITIZE_STRING);
        $fio = filter_var($data['fio'] ?? null, FILTER_SANITIZE_STRING);
        $password = filter_var($data['password'] ?? null, FILTER_SANITIZE_STRING);
        $email = filter_var($data['email'] ?? null, FILTER_SANITIZE_STRING);
        $roles = filter_var_array($data['roles_ids'] ?? [], FILTER_SANITIZE_STRING);
        

        $violations = $this->validate(
            User::getEntityConstraints(),
            [
                'username' => $username,
                'fio' => $fio,
                'password' => $password,
                'roles' => $roles,
                'email' => $email,
            ],
        );

        if (count($violations) > 0) {
            return $this->returnViolationsErrors($violations);
        }

        if (count(array_diff($roles, UserRoles::rolesList())) > 0) {
            return $this->json(['success' => false, 'errors' => ["Не корректные роли пользователя"]], Response::HTTP_BAD_REQUEST);
        }

        // TODO заменить на ассерт
        try {
            PasswordChecker::check($username, $password);
        } catch(\Exception $e) {
            return $this->json(array('success' => false, 'errors' => [$e->getMessage()]));
        }
        

        $manager = $this->getDoctrine()->getManager();
        $user = new User();
        $user->setFio($fio);
        $user->setUsername($username);
        $user->setPassword(
            $this->passwordHasher->hashPassword(
                $user,
                $password
            )
        );
        
        $user->setRoles($roles);
        $user->setEmail($email);

        try {
            $manager->persist($user);
            $manager->flush();
        } catch (UniqueConstraintViolationException $e) {
            return $this->json(['success' => false, 'errors' => ["Имя пользователя занято"]], Response::HTTP_BAD_REQUEST);
        }

        $responseData = [];
        try {
            $itemData = $this->serializer->normalize($item, false, User::getEntityAttributes());
            $responseData[] = $itemData;
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

    // TODO Сделать регистрацию
    // TODO Сделать восстановление пароля
    // TODO Сделать вход через вк и гугл
    // TODO Сделать смена пароля
    // TODO Сделать подтверждение учетки через почту

}
