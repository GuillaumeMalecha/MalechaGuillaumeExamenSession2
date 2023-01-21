<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Form\LivreType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LivreController extends AbstractController
{
    /**
     * @Route("/", name="touslivres")
     */

    //Fonction permettant d'afficher la liste de tous les livres depuis la db
    public function touslivres(EntityManagerInterface $entityManager): Response
    {
        $repository = $entityManager->getRepository(Livre::class);
        $listeLivres = $repository->findAll();

        if (!$listeLivres){
            return $this->redirectToRoute('ajouterlivre');
        }

        return $this->render('livre/index.html.twig', [
            'livres' => $listeLivres,
        ]);
    }

    /**
     * @Route("/detaillivre/{id}", name="detaillivre")
     */

    //Fonction permettant d'afficher les détails du livre sélectionné
    public function detaillivre($id, EntityManagerInterface $entityManager): Response
    {
        $repository = $entityManager->getRepository(Livre::class);
        $livre = $repository->find($id);

        return $this->render('livre/detail.html.twig', [
            'livre' => $livre
        ]);
    }


    /**
     * @Route("/ajouterlivre", name="ajouterlivre")
     */

    //Fonction permettant d'ajouter un livre
    public function ajouterlivre(Request $request, EntityManagerInterface $entityManager): Response
    {
        $livre = new Livre();
        $livre->setDateAjout(new \DateTime('now'));
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $livre = $form->getData();
            $entityManager->persist($livre);
            $entityManager->flush();

            return $this->redirectToRoute('touslivres');
        }

        return $this->renderForm('livre/ajouter.html.twig', [
            'form'=>$form
        ]);
    }

    /**
     * @Route("/detaillivre/{id}/supprimer", name="supprimerlivre")
     */

    //Fonction permettant de supprimer un livre
    public function supprimerLivre(int $id, EntityManagerInterface $entityManager)
    {
        $repository = $entityManager->getRepository(Livre::class);
        $livre = $repository->find($id);
        $entityManager->remove($livre);
        $entityManager->flush();

        return $this->redirectToRoute('touslivres');
    }

    /**
     * @Route("/detaillivre/{id}/modifier", name="modifierlivre")
     */

    //Fonction permettant de modifier un livre
    public function modifierlivre(int $id, EntityManagerInterface $entityManager, Request $request)
    {
        $repository = $entityManager->getRepository(Livre::class);
        $livre = $repository->find($id);
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $livre = $form->getData();
            $entityManager->flush();

            return $this->redirectToRoute('touslivres');
        }

        return $this->renderForm('livre/modifier.html.twig', [
            'form'=>$form,
            'livre'=>$livre,
        ]);
    }
}
