<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

/**
 * ContatoDeveTerPeloMenosUmTelefone
 */
class ContatoDeveTerPeloMenosUmTelefoneException extends \DomainException
{
    public $message = "Contato deve ter pelo menos um telefone.";
    public $code = Response::HTTP_PRECONDITION_FAILED;
}
