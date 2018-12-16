/*index页面*/
$(function () {

    cnt()
    dianwei()
    pie();
    zhu();
    time();
  /*  map(121.4191389701, 31.2394490211)*/
    /* setInterval("f5()", 300000);*/

})

function f5() {
    window.location.reload();
}

/*上方豆腐块数据*/
function cnt() {
    var date = new Date();
    var seperator1 = "-";
    var month = date.getMonth() + 1;
    var strDate = date.getDate();
    if (month >= 1 && month <= 9) {
        month = "0" + month;
    }
    if (strDate >= 0 && strDate <= 9) {
        strDate = "0" + strDate;
    }
    var currentdate = date.getFullYear() + seperator1 + month + seperator1 + strDate;

    $.ajax({
        type: 'post',
        url: "https://fire.zt-ioe.com/jdxf/mergin_test/api/mobileQuery.php?type=1&queryDatetime=" + currentdate,
        dataType: 'json',
        success: function (data) {
            $.each(data.data, function (idx, obj) {
                $("#weishangbao").text(obj.uncommit_count)
            })
        }, error: function () {
        }
    });

    $.ajax({
        type: 'post',
        url: "https://fire.zt-ioe.com/jdxf/mergin_test/api/mobileQuery.php?type=3&queryDatetime=" + currentdate,
        dataType: 'json',
        success: function (data) {
            $.each(data.data, function (idx, obj) {
                var unfeed_count = 0;
                if (obj.unfeed_count == null) {
                    unfeed_count == 0
                } else {
                    unfeed_count = obj.unfeed_count
                }
                $("#weifankui").text(unfeed_count)
            })
        }, error: function () {
        }
    });

}

function time() {
    var date = new Date();
    var seperator1 = "/";
    var month = date.getMonth() + 1;
    var strDate = date.getDate();
    if (month >= 1 && month <= 9) {
        month = "0" + month;
    }
    if (strDate >= 0 && strDate <= 9) {
        strDate = "0" + strDate;
    }
    var currentdate = date.getFullYear() + seperator1 + month + seperator1 + strDate;

    this.hour = date.getHours() < 10 ? "0" + date.getHours() : date.getHours();
    this.minute = date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes();
    this.second = date.getSeconds() < 10 ? "0" + date.getSeconds() : date.getSeconds();

    var querytime = this.hour + ":" + this.minute + ":" + this.second
    var d2 = new Date(currentdate + " " + querytime)

    var t = "2018-09-06 10:01:10"
    var d1 = new Date(t.replace(/\-/g, "/"))
    var kk = (date - d1) / 1000

    if (kk < 300) {


        console.log("当前时间秒差-----" + kk)
    }


}

/*添加地图*/
function map(a,b) {
    // 百度地图API功能
    var map = new BMap.Map("map-sh");  // 创建Map实例

    var point = new BMap.Point(a,b); // 创建点坐标
/*    map.setCurrentCity("上海市")*/
    map.centerAndZoom(point, 17);      // 初始化地图,用城市名设置地图中心点
    map.enableScrollWheelZoom();   //启用滚轮放大缩小，默认禁用
    map.enableContinuousZoom();    //启用地图惯性拖拽，默认禁用


    var marker = new BMap.Marker(point);
    map.addOverlay(marker);
    /* var tips = '<div class="alert alert-warning" style="width: 200px"><a href="#" class="close" data-dismiss="alert">&times;</a><p>' + title + '</p><p>' + content + '</p></div>';*/
    /*marker.setDiv(tips);*/

    var top_left_control = new BMap.ScaleControl({anchor: BMAP_ANCHOR_TOP_LEFT});// 左上角，添加比例尺
    var top_left_navigation = new BMap.NavigationControl();  //左上角，添加默认缩放平移控件
    var top_right_navigation = new BMap.NavigationControl({
        anchor: BMAP_ANCHOR_TOP_RIGHT,
        type: BMAP_NAVIGATION_CONTROL_SMALL
    }); //右上角，仅包含平移和缩放按钮
    map.addControl(top_left_control);
    map.addControl(top_left_navigation);
    map.addControl(top_right_navigation);

    /* var sContent = "环球港<br />"
     var infoWindow = new BMap.InfoWindow(sContent);  // 创建信息窗口对象
     map.openInfoWindow(infoWindow,point); //开启信息窗口
     map.enableScrollWheelZoom();//启动鼠标滚轮缩放地图
     document.getElementById("r-result").innerHTML = "信息窗口的内容是：<br />" + infoWindow.getContent();*/
}

