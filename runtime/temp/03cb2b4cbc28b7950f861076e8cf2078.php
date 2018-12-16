<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:81:"E:\demo\zqrb-server-php\public/../application/admin\view\advermanage\advlist.html";i:1535952502;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Mosaddek">
    <meta name="keyword" content="FlatLab, Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">
    <link rel="shortcut icon" href="img/favicon.png">

    <title>Editable Table</title>

    <!-- Bootstrap core CSS -->
    <link href="/__BOOT__/css/bootstrap.min.css" rel="stylesheet">
    <link href="/__BOOT__/css/bootstrap-reset.css" rel="stylesheet">
    <!--external css-->
    <link href="/__BOOT__/assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link rel="stylesheet" href="/__BOOT__/assets/data-tables/DT_bootstrap.css" />
    <!-- Custom styles for this template -->
    <link href="/__BOOT__/css/style.css" rel="stylesheet">
    <link href="/__BOOT__/css/style-responsive.css" rel="stylesheet" />
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 tooltipss and media queries -->
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
        .slt{width: 90px;}
        .self_selct{padding-left: 0px;width: 180px;margin-left: 20px;}
        .self_topic_left{float: right;line-height: 33px;}
        .self_topic{float: right;line-height: 30px;margin-left: 30px;}
        .self_topic_selct{float: left;}
        #self_do{text-align: center;}
        #adname{width: 400px;height: 34px;  font-size: 14px;  line-height: 1.42857;  color: rgb(85, 85, 85);  background-color: rgb(255, 255, 255);  background-image: none;  box-shadow: rgba(0, 0, 0, 0.075) 0px 1px 1px inset;  padding: 6px 12px;  border-width: 1px;  border-style: solid;  border-color: rgb(204, 204, 204);  border-image: initial;  border-radius: 4px;  transition: border-color 0.15s ease-in-out 0s, box-shadow 0.15s ease-in-out 0s;}
    </style>
</head>

<body>
<section id="container" class="">
    <!--main content start-->
    <section id="main-content">
        <section class="wrapper site-min-height">
            <!-- page start-->
            <section class="panel">
                <div class="panel-body">
                    <div class="adv-table editable-table ">
                        <div class="clearfix">
                            <div class="row">
                                <header class="panel-heading">
                                    广告列表
                                </header>
                                <br>
                                <div class="col-md-4 self_selct">
                                    <div class="self_topic_left">广告分类</div>
                                    <div class="self_topic_selct">
                                        <select  class="form-control slt" id="self_selct_id" onchange="window.location=this.value"  name="column_order">
                                            <option <?php if($column[0]==0): ?>selected<?php endif; ?> value="<?php echo url('admin/Advermanage/advlist'); ?>?name=0">全部</option>
                                            <option <?php if($column[0]==1): ?>selected<?php endif; ?> value="<?php echo url('admin/Advermanage/advlist'); ?>?name=1">启动页广告</option>
                                            <option <?php if($column[0]==2): ?>selected<?php endif; ?> value="<?php echo url('admin/Advermanage/advlist'); ?>?name=2">banner页广告</option>
                                            <option <?php if($column[0]==3): ?>selected<?php endif; ?> value="<?php echo url('admin/Advermanage/advlist'); ?>?name=3">新闻列表广告</option>
                                             <option <?php if($column[0]==4): ?>selected<?php endif; ?> value="<?php echo url('admin/Advermanage/advlist'); ?>?name=4">新闻详情广告</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 pull-right" style="width: 600px;float: right;">
                                    <!--搜索框开始-->
                                    <!--<form id='commentForm' role="form" method="post">-->
                                    <form class="form-horizontal m-t" action="<?php echo url('Advermanage/advlist'); ?>" id="commentForm" method="post">
                                        <div class="content clearfix m-b">
                                            <div class="form-group">
                                                <label>广告名称：</label>
                                                <input type="text" class="" id="adname" name="adname" value="<?php echo isset($adname) ? $adname :  ''; ?>" style="">
                                                <!--<button class="btn btn-primary" type="button" id="search"><strong>搜 索</strong></button>-->
                                                <input type="submit" class="btn btn-primary" value="搜 索">
                                            </div>
                                        </div>
                                    </form>
                                    <!--搜索框结束-->
                                </div>
                            </div>

                        </div>
                        <table class="table table-striped table-hover table-bordered">
                            <thead>
                            <tr>
                                <th>序号</th>
                                <th>广告标题</th>
                                <th>发布位置</th>
                                <th>发布时间</th>
                                <th>推广周期</th>
                                <th>发布人</th>
                                <th id="self_do">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(!empty($list)): if(is_array($list) || $list instanceof \think\Collection): if( count($list)==0 ) : echo "" ;else: foreach($list as $key=>$vo): ?>
                            <tr class="">
                                <td><?php echo $key+1; ?></td>
                                <td><?php echo $vo['title']; ?></td>
                                <td><?php echo $vo['posi_type']; ?></td>
                                <td><?php echo $vo['create_time']; ?></td>
                                <!--<td class="center"><?php echo $vo['circle_time_start']; ?>&#45;&#45;<?php echo $vo['circle_time_end']; ?></td>-->
                                <td class="center"><?php echo $vo['extension_time']; ?></td>
                                <td><a class="edit" href="javascript:;"><?php echo $vo['auther']; ?></a></td>
                                <td class="custom_cls">
                                    <div class="pull-right hidden-phone">
                                        <button class="btn btn-primary btn-xs edit"><a href="<?php echo $vo['operate']['编辑']; ?>">编辑</a></button>
                                        <button class="btn btn-danger btn-xs del"><a href="<?php echo $vo['operate']['下线']; ?>">下线</a></button>
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
<script src="__JS__/plugins/layer/layer.min.js"></script>
<script>
    jQuery(document).ready(function() {
        EditableTable.init();
    });
    $(function() {
        $(".pub").click(function(){
            alert("将新闻选择移动到待发布内");
        })
        $(".self_btn_time").click(function(){
            alert("按照发布时间展示");
        })
        $(".medium").attr("placeholder","搜索");
    });
    //更改栏目以及主题是改变
    function upperCase(x)
    {
        var y=document.getElementById(x).value
        document.getElementById(x).value=y.toUpperCase()
        alert(y);
    }
    //
    function downline(id){
        layer.confirm("确认要下线吗", { title: "下线确认" }, function (index) {
            layer.close(index);
            var jz;
            var url = "/admin/advermanage/adDownline";
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
                    setTimeout("javascript:history.back()", 1000);
                    }else{
                        console.log(data.err_msg);
                        layer.msg(data.msg, {time : 1000});
                    }
                }
            })
            return false;
        });
    }
    //暂时没用到
    function getContent(){
        var jz;
        var url = "/admin/advermanage/advertisementPublish";
        $.ajax({
            type:"POST",
            url:url,
            data:{'data' : $('#commentForm').serialize()},
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
                    console.log(data.err_msg);
                    //setTimeout("javascript:location.href='./advertisementPublish'", 1000);
                }else{
                    console.log(data.err_msg);
                    layer.msg(data.msg, {time : 1000});
                }
            }
        })
        return false;
    }
</script>
</body>
</html>
