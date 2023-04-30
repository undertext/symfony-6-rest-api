<?php

namespace App\Controller;

use App\DTO\UserCreationDTO;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api')]
class UserCreationController extends AbstractController {

  #[Route('/user/register', name: 'register', methods: ['POST'])]
  #[ParamConverter("userCreationDTO", converter: "fos_rest.request_body")]
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
