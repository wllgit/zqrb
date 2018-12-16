<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:81:"E:\demo\zqrb-server-php\public/../application/admin\view\newsmanage\newslist.html";i:1535952502;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/__BOOT__/css/bootstrap.min.css" rel="stylesheet">
    <link href="/__BOOT__/css/bootstrap-reset.css" rel="stylesheet">
    <link href="/__BOOT__/assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link rel="stylesheet" href="/__BOOT__/assets/data-tables/DT_bootstrap.css" />
    <link href="/__BOOT__/css/style.css?v=1.0.0" rel="stylesheet">
    <link href="/__BOOT__/css/style-responsive.css" rel="stylesheet" />
    <script src="/__BOOT__/js/html5shiv.js"></script>
    <script src="/__BOOT__/js/respond.min.js"></script>
    <!--自定义样式-->
    <style>
        #main-content{margin-left:0px;}
        .wrapper{margin-top:0px;}
        .custom_cls{width: 120px;}
        .pull-right{width: 100px;}
        .slt{width: 90px;}
        .self_selct{padding-left: 0px;width: 180px;}
        .self_topic_left{float: right;line-height: 33px;}
        .self_topic{float: right;line-height: 30px;margin-left: 30px;}
        .self_topic_selct{float: left;}
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
                                    新闻列表
                                </header>
                                <br>
                                <div class="col-lg-10 self_selct">
                                    <div class="self_topic_left">新闻分类</div>
                                    <div class="self_topic_selct">
                                        <select  class="form-control slt" id="self_selct_id" onchange="window.location=this.value"  name="column_order">
                                            <option <?php if($column[0]==0): ?>selected<?php endif; ?> value="<?php echo url('admin/Newsmanage/newslist'); ?>?name=0">全部</option>
                                            <?php if(!empty($column_info)): if(is_array($column_info) || $column_info instanceof \think\Collection): if( count($column_info)==0 ) : echo "" ;else: foreach($column_info as $key=>$vo): ?>
                                            <option <?php if($column[0]==$vo[1]): ?>selected<?php endif; ?> value="<?php echo url('admin/Newsmanage/newslist'); ?>?name=<?php echo $vo[1]; ?>"><?php echo $vo[0]; ?></option>
                                            <?php endforeach; endif; else: echo "" ;endif; endif; ?>
                                            <!--<option <?php if($column[0]==9): ?>selected<?php endif; ?> value="<?php echo url('admin/Newsmanage/newslist'); ?>?name=9">全部</option>-->
                                            <!--<option <?php if($column[0]==1): ?>selected<?php endif; ?> value="<?php echo url('admin/Newsmanage/newslist'); ?>?name=1">banner</option>-->
                                            <!--<option <?php if($column[0]==2): ?>selected<?php endif; ?> value="<?php echo url('admin/Newsmanage/newslist'); ?>?name=2">新闻列表</option>-->
                                            <!--<option <?php if($column[0]==3): ?>selected<?php endif; ?> value="<?php echo url('admin/Newsmanage/newslist'); ?>?name=3">深度</option>-->
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="btn-group pull-right" style="margin-top:60px;">
                                <form role="form" id="comForm" action="/admin/Newsmanage/newslist" method="get">
                                    <div style="width: 600px" role="form" class="form-inline pull-right">
                                        <div class="content clearfix m-b">
                                            <div class="form-group">
                                                <input type="text" class="form-control" id="source" name="source" placeholder="来源" value="<?php echo isset($source) ? $source :  ''; ?>">
                                                <input type="text" class="form-control" id="title" name="title" placeholder="标题" value="<?php echo isset($title) ? $title :  ''; ?>">
                                                <input type="text" class="form-control" id="author" name="author" placeholder="作者" value="<?php echo isset($author) ? $author :  ''; ?>">
                                            </div>
                                            <button class="btn button_btn bg-deep-blue " onclick="this.form.submit()" type="button">搜索</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <table class="table table-striped table-hover table-bordered" >
                            <thead>
                            <tr>
                                <th>序号</th>
                                <th>新闻标题</th>
                                <th>新闻链接</th>
                                <th>新闻来源</th>
                                <th>发布位置</th>
                                <th>广告插入</th>
                                <th>发布时间</th>
                                <th>作者</th>
                                <th>发布人</th>
                                <th id="self_do">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(!empty($list)): if(is_array($list) || $list instanceof \think\Collection): if( count($list)==0 ) : echo "" ;else: foreach($list as $key=>$vo): ?>
                            <tr class="">
                                <td><?php echo $vo['id']; ?></td>
                                <td><?php echo $vo['title']; ?></td>
                                <td><?php echo $domain; ?>/newsDetail/<?php echo $vo['id']; ?></td>
                                <td><?php echo $vo['source']; ?></td>
                                <td><?php echo $vo['position']; ?></td>
                                <td class="center"><?php echo $vo['allow_ad']; ?></td>
                                <td><?php echo $vo['update_time']; ?></td>
                                <td><?php echo $vo['author']; ?></td>
                                <td><?php echo $vo['name']; ?></td>
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
    //下线
    function downline(id){
        layer.confirm("确认要下线吗", { title: "确认" }, function (index) {
            layer.close(index);
            var jz;
            var url = "./newsDownline";
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
                        setTimeout("refresh()", 1000);
                    }else{
                        console.log(data.err_msg);
                        layer.msg(data.msg, {time : 1000});
                    }
                }
            })
            return false;
        });
    }
    function refresh(){
        window.location.href="";
    }
</script>
</body>
</html>
