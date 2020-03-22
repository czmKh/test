<?php
namespace App\Services;

use App\Entities\Department;
use App\Entities\Staff;
use Illuminate\Support\Facades\Log;

class CompaniesDepartmentsDataAnalyseServices
{

    /**
     * @param array $companyData
     * @return array
     */
    public function prepareChartConfig(array $companyData):array
    {
        $config = [];
        foreach ($companyData as $company) {

            foreach ($company->getDepartments() as $key => $department) {
                $calculateDepartmentData = $this->calculateDepartmentData($department);
                if(!empty($calculateDepartmentData)){
                    $config[$company->name][$department->name]['data'] = $calculateDepartmentData;
                    $config[$company->name][$department->name]['container'] = $this->createContainerName($company->name, $department->name);
                }

            }
        }
        return $config;
    }


    /**
     * @param string $companyName
     * @param string $departmentName
     * @return string
     */
    public function createContainerName(string $companyName, string $departmentName):string
    {
        return strtolower(str_replace(' ', '', $companyName)) . '_' . strtolower(str_replace(' ', '', $departmentName));
    }

    /**
     * @param Department $department
     * @return array
     */
    public function calculateDepartmentData(Department $department): array
    {
        $calculatedDepartments = $this->calculatedDepartmentsInterval($department);
        if(!empty($calculatedDepartments)){
            $dataForChart = $this->prepearDataForChart($calculatedDepartments);
        }
        return $dataForChart ?? [];
    }


    /**
     * @param array $calculatedDepartments
     * @return array
     */
    public function prepearDataForChart(array $calculatedDepartments): array
    {
        foreach ($calculatedDepartments as $key => $department) {
            $data[$key]['name'] = $this->prepareName($department);
            $data[$key]['y'] = $department['variantPercentage'];
            $data[$key]['count'] = $department['variantQuantity'];
        }

        return $data ?? [];
    }

    /**
     * @param array $department
     * @return string
     */
    public function prepareName(array $department): string
    {
        $name = 'From ' . $this->convertMonth($department['from']) . ' To ' . $this->convertMonth($department['to']);
        return $name;
    }

    /**
     * @param int $month
     * @return string
     */
    public function convertMonth(int $month): string
    {
        $years = intval($month/12);
        $months = $month - ($years * 12);

        if($years > 1){
            $years = $years . ' Years';
        }elseif ($years == 1){
            $years = $years . ' Year';
        }else{
            $years = '';
        }

        $months = ' ' . $months . ' Month';
        if ($months > 1) {
            $months = $months . 's';
        }

        $display = $years. '' . $months . '<br>';
        return $display;

    }

    /**
     * @param Department $department
     * @return array
     */
    public function getMembersDateRanges(Department $department): array
    {
        foreach ($department->getStaffs() as $stafKey => $staff) {
            $range = $this->calculateDateRange($staff,$department->name);
            if(!is_null($range)){
                $membersDateRanges[] = $range;

            }
        }

        return $membersDateRanges ?? [];
    }

    /**
     * @param Staff $staff
     * @return int
     * @throws \Exception
     */
    public function calculateDateRange(Staff $staff,string $departmentName): ?int
    {
        try{
            $end = new \DateTime($staff->endDate);
            $end->modify('+1 day');
            $start = new \DateTime($staff->startDate);
            $interval = $end->diff($start);
            return $interval->m + ($interval->y * 12);
        }catch (\Throwable $e){
            Log::channel('charts')->warning($departmentName .' '. $e->getMessage());
            return null;
        }



    }


    /**
     * @param Department $department
     * @return array
     */
    public function calculatedDepartmentsInterval(Department $department): array
    {
        $membersDateRanges = $this->getMembersDateRanges($department);
        if(!empty($membersDateRanges)){
            $max = max($membersDateRanges);
            $min = min($membersDateRanges);
            $staffTotalCount = count($membersDateRanges);
            $variationRange = $max - $min;
            $optimalIntervalQuantity = $this->optimalIntervalQuantity($staffTotalCount);
            $intervalLength = $this->intervalLength($variationRange,$optimalIntervalQuantity);
            $arrayRange = [];
            $startData = $min ;
            for ($i = 0; $i < $optimalIntervalQuantity; $i++) {
                $arrayRange[$i]['from'] = $startData;
                $arrayRange[$i]['to'] = $startData + $intervalLength;
                $arrayRange[$i]['variantQuantity'] = $this->getVariantsQuanity($membersDateRanges, $arrayRange[$i]['from'], $arrayRange[$i]['to']);
                $arrayRange[$i]['variantPercentage'] = $this->getPercentage($staffTotalCount, $arrayRange[$i]['variantQuantity']);

                $startData = $arrayRange[$i]['to'];
            }
        }

        return $arrayRange ?? [];

    }

    public function intervalLength($variationRange,$optimalIntervalQuantity){
        $intervalLength = ceil($variationRange / $optimalIntervalQuantity);
        if($intervalLength < 1){
            $intervalLength = 1;
        }

        return $intervalLength;
    }

    public function optimalIntervalQuantity($staffTotalCount){
        $optimalIntervalQuantity = floor(1 + 3.222 * log($staffTotalCount, 10));
        if($optimalIntervalQuantity <= 1){
            $optimalIntervalQuantity = 2;
        }

        return $optimalIntervalQuantity;
    }


    /**
     * @param int $staffTotalCount
     * @param int $variantQuantity
     * @return int
     */
    public function getPercentage(int $staffTotalCount, int $variantQuantity): int
    {
        return ($variantQuantity * 100) / $staffTotalCount;
    }

    /**
     * @param array $membersDateRanges
     * @param int $from
     * @param int $to
     * @return int
     */
    public function getVariantsQuanity( array $membersDateRanges, int $from, int $to): int
    {
        $variantsCount = 0;

        foreach ($membersDateRanges as $data) {
            if ($data >= $from && $data < $to) {
                $variantsCount++;
            }
        }

        return $variantsCount;

    }


}












