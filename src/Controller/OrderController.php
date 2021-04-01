<?php


namespace App\Controller;


use App\Entity\Order;
use App\Exceptions\GameNotFoundException;
use App\Repository\GameRepository;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * Class OrderController
 * @package App\Controller
 * @Route("/orders")
 */
class OrderController
{
    private $security;
    private OrderRepository $orderRepository;
    private UserRepository $userRepository;

    public function __construct(Security $security, OrderRepository $orderRepository, GameRepository $gameRepository, UserRepository $userRepository)
    {
        $this->security = $security;
        $this->orderRepository = $orderRepository;
        $this->gameRepository = $gameRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/new",methods={"POST"})
     */
    public function newOrder(Request $request): Response
    {
        $requestArray = json_decode($request->getContent(),true);
        $order = new Order();
        $user = $this->security->getUser();
        $order->setUser($user);
        foreach ($requestArray['game'] as $gameID) {
            try {
                $game = $this->gameRepository->getByID($gameID);
                $order->addGame($game);
            }catch (GameNotFoundException $e)
            {
                return new JsonResponse(['message'=>'Something went wrong'],Response::HTTP_BAD_REQUEST);
            }
        }
        $this->orderRepository->addOrder($order);
        return new JsonResponse(['message'=>$user->getUsername()." thanks for your order"],Response::HTTP_OK);
    }

    /**
     * @Route("/",methods={"GET"})
     */
    public function getOrders(): Response
    {
        $user = $this->security->getUser()->getUsername();
        $id = $this->userRepository->getUserByEmail($user)->getId();
        $orders = $this->orderRepository->getUserOrders($id);
        $arr = [];
        foreach ( $orders as $order){
            $arr[$order['orderID']][] = $order['gameID'];
        }
        return new JsonResponse($arr,Response::HTTP_OK);
    }
    /**
     * @Route("/a",methods={"GET"})
     */
    public function getOrderss(): Response
    {
        $user = $this->security->getUser()->getUsername();
        $id = $this->userRepository->getUserByEmail($user)->getId();
        $orders = $this->orderRepository->getUserOrders($id);

        return new JsonResponse($orders,Response::HTTP_OK);
    }
}