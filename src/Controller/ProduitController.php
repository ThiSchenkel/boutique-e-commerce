<?php

namespace App\Controller;

use DateTime;
use App\Entity\Produit;
use App\Form\ProduitType;
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

class ProduitController extends AbstractController
{
    
    // /**
    //  * @Route("/produits", name="app_produit")
    //  */
    public function allProduits(ProduitRepository $repo)
    {
        $produits=$repo->findAll();
        // $categories =$repoCat->findAll();
        return $this->render('produit/allproduits.html.twig', [
            'produits'=>$produits,
            // 'categories'=>$categories
        ]);
    }

        /**
     * @Route("/produits/{id<\d+>}", name="produit_show")
     */
    public function show($id, ProduitRepository $repo)
    {
        $produit=$repo->find($id);
        return $this->render('produit/show.html.twig', [
            'produit'=>$produit
        ]);
    }

    /**
     * @Route("/categorie-{id<\d+>}", name="produit_categorie")
     */
    public function categorieProduits($id, CategorieRepository $repo)
    {
        $categorie=$repo->find($id);
        $categories= $repo->findAll();

        return $this->render('produit/allproduits.html.twig', [
            'produits'=>$categorie->getProduits(),
            'categories'=>$categories
        ]);
    }

}
