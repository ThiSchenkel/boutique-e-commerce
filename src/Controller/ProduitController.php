<?php

namespace App\Controller;

use DateTime;
use App\Entity\Produit;
use App\Form\ProduitType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ProduitController extends AbstractController
{


    /**
     * @Route("/produits", name="app_produit")
     */
    public function allProduits(ManagerRegistry $doctrine, Request $request): Response
    {
        $allProduits=$doctrine->getRepository(Produit::class)->findAll();
        return $this->render('produit/AllProduits.html.twig', [
            'allProduits'=>$allProduits
        ]);
    }


    /**
     * @Route("/admin/ajout-produit", name="admin_app_produit")
     */
    public function ajout(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if($form->isSubmitted()&& $form->isValid()){
            $produit->setdateEnregistrement ( new DateTime("now"));

            $file = $form->get('photo')->getData();
            $fileName = $slugger->slug($produit->getTitre()) . uniqid() . '.' . $file->guessExtension();

            try{
                $file->move($this->getParameter('images_directory'), $fileName);
            }catch(FileException $e){
            }
            $produit->setPhoto($fileName);

            $manager=$doctrine->getManager();
            $manager->persist($produit);
            $manager->flush();

             $this->addFlash('success', "Le produit a bien été ajouté");

            return $this->redirectToRoute('app_home');
        }
        return $this->render('admin/formProduit.html.twig', [
            'formProduit'=>$form->createView()
        ]);
    }







}
