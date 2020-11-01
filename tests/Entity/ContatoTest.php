<?php

namespace App\Tests\Entity;

use App\Entity\Contato;
use App\Entity\Telefone;
use PHPUnit\Framework\TestCase;
use Faker\Factory as FakerFactory;

class ContatoTest extends TestCase
{
    /**
     * DataProvider de Lances de um Leilão
     * @return array
     */
    public function contatosComTelefones()
    {
        $faker = FakerFactory::create();

        $contatoCom2Telefones = new Contato($faker->name);
        $contatoCom2Telefones->setEmail($faker->email);
        $contatoCom2Telefones->adicionarTelefone(new Telefone($faker->phoneNumber, $contatoCom2Telefones));
        $contatoCom2Telefones->adicionarTelefone(new Telefone($faker->phoneNumber, $contatoCom2Telefones));

        $contatoCom1Telefone = new Contato($faker->name);
        $contatoCom1Telefone->adicionarTelefone(new Telefone($faker->phoneNumber, $contatoCom2Telefones));

        return [
            'contato-com-2-telefones' => [2, $contatoCom2Telefones],
            'contato-com-1-telefone' => [1, $contatoCom1Telefone]
        ];
    }

    /**
     * @dataProvider contatosComTelefones
     * @param $qtdTelefones int
     * @param $contato Contato
     */
    public function testContatoDeveReceberTelefones(int $qtdTelefones, Contato $contato)
    {
        static::assertCount($qtdTelefones, $contato->getTelefones());
    }

}
