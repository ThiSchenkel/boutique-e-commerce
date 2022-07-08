<?php

namespace App\Controller;

use App\Form\AdminType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/passer-en-admin_{id<\d+>}", name="passer_en_admin")
     */
    public function passerEnAdmin($id, UserRepository $repo, Request $request) : Response
    {
        $secret = "TropBien@cetAppli";

        $form = $this->createForm(AdminType::class);
        $form->handleRequest($request);

        $user = $repo->find($id);

        if (!$user) {
            $this->addFlash("error", "Aucun user trouvé avec l'id : $id");

            return redirecToRoute("app_home");
        }

        if ($form->isSubmitted()&& $form->isValid()) {
            if ($form->get('secret')->getData()==$secret){
            $user->setRoles(["ROLE_ADMIN"]);
            }else{
                $this->addFlash("error", "Vous n'avez pas les droits pour cette action, veuillez contacter l'admin");
                return redirectToRoute("app_home");
            }
            $repo->add($user, 1);
            $this->addFlash("success", "Vous êtes désormais Admin, veuillez vous reconnecter pour profiter de vos prévilèges");
            return $this->redirectToRoute("app_home");
        }

        return $this->render("security/passerEnAdmin.html.twig", [
            'user'=>$user,
            'formAdmin'=>$form->createView()
        ]);
    }
}
