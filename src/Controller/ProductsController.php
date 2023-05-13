<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;

use FOS\RestBundle\Controller\Annotations as Rest;

#[Route('/api')]
#[OA\Tag(name: 'Products')]
class ProductsController extends AbstractController
{

    public function __construct(
      private ProductRepository $productsRepository,
      private CategoryRepository $categoryRepository
    ) {
    }

    #[Rest\Get('/products', name: 'products')]
    #[Cache(maxage: 600, public: true)]
    #[Rest\View]
    #[Security(name: null)]
    #[OA\Response(
      response: 200,
      description: 'Successful response',
      content: new OA\JsonContent(
        type: 'array',
        items: new OA\Items(
          ref: new Model(
            type: Product::class,
            groups: ['list', 'Default']
          )
        )
      )
    )]
    public function getProducts(ManagerRegistry $doctrine)
    {
        return $this->productsRepository->findAll();
    }

    #[Rest\Get('/products/{id}', name: 'product', requirements: ['id' => '\d+'])]
    #[Cache(maxage: 600, public: true)]
    #[Rest\View]
    #[Security(name: null)]
    #[OA\Response(
      response: 200,
      description: 'Successful response',
      content: new Model(type: Product::class, groups: ['list', 'Default'])
    )]
    public function getProduct(int $id, ManagerRegistry $doctrine)
    {
        $product = $this->productsRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException(
              'No product found for id ' . $id
            );
        }
        return $product;
    }

    #[Route('/products', name: 'create_product', methods: ['POST'])]
    #[ParamConverter("product", converter: "fos_rest.request_body")]
    #[IsGranted('ROLE_USER')]
    #[Rest\View]
    #[OA\Response(
      response: 200,
      description: 'Successful response',
      content: new Model(type: Product::class, groups: ['create', 'Default'])
    )]
    #[OA\RequestBody(
      description: 'Product object that needs to be added to the store',
      required: true,
      content: new Model(type: Product::class, groups: ['create', 'Default'])
    )]
    public function createProduct(
      Product $product,
      ManagerRegistry $doctrine,
      ValidatorInterface $validator,
      Request $request
    ) {
        $validationErrors = $validator->validate($product);
        if (count($validationErrors)) {
            return $this->json($validationErrors, 400);
        }
        $data = json_decode($request->getContent(), true);
        $category_id = $data['category_id'] ?? null;
        if ($category_id) {
            $category = $this->categoryRepository->find($category_id);
            if (!$category) {
                throw $this->createNotFoundException(
                  'No category found for id ' . $category_id
                );
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
    #[OA\RequestBody(
      description: 'Product object that needs to be updated in the store',
      required: true,
      content: new Model(type: Product::class, groups: ['create', 'Default'])
    )]
    #[OA\Response(
      response: 200,
      description: 'Successful response',
      content: new Model(type: Product::class, groups: ['list', 'Default'])
    )]
    public function updateProduct(
      int $id,
      Product $product,
      ManagerRegistry $doctrine,
      ValidatorInterface $validator,
      Request $request
    ) {
        $originalProduct = $this->productsRepository->find($id);
        if (!$originalProduct) {
            throw $this->createNotFoundException(
              'No product found for id ' . $id
            );
        }
        $validationErrors = $validator->validate($product);
        if (count($validationErrors)) {
            return $this->json($validationErrors, 400);
        }

        $data = json_decode($request->getContent(), true);
        $category_id = $data['category_id'] ?? null;
        if ($category_id) {
            $category = $this->categoryRepository->find(
              $category_id
            );
            if (!$category) {
                throw $this->createNotFoundException(
                  'No category found for id ' . $category_id
                );
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
    #[OA\Response(
      response: 204,
      description: 'Successful response'
    )]
    public function deleteProduct(
      int $id,
      ManagerRegistry $doctrine
    ): JsonResponse {
        $product = $this->productsRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException(
              'No product found for id ' . $id
            );
        }
        $doctrine->getManager()->remove($product);
        $doctrine->getManager()->flush();
        return $this->json(null, 204);
    }

}
