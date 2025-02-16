<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }


    public function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * @Route("/login", name="login", methods={"POST"})
     */
    public function login(Request $request, EntityManagerInterface $manager)
    {
        $user = $this->getUser();
        $random = $this->generateRandomString(30);
        $user->setToken($random);
        $date = date("Y m d h:i:s");
        //$date2 = date("my");
        //$rvref = "";
        //$rvref = $date2."RV-";
        $user->setLastLogin($date);
        $manager->persist($user);
        $manager->flush();
        $tipo ='';
        if (in_array('ROLE_PERSONAL', $user->getRoles())) {

          /*
            if (!is_null($user->getAseguradora())) {
                $tipo =$user->getAseguradora()->getNombre();
            } else {
                $tipo = '';
            }
            */
        } else {
            /*
              if (!is_null($user->getSucursal())) {
                  $tipo =$user->getSucursal()->getNombre();
              } else {
                  $tipo = '';
              }
              */
        }

        if ($user->getActivo()) {
            return $this->json([
              'username' => $user->getUsername(),
              'roles' => $user->getRoles(),
              'token' => $user->getToken(),
              'lastLogin' => $user->getlastLogin(),
              'name' => $user->getNombre(),
              'tipo' => $tipo,
          ]);
        } else {
            return $this->json([
              'username' => '',
              'roles' => '',
              'token' => '',
              'lastLogin' => '',
              'name' => '',
              'tipo' => ''
          ]);
        }
    }




    /**
     * @Route("/security", name="security")
     */
    public function index(): Response
    {
        return $this->render('security/index.html.twig', [
            'controller_name' => 'SecurityController',
        ]);
    }
}
