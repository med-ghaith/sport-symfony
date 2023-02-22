<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Payment
{
    private $methode;

    public function getMethode(): ?string
    {
        return $this->methode;
    }

    public function setMethode(?string $methode): self
    {
        $this->methode = $methode;

        return $this;
    }
}
