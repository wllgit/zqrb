$(function () {
    /*切换显示*/
    radioChange();
    /*火警信息页面的所有图表*/
    charts();
})

function radioChange() {
    console.log("radioChange======================>");
    $("input[name='timeRadio']").change(function () {
        $("input[name='timeRadio']").each(function () {
            if (this.checked){
                $(this).parent().addClass("radio-checked");
            } else {
                $(this).parent().removeClass("radio-checked");

            }

        })

    })

}

/*火警信息页面的所有图表*/
function charts() {
    /*第一个环形图*/
    $("#pie-huojing").highcharts({
        chart: {
            type: 'solidgauge',
            marginTop: 50
        },

        title: {
            text: '125',
            align: 'center',
            verticalAlign: 'middle',
            y: 35,
            x:-5,
            style: {
                fontSize: '35px',
                color:'#8B8989'
            }
        },

        pane: {
            startAngle: 5,
            endAngle: 260,
            background: [{ // Track for Move
                outerRadius: '119%',
                innerRadius: '82%',
                backgroundColor: '#FFB2B2',
                //Highcharts.Color(Highcharts.getOptions().colors[1]).setOpacity(0.3).get(),
                borderWidth: 0
            }]
        },

        yAxis: {
            min: 0,
            max: 100,
            lineWidth: 0,
            tickPositions: []
        },

        plotOptions: {
            solidgauge: {
                borderWidth: '24px',
                dataLabels: {
                    enabled: false
                },
                linecap: 'round',
                stickyTracking: false
            }
        },

        series: [{
            name: 'Move',
            // borderColor: Highcharts.getOptions().colors[1],
            borderColor:'#e34947',
            data: [{
                color: Highcharts.getOptions().colors[0],
                // color:'yellow',
                radius: '100%',
                innerRadius: '100%',
                y: 80
            }]
        }]

    });

    /*第二个饼图*/
    $("#pie-huojing2").highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        colors: [
            '#E34947',
            '#56C2DE',
            '#76C4A8',
            '#EEAB52'

        ],
        title: {
            text: ''
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: false
                },
                showInLegend: true
            }
        },
        legend: {
            align: 'center',
            x: 130,
            verticalAlign: 'right',
            layout: 'vertical',
            y: 20,
        },
        series: [{
            minPointSize: 10,
            zMin: 0,
            name: 'countries',
            data: [{
                name: '真实火警',
                y: 25,
                sliced: true,
                selected: true
            }, {
                name: '预报火警',
                y: 45,
            }, {
                name: '测试火警',
                y: 25,
            }, {
                name: '手动复位',
                y: 25,

            }]
        }]

    });

    /*第三个饼图*/
    $("#pie-huojing3").highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        colors: [
            '#76C5A8',
            '#EEAB52'
        ],
        title: {
            text: ''
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: false
                },
                showInLegend: true
            }
        },
        legend: {
            align: 'center',
            x: 130,
            verticalAlign: 'right',
            layout: 'vertical',
            y: 20,
        },
        series: [{
            minPointSize: 10,
            zMin: 0,
            name: 'countries',
            data: [{
                name: '已处理',
                y: 35,
                sliced: true,
                selected: true
            }, {
                name: '未处理',
                y: 65,

            }]
        }]

    });

    /*3d*/
    $("#hj-3d").highcharts({
        chart: {
            type: 'column',
            margin: 35,
            options3d: {
                enabled: true,
                alpha: 10,
                beta: 25,
                depth: 70,
                viewDistance: 100,      // 视图距离，它对于计算角度影响在柱图和散列图非常重要。此值不能用于3D的饼图
                frame: {                // Frame框架，3D图包含柱的面板，我们以X ,Y，Z的坐标系来理解，X轴与 Z轴所形成
                    // 的面为bottom，Y轴与Z轴所形成的面为side，X轴与Y轴所形成的面为back，bottom、
                    // side、back的属性一样，其中size为感官理解的厚度，color为面板颜色
                    bottom: {
                        size: 10
                    },
                    side: {
                        size: 1,
                        color: 'transparent'
                    },
                    back: {
                        size: 1,
                        color: 'transparent'
                    }
                }
            },
        },
        colors: [
            '#56C2DE',
            '#e34947'
        ],
        legend: {
            align: 'center',
            x: 200,
            verticalAlign: 'right',
            y: 10,
        },
        title: {
            text: ''
        },
        subtitle: {
            text: ''
        },
        plotOptions: {
            column: {
                depth: 25
            }
        },
        xAxis: {
            categories: Highcharts.getOptions().lang.shortMonths
        },
        yAxis: [{
            title: {
                text: ''
            }
        }, {
            title: {
                text: ''
            },
            minPadding: 0,
            maxPadding: 0,
            max: 100,
            min: 0,
            opposite: true,
        }],
        series: [{
            type: 'pareto',
            name: '发生火灾的企业数',
            yAxis: 1,
            zIndex: 10,
            baseSeries: 1,

        },{
            name: '火警数',
            data: [2, 3, null, 4, 0, 5, 1, 4, 6, 3]
        }]

    });


}
