<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

/**
 * EmailInvalido
 */
class EmailInvalidoException extends \InvalidArgumentException
{
    public $message = "Email inválido.";
    public $code = Response::HTTP_UNPROCESSABLE_ENTITY;
}
