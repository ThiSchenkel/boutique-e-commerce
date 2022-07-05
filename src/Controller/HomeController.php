<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(ProduitRepository $repo): Response
    {
        $produit = $repo->findBy([], ['dateEnregistrement'=>'DESC'],4);
        return $this->render('home/index.html.twig', [
            'produit'=>$produit]);
    }
}
