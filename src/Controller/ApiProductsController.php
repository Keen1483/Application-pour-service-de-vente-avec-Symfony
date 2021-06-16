<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Filesystem;

class ApiProductsController extends AbstractController
{
    /**
     * @Route("/api/products", name="api_products_get", methods={"GET"})
     */
    public function getProducts(ProductRepository $productRepository)
    {
        return $this->json($productRepository->findAll(), 200, [], ['groups' => 'product:read']);
    }

    /**
     * @Route("/api/products", name="api_products_save", methods={"POST"})
     */
    public function savePoduct(Request $request, SerializerInterface $si, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $jsonGet = $request->getContent();

        try {
            $product = $si->deserialize($jsonGet, Product::class, 'json');
            $product->setSetAt(new \DateTime());
            $errors = $validator->validate($product);
            if(count($errors) > 0) {
                return $this->json($errors, 400);
            }
            $em->persist($product);
            $em->flush();

            return $this->json($product, 201, [], ['groups' => 'product:read']);
            dd($product);
        } catch (NotEncodableValueException $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * @Route("/api/products", name="api_products_del", methods={"DELETE"})
     */
    public function deleteProduct(Request $request, SerializerInterface $si, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $jsonGet = $request->getContent();

        try {
            $product = $si->deserialize($jsonGet, Product::class, 'json');
            $product->setSetAt(new \DateTime());
            $errors = $validator->validate($product);
            if(count($errors) > 0) {
                return $this->json($errors, 400);
            }
            
            $product->setImage(
                $oldImage = new File($this->getParameter('images_directory').'/'.$product->getImage())
            );
            $filesystem = new Filesystem();
            $filesystem->remove($oldImage);
    
    
            $em->remove($product);
            $em->flush();

            return $this->json($product, 201, [], ['groups' => 'product:read']);
        } catch (NotEncodableValueException $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }
    }

}
