<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:69:"E:\demo\zqrb-server-php\public/../application/admin\view\no-data.html";i:1538189725;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>首页 Ver2.0</title>

    <script src="__JS__/jquery-2.1.1.min.js"></script>
    <link rel="stylesheet" href="__CSS__/bootstrap.min.css">

    <link rel="stylesheet" href="__CSS__/open-v2.css">


    <link rel="stylesheet" href="__CSS__/common.css">
    <link rel="stylesheet" href="__CSS__/jquery-confirm.min.css">
    <link rel="stylesheet" href="__CSS__/bootstrap-table.css">
    <link rel="stylesheet" href="__CSS__/setting.css">


    <script src="__JS__/jquery-confirm.js"></script>
    <script src="__JS__/jquery-confirm/jquery-confirm.js"></script>
    <script src="__JS__/jquery-confirm/jquery-confirm.min.js"></script>
    <script src="__JS__/bootstrap.min.js"></script>

    <script src="__JS__/common-open-v2.js"></script>
    <script src="__JS__/common.js"></script>
    <script src="__JS__/nav-left/metisMenu.min.js"></script>
    <script src="__JS__/nav-left/sb-admin-2.js"></script>
    <script src="__JS__/jquery.cookie.js"></script>
    <script src="__JS__/bootstrap-table.js"></script>
    <script src="__JS__/bootstrap-table-locale-all.js"></script>
    <script src="__JS__/drag_map.js"></script>


    <!--百度地图-->
    <script type="text/javascript"
            src="https://api.map.baidu.com/api?v=2.0&ak=XtZLUzQfXZH6fbkZRxjCeK9FBppCMsF2&s=1"></script>

    <script src="__JS__/hcharts/highcharts.js"></script>
    <script src="__JS__/hcharts/highcharts-more.js"></script>
    <script src="__JS__/hcharts/solid-gauge.js"></script>
    <script src="__JS__/hcharts/oldie.js"></script>
    <script src="__JS__/hcharts/highcharts-3d.js"></script>
    <script src="__JS__/hcharts/pareto.js"></script>


</head>

<body>


<!--logo-title-->
<div class="row" style="width: 100%">

    <div class="col-md-4 col-lg-4 lost" style="text-align: right">
        <img src="__IMG__/title-left.gif" style="margin-top: 8px">

    </div>

    <div class="col-md-4 col-lg-4">
        <div class="slogan">
            <span>环球港智慧消防</span></div>

    </div>

    <div class="col-md-4 col-lg-4 align-center">
        <img src="__IMG__/title-right.gif" style="margin-top: 8px">

    </div>

</div>

<!--导航栏-->
<div class="row">
    <div class="col-md-2 lost">
        <span class="weather">26℃</span>
        <div class="time">
            <span>当前日期</span>
            <span>星期</span>

        </div>

    </div>

    <div class="col-md-8">
        <div class="slider_nav">
            <ul class="navvv">
                <a href="open-v2.html">
                    <li class="li-click">
                        <div style="background-image: url(__IMG__/icon_home_on_18.png);color: #fff">
                            首页
                        </div>
                    </li>
                </a>

                <li>
                    <a href="water/water1-shuichi.html" class="dropdown-toggle" data-toggle="dropdown"
                       data-hover="dropdown" id="dropdownMenu1">
                        <div style="background-image: url(__IMG__/icon_monitor_off_18.png)">
                            <a href="water/water1-shuichi.html">主机监测</a>
                        </div>
                    </a>
                    <ul class="panel-collapse dropdown-menu" role="menu" aria-labelledby="dropdownMenu1"
                        style="background-color: #2B4274">
                        <a href="water/water1-shuichi.html">
                            <li>火灾报警</li>
                        </a>
                        <a href="water/water2-xiaohuo.html">
                            <li>水泵</li>
                        </a>
                        <a href="water/water3-plguan.html">
                            <li>无线压力</li>
                        </a>

                    </ul>

                </li>

                <a href="#">
                    <li>
                        <div style="background-image: url(__IMG__/icon_firedoor_off_18.png)">
                            防火门
                        </div>
                    </li>
                </a>

                <a href="#">
                    <li>
                        <div style="background-image: url(__IMG__/icon_rolldoor_off_18.png)">
                            卷帘门
                        </div>
                    </li>
                </a>

                <a href="#">
                    <li>
                        <div style="background-image: url(__IMG__/icon_shine_off_18.png)">
                            电气火灾
                        </div>
                    </li>
                </a>

                <a href="#">
                    <li>
                        <div style="background-image: url(__IMG__/icon_yan_off_18.png)">
                            无线烟感
                        </div>
                    </li>
                </a>

                <li>
                    <a class="dropdown-toggle" data-toggle="dropdown">
                        <div style="background-image: url(__IMG__/icon_setting_off_12.png);">
                            <a href="setting/xunjian-person.html">智能维保</a>
                        </div>

                    </a>
                    <ul class="dropdown-menu">
                        <li data-toggle="collapse" data-parent="#accordion"
                            href="#collapseOne" style="background-color: rgba(255,255,255,0.16);">巡检管理
                        </li>

                        <ul id="collapseOne" class="panel-collapse collapse">
                            <a href="setting/xunjian-person.html">
                                <li>巡检人员管理</li>
                            </a>
                            <a href="setting/xunjian-task.html">
                                <li>巡检任务管理</li>
                            </a>
                            <a href="setting/xunjian-search.html">
                                <li>巡检查询管理</li>
                            </a>
                            <a href="setting/xunjian-table.html">
                                <li>巡检工作表单</li>
                            </a>

                        </ul>

                        <li data-toggle="collapse" data-parent="#accordion"
                            href="#collapseTwo" style="background-color: rgba(255,255,255,0.16);margin-top: 1px">维修任务
                        </li>

                        <ul id="collapseTwo" class="panel-collapse collapse">

                            <a href="setting/weixiu-paidan.html">
                                <li>维修派单</li>
                            </a>
                            <a href="setting/weixiu-shenhe.html">
                                <li>维修审核</li>
                            </a>

                        </ul>

                    </ul>
                </li>
            </ul>
        </div>

    </div>

    <div class="col-md-2 logout">
        <div class="slider_nav_login">
            <img src="__IMG__/admin-34.png">
            <span>环球港</span>
            <img src="__IMG__/atn_blue_11.png">
            <img src="__IMG__/ring.png" style="margin-left: 30px">
        </div>

    </div>

</div>


<script>

    $(function () {


    })


</script>


</html>