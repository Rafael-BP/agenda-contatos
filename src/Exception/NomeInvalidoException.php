<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

/**
 * NomeInvalido
 */
class NomeInvalidoException extends \InvalidArgumentException
{
    public $message = "Nome inválido.";
    public $code = Response::HTTP_UNPROCESSABLE_ENTITY;
}
