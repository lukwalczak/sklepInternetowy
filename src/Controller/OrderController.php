<?php


namespace App\Controller;


use App\Entity\Order;
use App\Repository\GameRepository;
use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class OrderController
{
    private $security;
    private OrderRepository $orderRepository;

    public function __construct(Security $security, OrderRepository $orderRepository, GameRepository $gameRepository)
    {
        $this->security = $security;
        $this->orderRepository = $orderRepository;
        $this->gameRepository = $gameRepository;
    }

    /**
     * @Route("/orders/new",methods={"POST"})
     */
    public function newOrder(Request $request): Response
    {
        $requestArray = json_decode($request->getContent(),true);
        $order = new Order();
        $user = $this->security->getUser();
        $order->setUser($user);
        foreach ($requestArray['game'] as $gameID) {
            $game = $this->gameRepository->getByID($gameID);
            $order->addGame($game);
        }
        $this->orderRepository->addOrder($order);
        return new JsonResponse(['message'=>$user->getUsername()."thanks for ordering".$requestArray['game']],Response::HTTP_OK);
    }
}