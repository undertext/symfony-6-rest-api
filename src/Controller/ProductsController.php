<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use FOS\RestBundle\Controller\Annotations as Rest;

#[Route('/api')]
class ProductsController extends AbstractController
{
    #[Rest\Get('/products', name: 'products')]
    #[Cache(maxage: 600, public: true)]
    #[Rest\View]
    public function getProducts(ManagerRegistry $doctrine)
    {
      return $doctrine->getRepository(Product::class)->findAll();
    }

    #[Rest\Get('/products/{id}', name: 'product', requirements: ['id' => '\d+'])]
    #[Cache(maxage: 600, public: true)]
    #[Rest\View]
    public function getProduct(int $id, ManagerRegistry $doctrine)
    {
      $product = $doctrine->getRepository(Product::class)->find($id);
      if (!$product) {
        throw $this->createNotFoundException('No product found for id ' . $id);
      }
      return $product;
    }

    #[Route('/products', name: 'create_product', methods: ['POST'])]
    #[ParamConverter("product", converter: "fos_rest.request_body")]
    #[IsGranted('ROLE_USER')]
    #[Rest\View]
    public function createProduct(Product $product, ManagerRegistry $doctrine, ValidatorInterface $validator, Request $request)
    {
      $validationErrors = $validator->validate($product);
      if (count($validationErrors)) {
        return $this->json($validationErrors, 400);
      }
      $data = json_decode($request->getContent(), true);
      $category_id = $data['category_id'] ?? null;
      if ($category_id) {
        $category = $doctrine->getRepository(Category::class)->find($category_id);
        if (!$category) {
          throw $this->createNotFoundException('No category found for id ' . $category_id);
        }
        $product->setCategory($category);
      }

      $doctrine->getManager()->persist($product);
      $doctrine->getManager()->flush();
      return $product;
    }

    #[Rest\Put('/products/{id}', name: 'update_product', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_USER')]
    #[ParamConverter("product", converter: "fos_rest.request_body")]
    #[Rest\View]
    public function updateProduct(int $id, Product $product, ManagerRegistry $doctrine, ValidatorInterface $validator, Request $request)
    {
      $originalProduct = $doctrine->getRepository(Product::class)->find($id);
      if (!$originalProduct) {
        throw $this->createNotFoundException('No product found for id ' . $id);
      }
      $validationErrors = $validator->validate($product);
      if (count($validationErrors)) {
        return $this->json($validationErrors, 400);
      }

      $data = json_decode($request->getContent(), true);
      $category_id = $data['category_id'] ?? null;
      if ($category_id) {
        $category = $doctrine->getRepository(Category::class)->find($category_id);
        if (!$category) {
          throw $this->createNotFoundException('No category found for id ' . $category_id);
        }
        $originalProduct->setCategory($category);
      }

      $originalProduct->setName($product->getName());
      $originalProduct->setPrice($product->getPrice());
      $originalProduct->setDescription($product->getDescription());
      $doctrine->getManager()->flush();
      return $originalProduct;
    }

    #[Rest\Delete('/products/{id}', name: 'delete_product', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_USER')]
    public function deleteProduct(int $id, ManagerRegistry $doctrine): JsonResponse
    {
      $product = $doctrine->getRepository(Product::class)->find($id);
      if (!$product) {
        throw $this->createNotFoundException('No product found for id ' . $id);
      }
      $doctrine->getManager()->remove($product);
      $doctrine->getManager()->flush();
      return $this->json(null, 204);
    }

}
