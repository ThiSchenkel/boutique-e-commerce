<?php

namespace App\Controller;

use DateTime;
use App\Entity\Produit;
use App\Form\ProduitType;
use App\Controller\AdminController;
use App\Repository\ProduitRepository;
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

            return $this->redirectToRoute('app_produit');
        }
        return $this->render('admin/formProduit.html.twig', [
            'formProduit'=>$form->createView()
        ]);
    }

    /**
     * @Route("/admin/produits", name="admin_app_produit")
     */
    public function adminProduits(ManagerRegistry $doctrine, Request $request): Response
    {
        $adminProduits=$doctrine->getRepository(Produit::class)->findAll();
        return $this->render('admin/adminProduits.html.twig', [
            'adminProduits'=>$adminProduits
        ]);
    }

    /**
     * @Route("/admin/update_produit/{id<\d+>}", name="update_produit")
     */
    public function update(ManagerRegistry $doctrine, $id, Request $request) : Response
    {
        $produit = $doctrine->getRepository(Produit::class)->find($id);
        $form =$this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
        $produit->setdateEnregistrement ( new DateTime("now"));
            $photo = $form->get('photo')->getData();
            if($produit->getPhoto()!= null){
            $file = $form->get('photo')->getData();
            $fileName= uniqid(). '-' .$file->guessExtension();
            try{
            $file->move(
                $this->getParameter('images_directory'),
                $fileName
            );
            }catch(FileException $e){
                return new Response($e->getMessage());
            }
            $produit->setPhoto($fileName);
        }
        $manager=$doctrine->getManager();
        $manager->persist($produit);
        $manager->flush();

        $this->addFlash('success', "Le produit a bien été mis à jour");

        return $this->redirectToRoute("admin_app_produit");
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
                $produit = $repo->find($id);
                $repo->remove($produit, 1); 

                $this->addFlash('success', "La fiche a bien été supprimée");

                return $this->redirectToRoute("admin_app_produit");
    } 

}

