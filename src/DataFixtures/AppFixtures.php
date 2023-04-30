<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Doctrine fixtures for testing purposes.
 */
class AppFixtures extends Fixture {

  /**
   * {@inheritdoc}
   */
  public function load(ObjectManager $manager): void {
    $user = new User();
    $user->setEmail('user')->setPassword('password')->setRoles(['ROLE_USER']);
    $manager->persist($user);

    $category1 = (new Category())->setName('Category 1');
    $category2 = (new Category())->setName('Category 2');

    $product1 = (new Product())->setName('Product 1')
      ->setPrice(100)
      ->setCategory($category1);
    $product2 = (new Product())->setName('Product 2')
      ->setPrice(200)
      ->setCategory($category1);
    $product3 = (new Product())->setName('Product 3')
      ->setPrice(300)
      ->setCategory($category2);

    foreach ([$product1, $product2, $product3] as $entity) {
      $manager->persist($entity);
    }

    $manager->flush();
  }

}
