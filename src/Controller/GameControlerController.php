<?php

namespace App\Controller;

use App\Entity\Game;
use App\Repository\GameRepository;
use http\Env\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
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
     * @Route(methods={"GET"})
     */
    public function getList(): Response
    {
        $games = array_map(static function (Game $game):array{
            return $game->toArray();
        }, $this->gameRepository->getAll());

        return new JsonResponse($games);
    }






//    /**
//     * @Route("/game/controler", name="game_controler")
//     */
//    public function index(): Response
//    {
//        return $this->render('game_controler/index.html.twig', [
//            'controller_name' => 'GameControlerController',
//        ]);
//    }
}
