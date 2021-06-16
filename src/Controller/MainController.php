<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;



use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Filesystem;

class MainController extends AbstractController
{

    /**
     * @Route("/", name="app_homepage")
     */
    public function index(ProductRepository $repo)
    {
        $seconds = (int)5000/1000;
        while(true)
        {
            return $this->render('main/index.html.twig', [
                'title' => 'Accueil',
                'products' => $repo->findAll(),
                'admin' => false,
            ]);
            sleep($seconds);
        }
        
    }

    public function setInterval($f, $milliseconds)
    {
        $seconds=(int)$milliseconds/1000;
        while(true)
        {
            $f();
            sleep($seconds);
        }
    }

    function getProducts()
    {
        $repo = $this->getDoctrine()->getRepository(Product::class);
        return $this->render('main/index.html.twig', [
            'title' => 'Accueil',
            'products' => $repo->findAll()
        ]);
    }

    

    
    public function admin()
    {
        return $this->render('main/admin.html.twig', [
            'title' => 'manager'
        ]);
    }

    /**
     * @Route("/user/redirect", name="redirect")
     */
    public function afterConnected()
    {
        return $this->render('main/redirect.html.twig', [
            'title' => 'admin'
        ]);
    }

    /**
     * @Route("/user", name="user")
     */
    public function userConnect()
    {
        return $this->render('main/user_connect.html.twig', [
            'title' => 'user'
        ]);
    }

    /**
     * @Route("/admin/manager", name="manager")
     */
    public function userManager(UserRepository $userRepository)
    {
        $users = $userRepository->findAll();

        return $this->render('main/manager.html.twig', [
            'title' => 'manager',
            'users' => $users,
        ]);
    }

    /**
     * @Route("admin/{id<\d+>}/user", name="roles")
     */
    public function giveRoles(User $user, EntityManagerInterface $entityManagerInterface)
    {
        if(in_array("ROLE_ADMIN", $user->getRoles())) {
            $this->addFlash('error', 'Cet utilisateur est déja administrateur !');

            return $this->render('main/manager.html.twig', [
                'title' => 'manager',
                'users' => $this->getDoctrine()->getRepository(User::class)->findAll(),
            ]);
        } else {
            $user->setRoles(["ROLE_ADMIN"]);

            $entityManagerInterface->persist($user);
            $entityManagerInterface->flush();

            $this->addFlash('success', 'Administration réussie !');

            return $this->render('main/manager.html.twig', [
                'title' => 'manager',
                'users' => $this->getDoctrine()->getRepository(User::class)->findAll(),
            ]);
        }
    }

    /**
     * @Route("/ajax", name="ajax")
     */
    public function ajaxAction(Request $request)
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        
        if ($request->isXmlHttpRequest()) {
            $jsonData = [];
            foreach ($users as $user) {
                $temp = [
                    'email' => $user->getEmail(),
                    'username' => $user->getName(),
                ];
                $jsonData[] = $temp;
            }

            return new JsonResponse($jsonData);
        } else {
            return $this->render('main/ajax.html.twig', [
                'title' => 'Ajax request',
            ]);
        }
        
    }
}
