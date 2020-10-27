<?php

namespace App\Controller;

use App\Entity\Contato;
use App\Entity\Telefone;
use App\Exception\ContatoDeveTerPeloMenosUmTelefoneException;
use App\Exception\ContatoJaExisteException;
use App\Exception\EmailInvalidoException;
use App\Exception\NomeInvalidoException;
use App\Form\ContatoType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/contato")
 */
class ContatoController extends AbstractController
{
    /**
     * @Route("/", name="contato_lista", methods={"GET"})
     */
    public function listar(): Response
    {
        $contatos = $this->getDoctrine()
            ->getRepository(Contato::class)
            ->findAll();
        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);

        return new Response($serializer->serialize($contatos, 'json'), Response::HTTP_OK);
    }

    /**
     * @Route("/novo", name="contato_novo", methods={"POST"})
     */
    public function novo(Request $request): Response
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $parameters = json_decode($request->getContent(), true);

            $contatoExiste = $this->getDoctrine()
                ->getRepository(Contato::class)
                ->findBy(['nome' => $parameters['nome']]);
            if($contatoExiste) {
                throw new ContatoJaExisteException("Contato já existe.", Response::HTTP_PRECONDITION_FAILED);
            }

            $telefones = $parameters['telefones'];
            if(empty($telefones)) {
                throw new ContatoDeveTerPeloMenosUmTelefoneException("Contato deve ter pelo menos um telefone.", Response::HTTP_PRECONDITION_FAILED);
            }

            $contato = new Contato();
            $contato->setNome($parameters['nome']);
            $contato->setEmail($parameters['email']);
            foreach ($telefones as $tel) {
                $telefone = new Telefone();
                $telefone->setNumero($tel['numero']);
                $telefone->setContato($contato);
                $contato->adicionarTelefone($telefone);
            }

            $em->persist($contato);
            $em->flush();

            return new Response("Contato criado com sucesso.", Response::HTTP_CREATED);
        } catch(\Throwable $e) {
            return new Response($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @Route("/{id}", name="contato_obter", methods={"GET"})
     */
    public function obterUm(Contato $contato): Response
    {
        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        return new Response($serializer->serialize($contato, 'json'), Response::HTTP_OK);
    }

    /**
     * @Route("/{id}/editar", name="contato_editar", methods={"PUT"})
     */
    public function editar(Request $request, Contato $contato): Response
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $parameters = json_decode($request->getContent(), true);

            $contatoExiste = $this->getDoctrine()
                ->getRepository(Contato::class)
                ->findBy(['nome' => $parameters['nome']]);
            if(
                $contatoExiste &&
                $parameters['nome'] != $contato->getNome()
            ) {
                throw new ContatoJaExisteException("Contato já existe.", Response::HTTP_PRECONDITION_FAILED);
            }

            $telefones = $parameters['telefones'];
            if(empty($telefones)) {
                throw new ContatoDeveTerPeloMenosUmTelefoneException("Contato deve ter pelo menos um telefone.", Response::HTTP_PRECONDITION_FAILED);
            }

            $contato->setNome($parameters['nome']);
            $contato->setEmail($parameters['email']);
            foreach ($contato->getTelefones() as $tel) {
                $em->remove($tel);
            }
            $contato->setTelefones(new ArrayCollection());
            foreach ($telefones as $tel) {
                $telefone = new Telefone();
                $telefone->setNumero($tel['numero']);
                $telefone->setContato($contato);
                $contato->adicionarTelefone($telefone);
            }

            $em->persist($contato);
            $em->flush();

            return new Response("Contato atualizado com sucesso.", Response::HTTP_OK);
        } catch(\Throwable $e) {
            return new Response($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @Route("/{id}", name="contato_deletar", methods={"DELETE"})
     */
    public function deletar(Contato $contato): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($contato);
        $em->flush();

        return new Response("Contato apagado com sucesso.", Response::HTTP_OK);
    }
}
