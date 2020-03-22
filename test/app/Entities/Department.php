<?php

namespace App\Entities;

class Department
{
    /**
     * @var string
     */
    public string $name;

    /**
     * @var Staff[]
     */
    private $staffs;


    public function setStaffs(array $staffs): void
    {
        $this->staffs = $staffs;
    }

    public function addStaff(Staff $staff): void
    {
        $this->staffs[] = $department;
    }


    public function getStaffs(): array {
        return  $this->staffs;
    }
}
