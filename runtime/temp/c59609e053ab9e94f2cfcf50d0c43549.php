<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:83:"D:\demo\zq\zqrb-server-php\public/../application/admin\view\columns\columnlist.html";i:1535952501;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="img/favicon.png">
    <link href="/__BOOT__/css/bootstrap.min.css" rel="stylesheet">
    <link href="/__BOOT__/css/bootstrap-reset.css" rel="stylesheet">
    <link href="/__BOOT__/assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link rel="stylesheet" href="/__BOOT__/assets/data-tables/DT_bootstrap.css" />
    <link href="/__BOOT__/css/style.css" rel="stylesheet">
    <link href="/__BOOT__/css/style-responsive.css" rel="stylesheet" />
    <!--[if lt IE 9]>
    <script src="/__BOOT__/js/html5shiv.js"></script>
    <script src="/__BOOT__/js/respond.min.js"></script>
    <![endif]-->
    <!--自定义样式-->
    <style>
        #main-content{margin-left:0px;}
        .wrapper{margin-top:0px;}
        .custom_cls{width: 120px;}
        .pull-right{width: 100px;}
        #self_do{text-align: center;}
    </style>
</head>
<body>
<section id="container" class="">
    <section id="main-content">
        <section class="wrapper site-min-height">
            <section class="panel">
                <div class="panel-body">
                    <div class="adv-table editable-table ">
                        <div class="clearfix">
                            <div class="btn-group">
                                <header class="panel-heading">
                                    栏目列表管理
                                </header>
                                <br>
                                <button id="editbut" class="btn green">
                                    <a href="<?php echo url('admin/columns/docolumn'); ?>">添加</a><i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="btn-group pull-right">
                            <form role="form" id="comForm" action="/admin/Columns/columnlist" method="get">
                                <div style="width: 300px" role="form" class="form-inline pull-right">
                                    <div class="content clearfix m-b">
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="title" name="title" placeholder="栏目" value="<?php echo isset($title) ? $title :  ''; ?>">
                                        </div>
                                        <button class="btn button_btn bg-deep-blue " onclick="this.form.submit()" type="button">搜索</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!--<div class="space15">123</div>-->
                        <table class="table table-striped table-hover table-bordered">
                            <thead>
                            <tr>
                                <th>序号</th>
                                <th>栏目级别</th>
                                <th>栏目</th>
                                <th>创建时间</th>
                                <th>创建人/修改人</th>
                                <th id="self_do">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(!empty($list)): if(is_array($list) || $list instanceof \think\Collection): if( count($list)==0 ) : echo "" ;else: foreach($list as $key=>$vo): ?>
                            <tr class="">
                                <td><?php echo $key+1; ?></td>
                                <td><?php echo $vo['column_top']; ?></td>
                                <td><?php echo $vo['title']; ?></td>
                                <td class="center"><?php echo $vo['create_time']; ?></td>
                                <td><a class="delete" href="javascript:;"><?php echo $vo['name']; ?></a></td>
                                <td class="custom_cls">
                                    <div class="pull-right hidden-phone">
                                        <button class="btn btn-primary btn-xs edit"><a href="<?php echo $vo['operate']['编辑']; ?>">编辑</a></button>
                                        <?php if(($vo['title'] != '新闻') AND ($vo['title'] != '热点') AND ($vo['title'] != '快讯') AND ($vo['title'] != '深度')): ?>
                                        <button class="btn btn-danger btn-xs del"><a href="<?php echo $vo['operate']['删除']; ?>">删除</a></button>
                                        <?php else: ?>
                                        <button class="btn <?php if(($vo['show'] == '显示')): ?>btn-success btn-xs edit<?php else: ?>btn-danger btn-xs del<?php endif; ?>"><a href="<?php echo $vo['url']; ?>"><?php echo $vo['show']; ?></a></button>
                                        <?php endif; ?>

                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; endif; else: echo "" ;endif; endif; ?>
                            </tbody>
                        </table>
                        <?php echo $list->render(); ?>
                    </div>
                </div>
            </section>
        </section>
    </section>
    <footer class="site-footer">
        <div class="text-center">
            2013 &copy; FlatLab by VectorLab.
            <a href="#" class="go-top">
                <i class="fa fa-angle-up"></i>
            </a>
        </div>
    </footer>
