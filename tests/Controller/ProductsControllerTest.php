<?php

namespace App\Tests\Controller;

use App\DataFixtures\AppFixtures;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

/**
 * @covers \App\Controller\ProductsController
 */
class ProductsControllerTest extends ControllerTestBase {

  /**
   * @covers \App\Controller\ProductsController::getProducts
   */
  public function testGetProducts() {
    $this->client->request('GET', '/api/products');
    $this->assertResponseStatusCodeSame(200);
    $contents = json_decode($this->client->getResponse()->getContent(), TRUE);
    $this->assertCount(3, $contents);
    $this->assertEquals([
      'id' => 1,
      'name' => 'Product 1',
      'price' => 100,
      'category' => [
        'id' => 1,
        'name' => 'Category 1',
      ],
    ], $contents[0]);
    $this->assertEquals([
      'id' => 2,
      'name' => 'Product 2',
      'price' => 200,
      'category' => [
        'id' => 1,
        'name' => 'Category 1',
      ],
    ], $contents[1]);
    $this->assertEquals([
      'id' => 3,
      'name' => 'Product 3',
      'price' => 300,
      'category' => [
        'id' => 2,
        'name' => 'Category 2',
      ],
    ], $contents[2]);
  }

  /**
   * @covers \App\Controller\ProductsController::getProduct
   */
  public function testGetProduct() {
    $this->client->request('GET', '/api/products/100');
    $this->assertResponseStatusCodeSame(404);

    $this->client->request('GET', '/api/products/1');
    $this->assertResponseStatusCodeSame(200);
    $contents = json_decode($this->client->getResponse()->getContent(), TRUE);
    $this->assertEquals([
      'id' => 1,
      'name' => 'Product 1',
      'price' => 100,
      'category' => [
        'id' => 1,
        'name' => 'Category 1',
      ],
    ], $contents);
  }

  public function testCreateProduct() {
    $this->client->jsonRequest('POST', '/api/products');
    $this->assertResponseStatusCodeSame(401);

    $this->loginTestUser();
    $this->client->jsonRequest('POST', '/api/products');
    $this->assertResponseStatusCodeSame(400);

    $this->client->jsonRequest('POST', '/api/products', [
      'name' => 'Product 4',
      'price' => 400,
      'category_id' => 1,
    ]);
    $this->assertResponseStatusCodeSame(200);
    $contents = json_decode($this->client->getResponse()->getContent(), TRUE);
    $this->assertEquals([
      'id' => 4,
      'name' => 'Product 4',
      'price' => 400,
      'category' => [
        'id' => 1,
        'name' => 'Category 1',
      ],
    ], $contents);
  }

  /**
   * @covers \App\Controller\ProductsController::updateProduct
   */
  public function testUpdateProduct() {
    $this->client->jsonRequest('POST', '/api/products');
    $this->assertResponseStatusCodeSame(401);

    $this->loginTestUser();
    $this->client->jsonRequest('PUT', '/api/products/100');
    $this->assertResponseStatusCodeSame(404);

    $this->client->jsonRequest('PUT', '/api/products/1');
    $this->assertResponseStatusCodeSame(400);

    $this->client->jsonRequest('PUT', '/api/products/1', [
      'name' => 'Product 1',
      'price' => 1000,
      'category_id' => 1,
    ]);
    $this->assertResponseStatusCodeSame(200);
    $contents = json_decode($this->client->getResponse()->getContent(), TRUE);
    $this->assertEquals([
      'id' => 1,
      'name' => 'Product 1',
      'price' => 1000,
      'category' => [
        'id' => 1,
        'name' => 'Category 1',
      ],
    ], $contents);
  }

  public function testDeleteProduct() {
    $this->client->request('DELETE', '/api/products/100');
    $this->assertResponseStatusCodeSame(401);

    $this->loginTestUser();
    $this->client->request('DELETE', '/api/products/100');
    $this->assertResponseStatusCodeSame(404);

    $this->client->request('DELETE', '/api/products/1');
    $this->assertResponseStatusCodeSame(204);
  }

}
