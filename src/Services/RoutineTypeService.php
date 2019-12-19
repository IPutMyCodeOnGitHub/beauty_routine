<?php

namespace App\Services;

use App\Entity\RoutineType;
use Doctrine\ORM\EntityManagerInterface;

class RoutineTypeService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getType($id): ?RoutineType
    {
        return $this->em->getRepository(RoutineType::class)->find($id);
    }

}