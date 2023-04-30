<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use FOS\RestBundle\Controller\Annotations as Rest;

#[Route('/api')]
class CategoriesController extends AbstractController
{
    #[Rest\Get('/categories', name: 'categories')]
    #[Cache(maxage: 600, public: true)]
    #[Rest\View]
    public function getCategories(ManagerRegistry $doctrine)
    {
        return $doctrine->getRepository(Category::class)->findAll();
    }

    #[Rest\Get('/categories/{id}', name: 'category', requirements: ['id' => '\d+'])]
    #[Cache(maxage: 600, public: true)]
    #[Rest\View]
    public function getCategory(int $id, ManagerRegistry $doctrine)
    {
        $category = $doctrine->getRepository(Category::class)->find($id);
        if (!$category) {
            throw $this->createNotFoundException('No category found for id ' . $id);
        }
        return $category;
    }

    #[Rest\Post('/categories', name: 'create_category')]
    #[ParamConverter("category", converter: "fos_rest.request_body")]
    #[Rest\View]
    #[IsGranted('ROLE_USER')]
    public function createCategory(Category $category, ManagerRegistry $doctrine, ValidatorInterface $validator)
    {
      $validationErrors = $validator->validate($category);
      if (count($validationErrors)) {
        return $this->json($validationErrors, 400);
      }
        $doctrine->getManager()->persist($category);
        $doctrine->getManager()->flush();
        return $category;
    }

    #[Rest\Put('/categories/{id}', name: 'update_category', requirements: ['id' => '\d+'])]
    #[ParamConverter("category", converter: "fos_rest.request_body")]
    #[Rest\View]
    #[IsGranted('ROLE_USER')]
    public function updateCategory(int $id, Category $category, ManagerRegistry $doctrine, ValidatorInterface $validator)
    {
        $originalCategory = $doctrine->getRepository(Category::class)->find($id);
        if (!$originalCategory) {
            throw $this->createNotFoundException('No category found for id ' . $id);
        }

        $validationErrors = $validator->validate($category);
        if (count($validationErrors)) {
          return $this->json($validationErrors, 400);
        }

        $originalCategory->setName($category->getName());
        $doctrine->getManager()->flush();
        return $originalCategory;
    }

    #[Rest\Delete('/categories/{id}', name: 'delete_category', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_USER')]
    public function deleteCategory(int $id, ManagerRegistry $doctrine): JsonResponse
    {
        $category = $doctrine->getRepository(Category::class)->find($id);
        if (!$category) {
            throw $this->createNotFoundException('No category found for id ' . $id);
        }
        $doctrine->getManager()->remove($category);
        $doctrine->getManager()->flush();
        return new JsonResponse(null, 204);
    }
}
