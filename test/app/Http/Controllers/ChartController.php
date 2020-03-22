<?php


namespace App\Http\Controllers;

use App\Jobs\ParseFile;
use App\Repositories\CompaniesDepartmentsRepository;
use App\Repositories\UserRepository;
use App\Services\CompaniesDepartmentsDataAnalyseServices;
use Illuminate\Support\Facades\Log;


class ChartController extends Controller
{
    /**
     * @var CompaniesDepartmentsRepository
     */
    public $companiesDepartmentsRepository;

    /**
     * @var CompaniesDepartmentsDataAnalyseServices
     */
    public $companiesDepartmentsDataAnalyseServices;

    public function __construct(
        CompaniesDepartmentsRepository $companiesDepartmentsRepository,
        CompaniesDepartmentsDataAnalyseServices $companiesDepartmentsDataAnalyseServices
    )
    {
        $this->companiesDepartmentsRepository = $companiesDepartmentsRepository;
        $this->companiesDepartmentsDataAnalyseServices = $companiesDepartmentsDataAnalyseServices;
    }

    public function charts()
    {
        $data = $this->companiesDepartmentsRepository->getCompaniesData();
        $data = $this->companiesDepartmentsDataAnalyseServices->prepareChartConfig($data);
        $config = json_encode($data);
        return view('charts', [
                'data' => $data,
                'config' => $config
        ]);
    }


}
