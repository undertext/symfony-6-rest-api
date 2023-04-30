<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * User creation DTO.
 */
class UserCreationDTO {

  /**
   * UserCreationDTO constructor.
   *
   * @param string $email
   *   User email.
   * @param string $password
   *   User password.
   */
  public function __construct(#[Assert\Regex('/.+@.+/')] private string $email, private string $password) {
  }

  public function getEmail(): string {
    return $this->email;
  }

  public function getPassword(): string {
    return $this->password;
  }

}
