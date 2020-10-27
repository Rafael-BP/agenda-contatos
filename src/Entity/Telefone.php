<?php

namespace App\Entity;

use App\Exception\NomeInvalidoException;
use App\Exception\TelefoneInvalidoException;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validation;

/**
 * Telefone
 *
 * @ORM\Table(name="telefone", indexes={@ORM\Index(name="fk_telefone_contato_idx", columns={"contato_id"})})
 * @ORM\Entity
 */
class Telefone
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="numero", type="string", length=30, nullable=true)
     */
    private $numero;

    /**
     * @var Contato
     *
     * @ORM\ManyToOne(targetEntity="Contato", inversedBy="telefones")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="contato_id", referencedColumnName="id")
     * })
     */
    private $contato;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getNumero(): ?string
    {
        return $this->numero;
    }

    /**
     * @param string|null $numero
     * @return $this
     */
    public function setNumero(?string $numero): self
    {
        $validator = Validation::createValidator();
        $violations = $validator->validate($numero, [
            new NotBlank(),
            new Length(['max' => 30])
        ]);

        if (0 !== count($violations)) {
            throw new TelefoneInvalidoException("Telefone inválido.", Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->numero = $numero;

        return $this;
    }

    /**
     * @return Contato|null
     */
    public function getContato(): ?Contato
    {
        return $this->contato;
    }

    /**
     * @param Contato|null $contato
     * @return $this
     */
    public function setContato(?Contato $contato): self
    {
        $this->contato = $contato;

        return $this;
    }


}
