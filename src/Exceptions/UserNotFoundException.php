<?php


namespace App\Exceptions;


use http\Exception\InvalidArgumentException;

class UserNotFoundException extends InvalidArgumentException
{
    public function __construct(int $id)
    {
        parent::__construct(`User with ${id} not Found`);
    }
}