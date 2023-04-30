<?php

namespace App\Tests\Controller;

use App\DataFixtures\AppFixtures;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ControllerTestBase extends WebTestCase {

  protected AbstractDatabaseTool $databaseTool;

  protected KernelBrowser $client;

  public function setUp(): void {
    parent::setUp();
    $this->client = $this->createClient();
    $this->databaseTool = self::getContainer()
      ->get(DatabaseToolCollection::class)
      ->get();
    $this->databaseTool->loadFixtures([AppFixtures::class]);
  }

  protected function loginTestUser() {
    $encoder = $this->client->getContainer()->get(JWTEncoderInterface::class);
    $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $encoder->encode([
      'username' => 'user',
    ])));
  }

}
