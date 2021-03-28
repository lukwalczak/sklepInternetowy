<?php


namespace App\Controller;

use App\Exceptions\UserNotFoundException;
use App\Repository\UserRepository;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Class UserController
 * @package App\Controller
 * @Route("/")
 */
final class UserController extends AbstractController
{
    private UserRepository $userRepository;

    private UserPasswordEncoder $passwordEncoder;

    private Security $security;

    public function __construct(UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder, Security $security)
    {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->security = $security;
    }

    /**
     * @Route("/register",methods={"POST"})
     */
    public function register(Request $request): Response
    {
        $requestArray = json_decode($request->getContent(), true);
        try {
            $this->userRepository->getUserByEmail($requestArray['email']);
            return new JsonResponse(['status'=>'ERROR','message'=>'user with this email already exists'],Response::HTTP_BAD_REQUEST);
        }catch (UserNotFoundException $exception){
            $user = new User();
            $user->setEmail($requestArray['email'])
                ->setPassword($this->passwordEncoder->encodePassword($user, $requestArray['password']))
                ->setRoles(['ROLE_USER']);
            $this->userRepository->newUser($user);
            return new JsonResponse(['status'=>'OK','message'=>'registred new user'],Response::HTTP_CREATED);
        }
    }

    /**
     * @Route("/restore",methods={"PATCH"})
     */
    public function restorePassword(Request $request): Response
    {
        $requestArray = json_decode($request->getContent(),true);
        $user = $this->userRepository->getUserByEmail($requestArray['username']);
        if (is_null($user))
        {
            return new JsonResponse(['status'=>'ERROR',
                'message'=>'Request was not processed due to request problems'],
                Response::HTTP_BAD_REQUEST);
        }
        $user->setPassword($this->passwordEncoder->encodePassword($user,$requestArray['password']));
        $this->userRepository->updateUser($user);
        return new JsonResponse(['status'=>'OK','message'=>'Password changed'],Response::HTTP_OK);
    }

    /**
     * @Route("/userData",methods={"GET"})
     */
    public function getUserInformation(): Response
    {
        $userUsername = $this->security->getUser()->getUsername();
        $user = $this->userRepository->getUserByEmail($userUsername)->toArray();
        return new JsonResponse($user,Response::HTTP_OK);
    }
}