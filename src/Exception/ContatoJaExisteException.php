<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

/**
 * ContatoJaExisteException
 */
class ContatoJaExisteException extends \DomainException
{
    public $message = "Contato já existe.";
    public $code = Response::HTTP_PRECONDITION_FAILED;
}
