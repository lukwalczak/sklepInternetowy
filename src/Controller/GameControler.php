<?php

namespace App\Controller;

use App\Entity\Game;
use App\Repository\GameRepository;
use Doctrine\ORM\Query;
use http\QueryString;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GameControlerController
 * @package App\Controller
 * @Route("/api/products")
 */
final class GameControlerController extends AbstractController
{
    private GameRepository $gameRepository;

    public function __construct(GameRepository $gameRepository)
    {
        $this->gameRepository = $gameRepository;
    }

    /**
     * @Route("/getALL",methods={"GET"})
     */
    public function getList(): Response
    {
        $games = array_map(static function (Game $game):array{
            return $game->toArray();
            }, $this->gameRepository->getAll());
        return new JsonResponse($games,Response::HTTP_OK);

    }

    /**
     * @Route("/get",methods={"GET"})
     */
    public function getGame(Request $request): Response
    {
        $query = $request->query->get('id');
        if (!($query)){
            $games = array_map(static function (Game $game):array{
                return $game->toArray();
            }, $this->gameRepository->getAll());

            return new JsonResponse($games);
        }
        $response = $this->gameRepository->getByID($query);
        return new JsonResponse($response->toArray(),Response::HTTP_OK);
    }


    /**
     * @Route("/add",methods={"POST"})
     */
    public function addGame(Request $request): Response
    {
        if(!($request)){
            return new JsonResponse(['status'=>'OK','message'=>'error empty']);
        }
        $requestJSON = json_decode($request->getContent(),true);
        if ($this->gameRepository->getByProductName($requestJSON['productName'])){
            return new JsonResponse(['status'=>'OK','message'=>'Game with this name already exists']);
        }
        $game = new Game();
        $game->setProductName($requestJSON['productName'])
            ->setDeveloper($requestJSON['developer'])
            ->setGenre($requestJSON['genre'])
            ->setPrice($requestJSON['price']);
        $this->gameRepository->addGame($game);
        return new JsonResponse(['status'=>'OK','message'=>'created'],Response::HTTP_CREATED);

    }
}
