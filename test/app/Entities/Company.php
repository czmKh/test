<?php

namespace App\Entities;


class Company
{

    /**
     * @var string
     */
    public string $name;

    /**
     * @var Department[]
     */
    private $departments;


    public function setDepartments(array $departments): void
    {
        $this->departments = $departments;
    }

    public function addDepartment(Department $department): void
    {
        $this->departments[] = $department;
    }


    public function getDepartments(): array {
        return  $this->departments;
    }
}
