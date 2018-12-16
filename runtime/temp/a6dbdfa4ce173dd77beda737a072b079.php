<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:79:"E:\demo\zqrb-server-php\public/../application/admin\view\banner\bannerlist.html";i:1535952502;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/__BOOT__/css/bootstrap.min.css" rel="stylesheet">
    <link href="/__BOOT__/css/bootstrap-reset.css" rel="stylesheet">
    <link href="/__BOOT__/assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link rel="stylesheet" href="/__BOOT__/assets/data-tables/DT_bootstrap.css" />
    <link href="/__BOOT__/css/style.css" rel="stylesheet">
    <link href="/__BOOT__/css/style-responsive.css" rel="stylesheet" />
    <script src="/__BOOT__/js/html5shiv.js"></script>
    <script src="/__BOOT__/js/respond.min.js"></script>
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
                                    BANNER列表
                                </header>
                                <br>
                                <button id="editbut" class="btn green">
                                    <a href="<?php echo url('admin/banner/bannerAdd'); ?>">重置BANNER</a><i class="fa fa-plus"></i>
                                </button>
                            </div>
                            <div class="btn-group pull-right">
                                <button class="btn dropdown-toggle" data-toggle="dropdown">Tools <i class="fa fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu pull-right">
                                    <li><a href="#">Export to Excel</a></li>
                                </ul>
                            </div>
                        </div>
                        <!--<div class="space15"></div>-->
                        <table class="table table-striped table-hover table-bordered" id="editable-sample">
                            <thead>
                            <tr>
                                <th>序号</th>
                                <th>位置</th>
                                <th>标题</th>
                                <th>新闻/广告</th>
                                <th>发布时间</th>
                                <th>发布人</th>
                                <th id="self_do">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(!empty($list)): if(is_array($list) || $list instanceof \think\Collection): if( count($list)==0 ) : echo "" ;else: foreach($list as $key=>$vo): ?>
                                    <tr class="">
                                        <td><?php echo $key+1; ?></td>
                                        <td><?php echo $vo['order']; ?></td>
                                        <td><?php echo $vo['title']; ?></td>
                                        <td><?php echo $vo['position']; ?></td>
                                        <td><?php echo $vo['create_time']; ?></td>
                                        <td><?php echo $vo['author']; ?></td>
                                        <td class="custom_cls">
                                            <div class="pull-right hidden-phone">
                                                <button class="btn btn-primary btn-xs edit"><a href="<?php echo $vo['operate']['编辑']; ?>">编辑</a></button>
                                                <button class="btn btn-danger btn-xs del"><a href="<?php echo $vo['operate']['删除']; ?>">删除</a></button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; endif; else: echo "" ;endif; endif; ?>

                            </tbody>
                        </table>
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

<!-- js placed at the end of the document so the pages load faster -->
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
<!--layer弹层-->
<script src="__JS__/plugins/layer/laydate/laydate.js"></script>
<script src="__JS__/plugins/sweetalert/sweetalert.min.js"></script>
<script src="__JS__/plugins/layer/layer.min.js"></script>


<script>

    jQuery(document).ready(function() {
        EditableTable.init();
    });


    $(function() {
        $(".medium").attr("placeholder","搜索");
    });


    //编辑banner
    //删除banner
    function del(id,name){
//        alert('删除');
        var url = "./bannerDel";
        $.ajax({
            type:"POST",
            url:url,
            data:{'id' : id, 'name' : name},
            async: false,
            beforeSend:function(){
                load = layer.load(0, {shade: false}); //0代表加载的风格，支持0-2
            },
            error: function(request) {
                layer.close(load);
                swal("网络错误!", "", "error");
            },
            success: function(data) {
                //关闭加载层
                layer.close(load);
                if(data.code == 1){
                    layer.msg(data.msg, {time : 1000});
                    setTimeout("javascript:location.href='./bannerList'", 1000);
//                    swal(data.msg, "", "error");
                }else{
                    layer.msg(data.msg, {time : 1000});
//                    swal(data.msg, "", "error");
                }

            }
        });

        return false;
    }




</script>


</body>
</html>