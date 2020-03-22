<?php
namespace App\Repositories;

use App\Entities\Company;
use App\Entities\Department;
use App\Entities\Staff;
use Illuminate\Support\Facades\Log;

class CompaniesDepartmentsRepository
{

    public $companyDirs = 'companies';

    /**
     * @return array
     */
    public function getCompaniesData(): array
    {
        $companies = $this->getCompanies();
        if (!empty($companies)) {
            foreach ($companies as $key => $company) {
                $companyDepartments = $this->getCompanyDepartmentsData($company->name);
                $company->setDepartments($companyDepartments);
            }
        }
        return $companies;

    }


    /**
     * @return array
     */
    public function getCompanies(): array
    {
        $companiesFolders = $this->getFiles($this->companyDirs);
        foreach ($companiesFolders as $key => $companyFolder) {
            $companies[$key] = new Company();
            $companies[$key]->name = $companyFolder;
        }

        return $companies ?? [];

    }


    /**
     * @param string $companyName
     * @return array
     */
    public function getDepartmentsFiles(string $companyName): array
    {
        return $this->getFiles($this->companyDirs . DIRECTORY_SEPARATOR . $companyName);
    }

    /**
     * @param string $company
     * @return array
     */
    public function getCompanyDepartmentsData(string $companyName): array
    {
        $companyDepartments = $this->getCompanyDepartments($companyName);
        foreach ($companyDepartments as $key => $companyDepartment) {
            $departmentFilePath = $this->companyDirs . DIRECTORY_SEPARATOR . $companyName . DIRECTORY_SEPARATOR . $companyDepartment->name;
            $companyDepartment->name = $this->extractFileName($companyDepartment->name);
            $departmentStaffs = $this->getStaffs($departmentFilePath);
            $companyDepartment->setStaffs($departmentStaffs);
        }
        return $companyDepartments;

    }

    /**
     * @param string $companyName
     * @return array
     */
    public function getCompanyDepartments(string $companyName): array
    {
        $companyDepartmentsFiles = $this->getDepartmentsFiles($companyName);
        foreach ($companyDepartmentsFiles as $key => $companyDepartmentsFile) {
            $departments[$key] = new Department();
            $departments[$key]->name = $companyDepartmentsFile;
        }
        return $departments ?? [];

    }

    /**
     * @param string $departmentFilePath
     * @return array
     */
    public function getStaffs(string $departmentFilePath): array
    {
        $departmentStaffsData = $this->getFilesData($departmentFilePath);
        if(!empty($departmentStaffsData)){
            foreach ($departmentStaffsData as $key => $data) {
                $staffs[$key] = new Staff();
                $staffs[$key]->name = $data['_id'];
                $staffs[$key]->startDate = $data['start'];
                $staffs[$key]->endDate = $data['end'];
            }
        }else{
            Log::channel('charts')->warning($departmentFilePath .' file is empty or not valid ');
        }

        return $staffs ?? [];

    }

    /**
     * @param string $filename
     * @return string
     */
    public function extractFileName(string $filename): string
    {
        $p = strrpos($filename, '.');
        if ($p > 0) {
            return substr($filename, 0, $p);
        } else {
            return $filename;
        }

    }

    /**
     * @param string $path
     * @return array
     */
    public function getFiles(string $path): array
    {
        $data = array_values(array_diff(scandir($this->getFilePath($path)), array('..', '.')));
        return $data ?? [];
    }


    /**
     * @param string $path
     * @return string
     */
    public function getFilePath(string $path): string {
        return storage_path('app/uploads/' . $path );
    }

    /**
     * @param string $path
     * @return array
     */
    public function getFilesData(string $path): array
    {

        if (file_exists($this->getFilePath($path))) {
            $content = file_get_contents($this->getFilePath($path));
            $data = json_decode($content, true);
        }
        return $data ?? [];
    }

}
