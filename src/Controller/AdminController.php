<?php


namespace App\Controller;

use App\Repository\UserRepository;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminController
 * @package App\Controller
 * @Route("/admin")
 */
final class AdminController extends AbstractController
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/users/{userID}",methods={"GET"})
     */
    public function admin(?int $userID): Response
    {
        $user = $this->userRepository->getUserByID($userID);
        return new JsonResponse($user->toArray(),Response::HTTP_OK);
    }

    /**
     * @Route("/users",methods={"GET"})
     */
    public function getUsers(): Response
    {
        $users = array_map(static function (User $user):array{
            return $user->toArray();
            }, $this->userRepository->getAllUsers());
        return new JsonResponse($users,Response::HTTP_OK);
    }

}