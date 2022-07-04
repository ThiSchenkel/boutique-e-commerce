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
    
    /**
     * @Route("/produits", name="app_produit")
     */
    public function allProduits(ManagerRegistry $doctrine, Request $request): Response
    {
        $allProduits=$doctrine->getRepository(Produit::class)->findAll();
        return $this->render('produit/allproduits.html.twig', [
            'allProduits'=>$allProduits
        ]);
    }

           /**
     * @Route("/one_produit/{id<\d+>}", name="one_produit")
     */
    public function oneCar($id, ManagerRegistry $doctrine): Response
    {
        $produit=$doctrine->getRepository(Produit::class)->find($id);
        return $this->render('produit/oneProduit.html.twig', [
            'produit'=>$produit
        ]);
    }


}
