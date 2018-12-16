$(function () {
    /*水池页面的所有图表*/
   /* pieshui();*/
})


/*左二实时监测*/
function pieshui() {
    var test = [];
    var count = 0;
    $.ajax({
        type: 'post',
        url: 'https://fire.zt-ioe.com/jdxf/mergin_test/api/webMainQuery.php?type=2',
        dataType: 'json',
        success: function (data) {
            if (data.code == '200') {
                $.each(data.data, function (i, d) {
                    var a1 = {
                        name: "正常(" + d.normalcnt + "栋 " + (d.rnormal * 100).toFixed(2) + "%)",
                        y: parseInt(d.rnormal)
                    }
                    test.push(a1)
                    var a2 = {name: "故障(" + d.gzcnt + "栋 " + (d.rgz * 100).toFixed(2) + "%)", y: parseInt(d.rgz)}
                    test.push(a2)
                    var a3 = {name: "报警(" + d.bjcnt + "栋 " + (d.rbj * 100).toFixed(2) + "%)", y: parseInt(d.rbj)}
                    test.push(a3)
                    var a4 = {
                        name: "故障+报警(" + d.bjgzcnt + "栋 " + (d.rbjgz * 100).toFixed(2) + "%)",
                        y: parseInt(d.rbjgz)
                    }
                    test.push(a4)
                    count = d.bjcnt;

                });
                console.log("打印数组===" + test + "总数====" + count)

                /*第二个饼图*/
                $("#pieshui").highcharts({
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: 'pie',
                        marginLeft: 250
                    },
                    colors: [
                        '#E34947',
                        '#56C2DE',
                        '#76C4A8',
                        '#EEAB52'
                    ],
                    title: {
                        text: '警告总数<br>' + count,
                        align: 'center',
                        verticalAlign: 'middle',
                        y: 0,
                        x: 120,
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
                        align: 'left',
                        x: 50,
                        verticalAlign: 'left',
                        layout: 'vertical',
                        y: 0,
                        itemMarginTop: 45,
                    },
                    series: [{
                        type: 'pie',
                        name: '占比',
                        innerSize: '50%',
                        data: test
                    }]

                });
            } else {
                tipsZt("检查网络设置==========>");
            }
        }, error: function () {

        }
    });
    console.log("打印数组===" + test + "总数====" + count)


}



