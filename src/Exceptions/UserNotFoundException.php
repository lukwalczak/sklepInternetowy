<?php


namespace App\Exceptions;

final class UserNotFoundException extends \InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct("User not found");
    }
}