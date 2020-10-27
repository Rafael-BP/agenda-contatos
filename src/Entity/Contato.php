<?php

namespace App\Entity;

use App\Exception\EmailInvalidoException;
use App\Exception\NomeInvalidoException;
use App\Exception\TelefoneDuplicadoException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validation;

/**
 * Contato
 *
 * @ORM\Table(name="contato")
 * @ORM\Entity
 */
class Contato
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
     * @var string
     *
     * @ORM\Column(name="nome", type="string", length=100, nullable=false)
     */
    private $nome;

    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="string", length=254, nullable=true)
     */
    private $email;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Telefone", mappedBy="contato", cascade={"persist", "remove"})
     */
    private $telefones;

    /**
     * Contato constructor.
     */
    public function __construct()
    {
        $this->telefones = new ArrayCollection();
    }

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
    public function getNome(): ?string
    {
        return $this->nome;
    }

    /**
     * @param string $nome
     * @return $this
     */
    public function setNome(string $nome): self
    {
        $validator = Validation::createValidator();
        $violations = $validator->validate($nome, [
            new NotBlank(),
            new Length(['max' => 100])
        ]);

        if (0 !== count($violations)) {
            throw new NomeInvalidoException("Nome inválido.", Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->nome = $nome;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     * @return $this
     */
    public function setEmail(?string $email): self
    {
        $validator = Validation::createValidator();
        $violations = $validator->validate($email, [
            new Email(),
            new Length(['max' => 254])
        ]);

        if (0 !== count($violations)) {
            throw new EmailInvalidoException("Email inválido.", Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        $this->email = $email;

        return $this;
    }

    /**
     * @param Telefone $telefone
     * @return $this
     */
    public function adicionarTelefone(Telefone $telefone): self
    {
        foreach($this->getTelefones() as $tel) {
            if($tel->getNumero() == $telefone->getNumero()) {
                throw new TelefoneDuplicadoException("Telefone duplicado.", Response::HTTP_PRECONDITION_FAILED);
            }
        }
        $this->telefones->add($telefone);
        return $this;
    }

    /**
     * @return Collection
     */
    public function getTelefones(): Collection
    {
        return $this->telefones;
    }

    /**
     * @param Collection $telefones
     */
    public function setTelefones(Collection $telefones): void
    {
        $this->telefones = $telefones;
    }

}
