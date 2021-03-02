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
 * Class UserControler
 * @package App\Controller
 * @Route("/api/admin")
 */
final class AdminController extends AbstractController
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/getUsers",methods={"GET"})
     */
    public function getAllUsers(Request $request): Response
    {
        $query = $request->query->get('id');
        if (!($query)){
            $users = array_map(static function (User $user):array{
                return $user->toArray();
            }, $this->userRepository->getAllUsers());

            return new JsonResponse($users,Response::HTTP_OK);
        }
        $user = $this->userRepository->getUserByID($query);
        return new JsonResponse($user->toArray(),Response::HTTP_OK);
    }

}