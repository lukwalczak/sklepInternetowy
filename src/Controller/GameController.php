<?php

namespace App\Controller;

use App\Entity\Game;
use App\Exceptions\GameNotFoundException;
use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GameController
 * @package App\Controller
 * @Route("/products")
 */
final class GameController extends AbstractController
{
    private GameRepository $gameRepository;

    public function __construct(GameRepository $gameRepository)
    {
        $this->gameRepository = $gameRepository;
    }

    /**
     * @Route("/get",methods={"GET"})
     */
    public function getGame(): Response
    {
        $games = array_map(static function (Game $game):array{
            return $game->toArray();
            }, $this->gameRepository->getAll());

        return new JsonResponse($games,Response::HTTP_OK);
    }

    /**
     * @Route("/get/{id}",methods={"GET"})
     * @param int $id
     * @return Response
     */
    public function getGameByID(int $id): Response
    {
        try {
            $game = $this->gameRepository->getByID($id);
        }catch (GameNotFoundException $e){
            return new JsonResponse(['message'=>$e->getMessage()],Response::HTTP_NOT_FOUND);
        }
        return new JsonResponse($game->toArray(),Response::HTTP_OK);
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
        $diff = array_diff(array_keys($requestArray),['productName','price','genre','developer','description','imageURL']);
        if (!(empty($diff)))
        {
            return new JsonResponse(['status'=>'FAILED','message'=>'Wrong column names'],Response::HTTP_BAD_REQUEST);
        }
        try {
            $exists = $this->gameRepository->getOneByProductName($requestArray['productName']);
        }catch (GameNotFoundException $e)
        {
            $game = new Game();
            $game->setProductName($requestArray['productName'])
                ->setDeveloper($requestArray['developer'])
                ->setGenre($requestArray['genre'])
                ->setPrice($requestArray['price'])
                ->setDescription($requestArray['description'])
                ->setImageURL($requestArray['imageURL'])
                ->setShortDescription($requestArray['shortDescription']);
            $this->gameRepository->addGame($game);
            return new JsonResponse(['status'=>'OK','message'=>'created'],Response::HTTP_CREATED);
        }
        return new JsonResponse(['status'=>'FAILED','message'=>'Game with this name already exists'],Response::HTTP_OK);
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
        return new JsonResponse(['status'=>'FAILED','message'=>'Game not removed due to request problems']);
    }

    /**
     * @Route("/update",methods={"PATCH"})
     */
    public function patchGame(Request $request): Response
    {
        $requestArray = json_decode($request->getContent(), true);
        $diff = array_diff(array_keys($requestArray),['id','productName','price']);
        if (!empty($diff))
        {
            return new JsonResponse(['status'=>'FAILED','message'=>'Request rejected'],
                Response::HTTP_BAD_REQUEST);
        }
        try {
            $game = $this->gameRepository->getByID($requestArray['id']);
            $p = $this->gameRepository->getOneByProductName($requestArray['productName']);
        }catch (GameNotFoundException $e)
        {
            return new JsonResponse(['status'=>'FAILED','message'=>'Request rejected'],
                Response::HTTP_BAD_REQUEST);
        }
        if($game !== $p)
        {
            return new JsonResponse(['status'=>'FAILED','message'=>'Request rejected'],
                Response::HTTP_BAD_REQUEST);
        }
        $game->setPrice($requestArray['price']);
        $this->gameRepository->updateGame($game);
        return new JsonResponse(['status'=>'UPDATED'],Response::HTTP_OK);
    }
}
