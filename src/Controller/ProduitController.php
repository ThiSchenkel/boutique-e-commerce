<?php

namespace App\Controller;

use DateTime;
use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
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
        $allProduits=$repo->findAll();
        return $this->render('produit/allProduits.html.twig', [
            'allProduits'=>$allProduits
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

}
