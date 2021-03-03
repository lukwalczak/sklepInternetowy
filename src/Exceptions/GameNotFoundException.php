<?php


namespace App\Exceptions;


use http\Exception\InvalidArgumentException;

class GameNotFoundException extends InvalidArgumentException
{
    public function __construct(int $id)
    {
        parent::__construct(`Game with ${id} not found`);
    }
}