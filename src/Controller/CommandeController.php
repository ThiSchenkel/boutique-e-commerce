<?php

namespace App\Controller;

use DateTime;
use App\Entity\Commande;
use App\Entity\CommandeDetail;
use App\Repository\ProduitRepository;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommandeDetailRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommandeController extends AbstractController
{
    /**
     * @Route("/commande", name="app_commande")
     */
    public function index(): Response
    {
        return $this->render('commande/index.html.twig', [
            'controller_name' => 'CommandeController',
        ]);
    }

     /**
     * @Route("/passer-ma-commande", name="passer_commande")
     */
    public function passerCommande(
        SessionInterface $session, 
        ProduitRepository $repoPro, 
        CommandeRepository $repoCom, 
        CommandeDetailRepository $repoDet,
        EntityManagerInterface $manager ): Response
    {
        $commande = new Commande();
        // on crée un objet Commande pour remplir les informations
        $panier =$session->get('panier', []);
        // dd($panier);

        // On récupère l'user en cours
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash("error", "Veuillez vous connecter ou vous inscrire pour passer la commande!");

            return $this->redirectToRoute("app_login");
        }
        if (empty($panier)) {
            $this->addFlash("error", "Votre panier est vide, vous ne pouvez pas passer commande!");

            return $this->redirectToRoute("app_produit");
        }

        $dataPanier =[];
        $total = 0;

        foreach ($panier as $id => $quantite) {
            $produit =$repoPro->find($id);
            $dataPanier[]=
            [
                "produit"=>$produit,
                "quantite"=> $quantite,
                "sousTotal"=> $produit->getPrix()*$quantite
            ];
            $total += $produit->getPrix()*$quantite;
        }

        // dd($dataPanier);

        $commande->setUser($user)
                    ->setDateDeCommande(new DateTime('now'))
                    ->setMontant($total)
                ;

        $repoCom->add($commande);

        foreach ($dataPanier as $key => $value) {
            $commandeDetail =new CommandeDetail();
            $produit = $value["produit"];
            $quantite = $value["quantite"];
            $sousTotal = $value["sousTotal"];

            $commandeDetail->setCommande($commande)
                            ->setProduit($produit)
                            ->setQuantite($quantite)
                            ->setPrix($sousTotal)
                            ;
            
            $repoDet->add($commandeDetail);
        }

        $manager->flush();
        $session->remove("panier");
        $this->addFlash("success", "Votre commande a bien été enregistrée!");
        return $this->redirectToRoute("app_produit");

    }
}
