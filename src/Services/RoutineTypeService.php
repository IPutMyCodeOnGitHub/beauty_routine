<?php

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;

class RoutineTypeService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function createType()
    {

    }

}