</section>
<script src="/__BOOT__/js/jquery-1.8.3.min.js"></script>
<script src="/__BOOT__/js/bootstrap.min.js"></script>
<script class="include" type="text/javascript" src="/__BOOT__/js/jquery.dcjqaccordion.2.7.js"></script>
<script src="/__BOOT__/js/jquery.scrollTo.min.js"></script>
<script src="/__BOOT__/js/jquery.nicescroll.js" type="text/javascript"></script>
<script type="text/javascript" src="/__BOOT__/assets/data-tables/jquery.dataTables.js"></script>
<script type="text/javascript" src="/__BOOT__/assets/data-tables/DT_bootstrap.js"></script>
<script src="/__BOOT__/js/respond.min.js" ></script>
<script src="/__BOOT__/js/common-scripts.js"></script>
<script src="/__BOOT__/js/editable-table.js"></script>
<!--layer插件-->
<script src="__JS__/plugins/layer/laydate/laydate.js"></script>
<script src="__JS__/plugins/suggest/bootstrap-suggest.min.js"></script>
<script src="__JS__/plugins/layer/layer.min.js"></script>
<script>

    jQuery(document).ready(function() {
        EditableTable.init();
    });
    $(function() {
        $(".medium").attr("placeholder","搜索");
    });
    //删除新闻草稿箱
    function delcolumn(id){
        layer.confirm("确认要删除吗", { title: "确认" }, function (index) {
            layer.close(index);
            var jz;
            var url = "./delColumn";
            $.ajax({
                type:"POST",
                url:url,
                data:{'id' : id},
                async: false,
                beforeSend:function(){
                    jz = layer.load(0, {shade: false}); //0代表加载的风格，支持0-2
                },
                error: function(request) {
                    layer.close(jz);
                    swal("网络错误!", "", "error");
                },
                success: function(data) {
                    //关闭加载层
                    layer.close(jz);
                    if(data.code == 1){
                        layer.msg(data.msg, {time : 1000});
                        setTimeout("javascript:location.href='./columnList'", 1000);
                    }else{
                        console.log(data.err_msg);
                        layer.msg(data.msg, {time : 1000});
                    }
                }
            })
            return false;
        });
    }


    //隐藏
    function hideColumn(id){
        layer.confirm("确认要隐藏吗", { title: "确认" }, function (index) {
            layer.close(index);
            var jz;
            var url = "./hideColumn";
            $.ajax({
                type:"POST",
                url:url,
                data:{'id' : id},
                async: false,
                beforeSend:function(){
                    jz = layer.load(0, {shade: false}); //0代表加载的风格，支持0-2
                },
                error: function(request) {
                    layer.close(jz);
                    swal("网络错误!", "", "error");
                },
                success: function(data) {
                    //关闭加载层
                    layer.close(jz);
                    if(data.code == 1){
                        layer.msg(data.msg, {time : 1000});
                        setTimeout("javascript:location.href='./columnList'", 1000);
                    }else{
                        console.log(data.err_msg);
                        layer.msg(data.msg, {time : 1000});
                    }
                }
            })
            return false;
        });
    }

    //显示
    function showColumn(id){
        layer.confirm("确认要显示吗", { title: "确认" }, function (index) {
            layer.close(index);
            var jz;
            var url = "./showColumn";
            $.ajax({
                type:"POST",
                url:url,
                data:{'id' : id},
                async: false,
                beforeSend:function(){
                    jz = layer.load(0, {shade: false}); //0代表加载的风格，支持0-2
                },
                error: function(request) {
                    layer.close(jz);
                    swal("网络错误!", "", "error");
                },
                success: function(data) {
                    //关闭加载层
                    layer.close(jz);
                    if(data.code == 1){
                        layer.msg(data.msg, {time : 1000});
                        setTimeout("javascript:location.href='./columnList'", 1000);
                    }else{
                        console.log(data.err_msg);
                        layer.msg(data.msg, {time : 1000});
                    }
                }
            })
            return false;
        });
    }
</script>
</body>
</html>
