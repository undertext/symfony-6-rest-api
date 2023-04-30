<?php

namespace App\Tests\Controller;

/**
 * @covers \App\Controller\CategoriesController
 */
class CategoriesControllerTest extends ControllerTestBase  {

    /**
     * @covers \App\Controller\CategoriesController::getCategories
     */
    public function testGetCategories() {
      $this->client->request('GET', '/api/categories');
      $this->assertResponseStatusCodeSame(200);
      $contents = json_decode($this->client->getResponse()->getContent(), TRUE);
      $this->assertCount(2, $contents);
      $this->assertEquals([
        'id' => 1,
        'name' => 'Category 1',
      ], $contents[0]);
      $this->assertEquals([
        'id' => 2,
        'name' => 'Category 2',
      ], $contents[1]);

    }

    /**
     * @covers \App\Controller\CategoriesController::getCategory
     */
    public function testGetCategory() {
      $this->client->request('GET', '/api/categories/100');
      $this->assertResponseStatusCodeSame(404);

      $this->client->request('GET', '/api/categories/1');
      $this->assertResponseStatusCodeSame(200);
      $contents = json_decode($this->client->getResponse()->getContent(), TRUE);
      $this->assertEquals([
        'id' => 1,
        'name' => 'Category 1',
      ], $contents);
    }

    /**
     * @covers \App\Controller\CategoriesController::createCategory
     */
    public function testCreateCategory() {
      $this->client->jsonRequest('POST', '/api/categories', [
        'name' => 'Category 3',
      ]);
      $this->assertResponseStatusCodeSame(401);

      $this->loginTestUser();
      $this->client->jsonRequest('POST', '/api/categories', [
        'name' => 'Category 3',
      ]);
      $contents = json_decode($this->client->getResponse()->getContent(), TRUE);
      $this->assertEquals([
        'id' => 3,
        'name' => 'Category 3',
      ], $contents);
    }

    /**
     * @covers \App\Controller\CategoriesController::updateCategory
     */
    public function testUpdateCategory() {
      $this->client->jsonRequest('PUT', '/api/categories/1',[
        'name' => 'Category 1+',
      ]);
      $this->assertResponseStatusCodeSame(401);

      $this->loginTestUser();
      $this->client->jsonRequest('PUT', '/api/categories/1', [
        'name' => 'Category 1+',
      ]);
      $contents = json_decode($this->client->getResponse()->getContent(), TRUE);
      $this->assertEquals([
        'id' => 1,
        'name' => 'Category 1+',
      ], $contents);
    }

    /**
     * @covers \App\Controller\CategoriesController::deleteCategory
     */
    public function testDeleteCategory() {
      $this->client->request('DELETE', '/api/categories/1');
      $this->assertResponseStatusCodeSame(401);

      $this->loginTestUser();
      $this->client->request('DELETE', '/api/categories/1');
      $this->assertResponseStatusCodeSame(204);
    }

}
