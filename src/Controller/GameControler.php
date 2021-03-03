<?php

namespace App\Controller;

use App\Entity\Game;
use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GameControler
 * @package App\Controller
 * @Route("/products")
 */
final class GameControler extends AbstractController
{
    private GameRepository $gameRepository;

    public function __construct(GameRepository $gameRepository)
    {
        $this->gameRepository = $gameRepository;
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

            return new JsonResponse($games,Response::HTTP_OK);
        }
        $response = $this->gameRepository->getByID($query);
        return new JsonResponse($response->toArray(),Response::HTTP_OK);
    }

    /**
     * @Route("/genre",methods={"GET"})
     */
    public function getGameByGenre(Request $request): Response
    {
        $genre = $request->query->get('genre');
        $query = array_map(static function (Game $game):array{
            return $game->toArray();
        }, $this->gameRepository->getByGenre($genre));
        return new JsonResponse($query, Response::HTTP_OK);
    }

    /**
     * @Route("/add",methods={"POST"})
     */
    public function addGame(Request $request): Response
    {
        if(!($request)){
            return new JsonResponse(['status'=>'OK','message'=>'error empty']);
        }
        $requestArray = json_decode($request->getContent(),true);
        $diff = array_diff(array_keys($requestArray),['productName','price','genre','developer']);
        if (!(empty($diff)))
        {
            return new JsonResponse(['status'=>'OK','message'=>'Wrong column names'],Response::HTTP_BAD_REQUEST);
        }
        if ($this->gameRepository->getOneByProductName($requestArray['productName'])){
            return new JsonResponse(['status'=>'OK','message'=>'Game with this name already exists'],Response::HTTP_OK);
        }
        $game = new Game();
        $game->setProductName($requestArray['productName'])
            ->setDeveloper($requestArray['developer'])
            ->setGenre($requestArray['genre'])
            ->setPrice($requestArray['price']);
        $this->gameRepository->addGame($game);
        return new JsonResponse(['status'=>'OK','message'=>'created'],Response::HTTP_CREATED);
    }

    /**
     * @Route("/delete",methods={"DELETE"})
     */
    public function deleteGame(Request $request): Response
    {
        $gameArray = json_decode($request->getContent(), true);
        $gameByID = $this->gameRepository->getByID($gameArray['id']);
        $gameByProductName = $this->gameRepository->getOneByProductName($gameArray['productName']);
        if ($gameByID == $gameByProductName){
            $this->gameRepository->removeGame($gameByID);
            return new JsonResponse(['status'=>'OK','message'=>'Game Removed'],Response::HTTP_OK);
        }
        return new JsonResponse(['status'=>'OK','message'=>'Game not removed due to request problems']);
    }

    /**
     * @Route("/update",methods={"PATCH"})
     */
    public function patchGame(Request $request): Response
    {
        $requestArray = json_decode($request->getContent(), true);

    }
}
