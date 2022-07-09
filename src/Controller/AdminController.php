<?php

namespace App\Controller;

use DateTime;
use App\Entity\Produit;
use App\Entity\Categorie;
use App\Form\ProduitType;
use App\Form\CategorieType;
use App\Controller\AdminController;
use App\Repository\ProduitRepository;
use App\Repository\CategorieRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/ajout-produit", name="admin_ajout_produit")
     */
    public function ajout(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger): Response
    {

         if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
             $this->addFlash('error', "Veuillez vous connecter pour accéder à la page");
             return $this->redirectToRoute('app_login');
        }

        if (!$this->isGranted('ROLE_ADMIN')) {
             $this->addFlash('error', "Vous n'avez pas les droits pour accéder à cette page");
             return $this->redirectToRoute('app_home');
        }

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

            return $this->redirectToRoute('admin_gestion_produit');
        }
        return $this->render('admin/formProduit.html.twig', [
            'formProduit'=>$form->createView()
        ]);
    }

       /**
     * @Route("/gestion-produits", name="admin_gestion_produit")
     */
    public function adminProduits(ProduitRepository $repo): Response
    {
         if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
             $this->addFlash('error', "Veuillez vous connecter pour accéder à la page");
             return $this->redirectToRoute('app_login');
        }

        if (!$this->isGranted('ROLE_ADMIN')) {
             $this->addFlash('error', "Vous n'avez pas les droits pour accéder à cette page");
             return $this->redirectToRoute('app_home');
        }

        $produits=$repo->findAll();

        return $this->render('admin/gestion-produits.html.twig', [
            'produits'=>$produits
        ]);
    }

           /**
     * @Route("/admin/detail_produit/{id<\d+>}", name="admin_detail_produit")
     */
    public function detailProduit($id, ProduitRepository $repo): Response
    {
        $produit=$repo->find($id);


        return $this->render('admin/oneProduit.html.twig', [
            'produit'=>$produit
        ]);

        
    }

    /**
     * @Route("/admin/update_produit/{id<\d+>}", name="update_produit")
     */
    public function update($id, ProduitRepository $repo, Request $request, SluggerInterface $slugger) 
    {
         if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
             $this->addFlash('error', "Veuillez vous connecter pour accéder à la page");
             return $this->redirectToRoute('app_login');
        }

        if (!$this->isGranted('ROLE_ADMIN')) {
             $this->addFlash('error', "Vous n'avez pas les droits pour accéder à cette page");
             return $this->redirectToRoute('app_home');
        }

        $produit = $repo->find($id);
        $form =$this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if($form->get('photo')->getData()){
                $file = $form->get('photo')->getData();
            $fileName = $slugger->slug($produit->getTitre()) . uniqid() . '.' . $file->guessExtension();

            try{
                $file->move($this->getParameter('images_directory'), $fileName);
            }catch(FileException $e){
            }
            $produit->setPhoto($fileName);
            }

            $repo->add($produit,1);
            
        $this->addFlash('success', "Le produit a bien été mis à jour");

        return $this->redirectToRoute("admin_gestion_produit");
        }

        return $this->render('admin/formProduit.html.twig', [
            'formProduit'=>$form->createView(),
        ]);
    }

    /**
     * @Route("/admin/delete_produit_{id<\d+>}", name="delete_produit")
     */
    public function delete($id, ProduitRepository $repo) : Response
    {
         if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
             $this->addFlash('error', "Veuillez vous connecter pour accéder à la page");
             return $this->redirectToRoute('app_login');
        }

        if (!$this->isGranted('ROLE_ADMIN')) {
             $this->addFlash('error', "Vous n'avez pas les droits pour accéder à cette page");
             return $this->redirectToRoute('app_home');
        }

                $produit = $repo->find($id);
                $repo->remove($produit, 1); 

                $this->addFlash('success', "La fiche a bien été supprimée");

                return $this->redirectToRoute("admin_gestion_produit");
    } 

    /**
     * @Route("/categorie-ajout", name="admin_ajout_categorie")
     */
    public function ajoutCategorie(Request  $request, CategorieRepository $repo) : Response
    {
         if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
             $this->addFlash('error', "Veuillez vous connecter pour accéder à la page");
             return $this->redirectToRoute('app_login');
        }

        if (!$this->isGranted('ROLE_ADMIN')) {
             $this->addFlash('error', "Vous n'avez pas les droits pour accéder à cette page");
             return $this->redirectToRoute('app_home');
        }
        
            $categorie = new Categorie();
            $form = $this->createForm(CategorieType:: class, $categorie);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $repo->add($categorie, 1);
                
                $this->addFlash('success', "La catégorie a bien été ajoutée");
                return $this->redirectToRoute('admin_gestion_produit');
            }
                return $this->render('admin/formCategorie.html.twig', [
                'formCategorie'=>$form->createView(),
        ]);
    } 

}

