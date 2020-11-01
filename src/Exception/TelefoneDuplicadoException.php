<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

/**
 * TelefoneDuplicadoException
 */
class TelefoneDuplicadoException extends \DomainException
{
    public $message = "Telefone duplicado.";
    public $code = Response::HTTP_PRECONDITION_FAILED;
}
