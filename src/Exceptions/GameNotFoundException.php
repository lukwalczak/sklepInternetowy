<?php


namespace App\Exceptions;

final class GameNotFoundException extends \InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct("Game not found");
    }
}