/*左上角点位监测*/
function dianwei() {
    var huojingArr = [];
    var guzhangArr = [];
    $.ajax({
        type: 'post',
        url: 'https://fire.zt-ioe.com/jdxf/mergin_test/api/webMainQuery.php?type=1',
        dataType: 'json',
        success: function (data) {
            if (data.code == '200') {
                var countbj = 0;
                var countgz = 0;
                $.each(data.data, function (i, d) {
                    huojingArr.push(parseInt(d.bjcnt))
                    guzhangArr.push(parseInt(d.gzcnt))
                    countbj = countbj + parseInt(d.bjcnt);
                    countgz = countgz + parseInt(d.gzcnt);
                });

                /*设置数值*/
                $("#baojingcnt").text("0")
                $("#guzhangcnt").text("0")

                console.log("countbj" + countbj + "countgz" + countgz)
                var chart = Highcharts.chart('container', {
                    chart: {
                        type: 'areaspline'
                    },
                    title: {
                        text: ''
                    },
                    colors: [
                        '#F17976',
                        '#EB7B27',
                    ],
                    legend: {
                        align: 'center',
                        x: 0,
                        verticalAlign: 'bottom',
                        layout: 'horizontal',
                        y: 20,

                    },
                    xAxis: {
                        categories: ['0:00', '1:00', '2:00', '3:00', '4:00', '5:00', '6:00', '7:00', '8:00', '9:00', '10:00',
                            '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00', '23:00'],
                    },
                    yAxis: {
                        title: {
                            text: ''
                        }
                    },
                    tooltip: {
                        shared: true,
                        valueSuffix: '单位'
                    },
                    plotOptions: {
                        areaspline: {
                            fillOpacity: 0.5
                        }
                    },
                    series: [{
                        name: '报警',
                        data: huojingArr
                    }, {
                        name: '故障',
                        data: guzhangArr
                    }],

                });

            } else {
                tipsZt("检查网络设置======kkk=====>");
            }
        }, error: function () {

        }
    });


}

/*左二实时监测*/
function pie() {
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
                    count = d.buildcnt;

                });
                console.log("打印数组===" + test + "总数====" + count)

                /*第二个饼图*/
                $("#pieleft").highcharts({
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: 'pie',
                        marginLeft: 180
                    },
                    colors: [
                        '#E34947',
                        '#EB7B27',
                        '#81D09B',
                        '#709CD9'
                    ],
                    title: {
                        text: '总栋数<br>' + count,
                        align: 'center',
                        verticalAlign: 'middle',
                        y: 0,
                        x: 82,
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
                        x: 0,
                        verticalAlign: 'left',
                        layout: 'vertical',
                        y: 0,
                        itemMarginTop: 25,
                        symbolWidth: 20
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

/*右上角柱状图*/
function zhu() {
    var baojingZhu = [];
    var guazhangZhu = [];
    $.ajax({
        type: 'post',
        url: 'https://fire.zt-ioe.com/jdxf/mergin_test/api/webMainQuery.php?type=3',
        dataType: 'json',
        success: function (data) {
            if (data.code == '200') {
                $.each(data.data, function (i, d) {
                    baojingZhu.push(parseInt(d.bjcnt))
                    guazhangZhu.push(parseInt(d.gzcnt))
                });
                console.log("baojingZhu" + baojingZhu)

                var chart = Highcharts.chart('zhuright', {
                    chart: {
                        type: 'column'
                    },
                    colors: [
                        '#F17976',
                        '#EB7B27',
                    ],
                    title: {
                        text: ''
                    },
                    xAxis: {
                        categories: ['昨天', '今天', '上月今天']
                    },
                    yAxis: {
                        allowDecimals: false,
                        min: 0,
                        title: {
                            text: ''
                        }
                    },
                    tooltip: {
                        formatter: function () {
                            return '<b>' + this.x + '</b><br/>' +
                                this.series.name + ': ' + this.y + '<br/>';
                        }
                    },
                    plotOptions: {
                        column: {
                            stacking: 'normal'
                        }
                    },
                    series: [{
                        name: '报警',
                        data: guazhangZhu,
                        stack: 'baojing'
                    }, {
                        name: '故障',
                        data: baojingZhu,
                        stack: 'guzhang' // stack 值相同的为同一组
                    }]
                });


            } else {
                tipsZt("检查网络设置==========>");
            }
        }, error: function () {

        }
    });

    console.log("baojingZhu=====2=====" + baojingZhu)


}


