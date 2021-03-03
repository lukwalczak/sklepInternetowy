<?php


namespace App\Controller;

use App\Exceptions\UserNotFoundException;
use App\Repository\UserRepository;
use App\Entity\User;
use http\Exception\BadMethodCallException;
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
        if(!($user))
        {
            return new JsonResponse(['status'=>'OK','message'=>'User does not exist'],
                Response::HTTP_NOT_FOUND);
        }
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