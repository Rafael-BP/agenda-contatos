<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

/**
 * TelefoneInvalido
 */
class TelefoneInvalidoException extends \InvalidArgumentException
{
    public $message = "Telefone inválido.";
    public $code = Response::HTTP_UNPROCESSABLE_ENTITY;
}
