<?php

namespace App\Controller;

use App\Entity\Contato;
use App\Entity\Telefone;
use App\Exception\ContatoDeveTerPeloMenosUmTelefoneException;
use App\Exception\ContatoJaExisteException;
use App\Form\ContatoType;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/contato")
 */
class ContatoController extends DefaultController
{
    /**
     * @Route("/", name="contato_lista", methods={"GET"})
     */
    public function listar(PaginatorInterface $paginator, Request $request): Response
    {
        $orderBy = $request->get('orderBy') ? $request->get('orderBy') : 'nome';
        $orderMethod = $request->get('orderMethod') ? $request->get('orderMethod') : 'ASC';
        $contatos = $this->getDoctrine()
            ->getRepository(Contato::class)
            ->findBy(
                [],
                [$orderBy => $orderMethod]
            );

        $pagination = $paginator->paginate(
            $contatos,
            $request->query->getInt('page',$_SERVER['PAGE_DEFAULT']),
            $request->query->getInt('limit',$_SERVER['LIMIT_DEFAULT'])
        );

        return new Response($this->serializer->serialize($pagination, 'json'), Response::HTTP_OK);
    }

    /**
     * @Route("/novo", name="contato_novo", methods={"POST"})
     */
    public function novo(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $parameters = json_decode($request->getContent(), true);

        $contatoExiste = $this->getDoctrine()
            ->getRepository(Contato::class)
            ->findBy(['nome' => $parameters['nome']]);
        if($contatoExiste) {
            throw new ContatoJaExisteException();
        }

        $telefones = $parameters['telefones'];
        if(empty($telefones)) {
            throw new ContatoDeveTerPeloMenosUmTelefoneException();
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
    }

    /**
     * @Route("/{id}", name="contato_obter", methods={"GET"})
     */
    public function obterUm(Contato $contato): Response
    {
        return new Response($this->serializer->serialize($contato, 'json'), Response::HTTP_OK);
    }

    /**
     * @Route("/{id}/editar", name="contato_editar", methods={"PUT"})
     */
    public function editar(Request $request, Contato $contato): Response
    {
        $em = $this->getDoctrine()->getManager();
        $parameters = json_decode($request->getContent(), true);

        $contatoExiste = $this->getDoctrine()
            ->getRepository(Contato::class)
            ->findBy(['nome' => $parameters['nome']]);
        if(
            $contatoExiste &&
            $parameters['nome'] != $contato->getNome()
        ) {
            throw new ContatoJaExisteException();
        }

        $telefones = $parameters['telefones'];
        if(empty($telefones)) {
            throw new ContatoDeveTerPeloMenosUmTelefoneException();
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
