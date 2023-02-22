<?php

namespace App\Entity;

use App\Repository\GymMangerRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GymMangerRepository::class)
 */
class GymManger extends User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
//
//    public function getId(): ?int
//    {
//        return $this->id;
//    }
}
