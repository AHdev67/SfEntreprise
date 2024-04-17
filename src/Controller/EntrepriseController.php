<?php

namespace App\Controller;

use App\Entity\Entreprise;
use App\Form\EntrepriseType;
use Doctrine\ORM\EntityManager;
use App\Repository\EntrepriseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\VarDumper\VarDumper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EntrepriseController extends AbstractController
{
    //----------------------------------------------
    //METHODE INDEX QUI RENVOIE LISTE DES ENTREPRISE
    //----------------------------------------------
    #[Route('/entreprise', name: 'app_entreprise')]
    // public function index(EntityManagerInterface $entityManager): Response
    public function index(EntrepriseRepository $entrepriseRepository): Response
    {
        // $entreprises = $entityManager->getRepository(Entreprise::class)->findAll();
        $entreprises = $entrepriseRepository->findBy([], ["raisonSociale" => "ASC"]);
        // dd($entreprises);
        return $this->render('entreprise/index.html.twig', [
            'entreprises' => $entreprises
        ]);
    }

    //-----------------------------------------------------------------------------------
    //METHODE NEW_EDIT FORM QUI RENVOIE UN FORMULAIRE D'AJOUT / MODIFICATION D'ENTREPRISE
    //-----------------------------------------------------------------------------------
    #[Route('/entreprise/new', name: 'new_entreprise')]
    #[Route('/entreprise/{id}/edit', name: 'edit_entreprise')]
    public function new_edit(Entreprise $entreprise = null, Request $request, EntityManagerInterface $entityManager): Response
    {
        if(!$entreprise){
            $entreprise = new Entreprise();
        }

        $form = $this->createForm(EntrepriseType::class, $entreprise);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $entreprise = $form->getData();
            //equivalent PDO prepare
            $entityManager->persist($entreprise);
            //equivalent PDO execute
            $entityManager->flush();

            return $this->redirectToRoute('app_entreprise');
        }

        return $this->render('entreprise/new.html.twig', [
            'formAddEntreprise' => $form,
            //var edit qui renvoie un booléen permettant l'affichage dynamique du titre du formulaire
            'edit' => $entreprise->getId()
        ]);
    }

    //-----------------------------------------------------
    //METHODE DELETE QUI SUPPRIME UNE ENTREPRISE
    //-----------------------------------------------------
    #[Route('/entreprise/{id}/delete', name: 'delete_entreprise')]
    public function delete(Entreprise $entreprise, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($entreprise);
        $entityManager->flush();

        return $this->redirectToRoute('app_entreprise');
    }

    //-----------------------------------------------------
    //METHODE SHOW QUI RENVOIE LES DETAILS D'UNE ENTREPRISE
    //-----------------------------------------------------
    #[Route('/entreprise/{id}', name: 'show_entreprise')]
    public function show(Entreprise $entreprise): Response
    {
        return $this->render('entreprise/show.html.twig', [
            'entreprise' => $entreprise
        ]);
    }

}
