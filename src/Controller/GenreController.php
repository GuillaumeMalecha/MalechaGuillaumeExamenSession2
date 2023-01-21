<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Form\GenreType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GenreController extends AbstractController
{
    /**
     * @Route("/genre", name="tousgenres")
     */

    //Fonction permettant d'afficher tous les genres depuis la db
    public function tousgenres(EntityManagerInterface $entityManager): Response
    {
        $repository = $entityManager->getRepository(Genre::class);
        $genres = $repository->findAll();

        if (!$genres){
            return $this->redirectToRoute('ajoutergenre');
        }

        return $this->render('genre/index.html.twig', [
            'controller_name' => 'GenreController',
            'genres' => $genres
        ]);
    }

    /**
     * @Route("/genre/ajouter", name="ajoutergenre")
     */

    //Fonction permettant d'ajouter un genre littéraire
    public function ajouterGenre(Request $request, EntityManagerInterface $entityManager){
        $genre = new Genre();
        $form = $this->createForm(GenreType::class, $genre);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $genre = $form->getData();
            $entityManager->persist($genre);
            $entityManager->flush();

            return $this->redirectToRoute('tousgenres');
        }

        return $this->renderForm('genre/ajouter.html.twig', [
            'form'=>$form,
        ]);
    }

    /**
     * @Route("/genre/{id}/modifier", name="modifiergenre")
     */

    //Fonction permettant de modifier un genre littéraire
    public function modifierGenre(Request $request, EntityManagerInterface $entityManager, int $id){
        $repository = $entityManager->getRepository(Genre::class);
        $genre = $repository->find($id);
        $form = $this->createForm(GenreType::class, $genre);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $genre = $form->getData();
            $entityManager->flush();

            return $this->redirectToRoute('tousgenres');
        }

        return $this->renderForm('genre/modifier.html.twig', [
            'form'=>$form,
            'genre'=>$genre,
        ]);
    }
}
