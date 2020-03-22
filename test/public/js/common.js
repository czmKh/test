Highcharts.setOptions({
    colors: Highcharts.map(Highcharts.getOptions().colors, function (color) {
        return {
            radialGradient: {
                cx: 0.5,
                cy: 0.3,
                r: 0.7
            },
            stops: [
                [0, color],
                [1, Highcharts.color(color).brighten(-0.3).get('rgb')] // darken
            ]
        };
    })
});


function drawCharts(config){
    for (var company in config) {
        for (var department in config[company]) {
            drawChart(config[company][department]['container'],department,config[company][department]['data'])
        }
    }
}

function drawChart(container,name,data){
    Highcharts.chart(container, {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: name
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.count}</b>'
        },
        accessibility: {
            point: {
                valueSuffix: '%'
            }
        },
        exporting: { enabled: false },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<span>{point.name}<br> {point.percentage:.1f} %</span>',
                    connectorColor: 'silver',
                    style:{
                        fontSize:'13px',
                        fontWeight:'normal',
                        textOutline:'1px contrast',
                    }
                }
            }
        },
        series: [{
            name: 'Employees',
            data: data,
        }]
    });
}



