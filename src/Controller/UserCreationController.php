<?php

namespace App\Controller;

use App\DTO\UserCreationDTO;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;

#[Route('/api')]
#[Security(name: null)]
#[OA\Tag(name: 'User login')]
class UserCreationController extends AbstractController {

  #[Route('/user/register', name: 'register', methods: ['POST'])]
  #[ParamConverter("userCreationDTO", converter: "fos_rest.request_body")]
  #[OA\RequestBody(
    description: 'New user credentials',
    required: true,
    content: new Model(type: UserCreationDTO::class))]
  #[OA\Response(
    response: 200,
    description: 'Successful response',
    content: new Model(type: User::class)
  )]
  public function registerUser(UserCreationDTO $userCreationDTO, ManagerRegistry $doctrine, ValidatorInterface $validator, UserPasswordHasherInterface $passwordHasher) {
    if (empty($errors = $validator->validate($userCreationDTO))) {
      return $this->json($errors, 400);
    }
    $user = new User();
    $user->setEmail($userCreationDTO->getEmail());
    $password = $passwordHasher->hashPassword($user, $userCreationDTO->getPassword());
    $user->setPassword($password);
    $doctrine->getManager()->persist($user);
    $doctrine->getManager()->flush();

    return $this->json($user);
  }

}
