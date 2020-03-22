@extends('layouts.app')

@section('title-document')Charts @endsection

<div class="container">
    @section('content')

        @foreach($data as $companyName => $companyDepartments)
            <h1>{{$companyName}}</h1>
            <div class="row">
                @foreach($companyDepartments as $companydepKey => $companyDepartment)

                    <div class="col-md-12">
                        <figure class="highcharts-figure">
                            <div id="{{$companyDepartment['container']}}"></div>
                        </figure>
                    </div>

                @endforeach
            </div>
        @endforeach



    @endsection
</div>

@section('script')
    <script>
        drawCharts(<?= $config?>);
    </script>
@endsection
