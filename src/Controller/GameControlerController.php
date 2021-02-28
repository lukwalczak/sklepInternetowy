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
 * @Route("/api")
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
        return new JsonResponse($games);

    }

    /**
     * @Route("/get",name="index",methods={"GET"})
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
        return new JsonResponse($response->toArray());
    }

}
