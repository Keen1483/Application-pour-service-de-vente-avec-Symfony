<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Product;
use App\Form\CommentType;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Filesystem;

class ShopController extends AbstractController
{
    /**
     * @Route("/products", name="products")
     */
    public function list(ProductRepository $productRepository)
    {
        $products = $productRepository->findAll();
        $elects = [];
        $cosms = [];
        $tronics = [];
        $dresses = [];
        $foods = [];
        foreach ($products as $key => $product) {
            $category = $product->getCategory();
            if ($category->getName() == 'Electroménager') {
                $elects[] = $product;
            } else if($category->getName() == 'Cosmétique') {
                $cosms[] = $product;
            } else if($category->getName() == 'Electronique') {
                $tronics[] = $product;
            } else if($category->getName() == 'Habillement') {
                $dresses[] = $product;
            } else if($category->getName() == 'Aliments et Boissons') {
                $foods[] = $product;
            }   
        }

        return $this->render('shop/list.html.twig', [
            'title' => 'Products',
            'elects' => $elects,
            'cosms' => $cosms,
            'tronics' => $tronics,
            'dresses' => $dresses,
            'foods' => $foods
        ]);
    }

    /**
     * @Route("/products/{id<\d+>}", name="show")
     */
    public function show(Product $product, Request $request, EntityManagerInterface $entityManager)
    {
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $comment->setCreatedAt(new \DateTime())
                    ->setArticle($product);

            $comment = $form->getData();

            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('show', ['id' => $product->getId()], 301);
        }
        
        return $this->render('shop/show.html.twig', [
            'title' => 'Show',
            'product' => $product,
            'comment' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/new", name="new")
     * @Route("/admin/{id<\d+>}/edit", name="edit")
     */
    public function form(Product $product = null, Request $request, EntityManagerInterface $entityManagerInterface, SluggerInterface $slugger, ProductRepository $productRepository)
    {
        $listProducts = $this->getDoctrine()->getRepository(Product::class)->findAll();

        if(!$product) {
            $product = new Product();
        }

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $product = $form->getData();

            // Check price
            if($product->getPrice() <= 0 || (int)$product->getPrice() == 0) {
                $this->addFlash('error', 'Le prix est incorrect');

                return $this->render('shop/new.html.twig', [
                    'title' => 'New',
                    'form_product' => $form->createView(),
                    'edit_product' => $product->getId() !== null,
                    'products' => $listProducts,
                    'admin' => true,
                ]);

            }

            if(!$product->getId()) {
                $product->setSetAt(new \DateTime());
            }

            $product = $form->getData();

            $image = $form->get('image')->getData();
            if ($image) {

                if($product->getId()) {
                    $product->setImage(
                        $oldImage = new File($this->getParameter('images_directory').'/'.$product->getImage())
                    );
                    $filesystem = new Filesystem();
                    $filesystem->remove($oldImage);
                }
                $originaImagename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeImagename = $slugger->slug($originaImagename);
                $newImagename = $safeImagename.'-'.uniqid().'.'.$image->guessExtension();

                // Move the file to the directory where image are stored
                try {
                    $image->move(
                        $this->getParameter('images_directory'),
                        $newImagename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Echec de sauvegarde d\'image');

                    return $this->render('shop/new.html.twig', [
                        'title' => 'New',
                        'form_product' => $form->createView(),
                        'edit_product' => $product->getId() !== null,
                        'products' => $listProducts,
                        'admin' => true,
                    ]);
                }

                // updates the 'Imagename' property to store the image file name
                // instead of its contents
                $product->setImage($newImagename);
            }

            if (!$product->getImage()) {
                $this->addFlash('error', 'Uploader une image pour le produit');
            } else {
                $listProducts = count($productRepository->findByCategory($product->getCategory()));
                $product->setQuantity($listProducts++);

                $entityManagerInterface->persist($product);
                $entityManagerInterface->flush();

                $this->redirectToRoute('show', ['id' => $product->getId()], 301);
            }
        }

        return $this->render('shop/new.html.twig', [
            'title' => 'New',
            'form_product' => $form->createView(),
            'edit_product' => $product->getId() !== null,
            'products' => $listProducts,
            'admin' => true,
        ]);
    }

    /**
     * @Route("/products/{id<\d+>}/delete", name="delete")
     */
    public function deleteProduct(ProductRepository $productRepository ,int $id, EntityManagerInterface $entityManagerInterface): Response
    {
        $product = $productRepository->find($id);

        /* Delete image uploaded*/
        $product->setImage(
            $oldImage = new File($this->getParameter('images_directory').'/'.$product->getImage())
        );
        $filesystem = new Filesystem();
        $filesystem->remove($oldImage);


        $entityManagerInterface->remove($product);
        $entityManagerInterface->flush();

        $this->addFlash('success', 'Suppression réuissie !');

        return $this->redirectToRoute("products");
    }


    public function miniImage($imageName)
    {
        $src = imagecreatefromjpeg($imageName);
        $dst = imagecreatetruecolor(200, 200);

        $lg_src = imagesx($src);
        $hg_src = imagesy($src);

        $lg_dst = imagesx($dst);
        $hg_dst = imagesy($dst);

        //On crée la miniature
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $lg_dst, $hg_dst, $lg_src, $hg_src);

        // On enregistre la miniature sous le nom "ndj_mini.jpg"
        imagepng($dst, 'images_mini/ndj_mini.jpg');
    }
}
