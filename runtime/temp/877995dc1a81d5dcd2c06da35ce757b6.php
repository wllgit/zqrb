<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:78:"E:\demo\zqrb-server-php\public/../application/admin\view\banner\banneradd.html";i:1535952502;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="__CSS__/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="__CSS__/font-awesome.min.css?v=4.4.0" rel="stylesheet">
    <link href="__CSS__/animate.min.css" rel="stylesheet">
    <link href="__CSS__/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="__CSS__/style.min.css?v=4.1.0" rel="stylesheet">
    <link href="__CSS__/plugins/sweetalert/sweetalert.css" rel="stylesheet">
    <!--自定义样式-->
    <style>
        #main-content{margin-left:0px;}
        .slt{width: 200px;}
        .banner_position{width: 150px;display: inline-block;}
        .banner_check{width: 14px;height: 14px;display: inline-block;}
        .banner_radio{width: 14px;height: 14px;display: inline-block;}
        .col-sm-12{margin-top: 27px;}
    </style>
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="ibox-content">
                        <header class="panel-heading tab-bg-dark-navy-blue">
                            <h2 class="nav nav-tabs">重置BANNER</h2>
                        </header>
                        <div class="content">
                            <form class="form-horizontal" id="commentForm" method="post" onsubmit="return toVaild()">
                                <fieldset title="Step1" class="step" id="default-step-0">
                                    <!--<div class="form-group">-->
                                        <!--<label class="col-lg-2 control-label">banner标题</label>-->
                                        <!--<div class="col-lg-10">-->
                                            <!--<input type="text" class="form-control" required="required" name="title">-->
                                        <!--</div>-->
                                    <!--</div>-->
                                    <!--<div class="form-group">-->
                                        <!--<label class="col-lg-2 control-label">banner链接</label>-->
                                        <!--<div class="col-lg-10">-->
                                            <!--<input type="text" class="form-control" required="required" name="url">-->
                                        <!--</div>-->
                                    <!--</div>-->
                                    <!--<div class="form-group">-->
                                        <!--<label class="col-lg-2 control-label">banner排序</label>-->
                                        <!--<div class="col-lg-10">-->
                                            <!--<input type="text" class="form-control" required="required" name="sort" onkeyup="if(/\D/.test(this.value)){alert('只能输入数字');this.value='';}">-->
                                        <!--</div>-->
                                    <!--</div>-->
                                    <!--<div class="form-group">-->
                                        <!--<label class="col-lg-2 control-label">banner显示</label>-->
                                        <!--<div class="col-lg-10">-->
                                            <!--<select  class="form-control slt"  name="is_show">-->
                                                <!--<option value="1">是</option>-->
                                                <!--<option value="0">否</option>-->
                                            <!--</select>-->
                                        <!--</div>-->
                                    <!--</div>-->
                                    <div class="form-group col-sm-12">
                                        <label class="col-sm-2">banner位置数量</label>
                                            <!--<input type="text" class="form-control banner_num" required="required" id="banner_num" name="banner_num" onchange="bannerNum(this.id)" onkeyup="if(/\D/.test(this.value)){alert('只能输入数字');this.value='';}">-->
                                        <input type="number" class="col-sm-5" required="required" id="banner_num" name="banner_num" value="<?php if(isset($num)): ?><?php echo isset($num['banner_num']) ? $num['banner_num'] :  ''; endif; ?>">
                                    </div>
                                    <div class="form-group col-sm-12">
                                        <label class="col-sm-2">banner广告位</label>
                                        <button class="btn btn-info self_ad_fix btn-sm btn-add" type="button" onclick="bannerNum()">添加</button>
                                        <button class="btn btn-danger self_ad_fix btn-sm btn-dele" type="button"  onclick="bannerNumdel()" style="margin-left: 80px;">删除</button>
                                    </div>
                                    <div class="form-group col-sm-12">
                                        <label class="col-sm-2"></label>
                                        <div class="col-lg-5" style="display: inline-block;">
                                            <div class="banner_ad_box" style="width: 150px;display: inline-block;">
                                                <?php if(isset($b_list)): if(!empty($b_list)): if(is_array($b_list) || $b_list instanceof \think\Collection): if( count($b_list)==0 ) : echo "" ;else: foreach($b_list as $key=>$vo): ?>
                                                <div class="ad_box_case">
                                                    <input type="number" class="form-control banner_position" name="banner_position[]" value="<?php echo $vo['position']; ?>">
                                                    <span >随机广告</span>
                                                    <input type="radio" class="form-control banner_radio" name="banner_radio<?php echo $key+1; ?>" onclick="rand_ads(<?php echo $key+1; ?>)" value="0" <?php if($vo['adver_id'] == 0): ?>checked<?php endif; ?>>
                                                    <span  >固定广告</span>
                                                    <input type="radio" class="form-control banner_radio" name="banner_radio<?php echo $key+1; ?>" onclick="fix_ads(<?php echo $key+1; ?>)" value="1" <?php if($vo['adver_id'] != 0): ?>checked<?php endif; ?>>
                                                    <div style="margin-top: 5px;margin-bottom: 5px;">
                                                        <span class="ad_title<?php echo $key+1; ?>" <?php if($vo['adver_id'] == 0): ?>hidden<?php endif; ?>>请选择广告位置</span>
                                                        <div class="ad_cont<?php echo $key+1; ?>"  <?php if($vo['adver_id'] == 0): ?>hidden<?php endif; ?>>
                                                            <select  class="form-control slt"  name="ad_position[]">
                                                                <option value="0" >--请选择--</option>
                                                                <?php if(!empty($a_list[0]["id"])): if(is_array($a_list) || $a_list instanceof \think\Collection): if( count($a_list)==0 ) : echo "" ;else: foreach($a_list as $key=>$v): ?>
                                                                <option value="<?php echo $v["id"]; ?>" <?php if($vo['adver_id'] == $v['id']): ?>selected<?php endif; ?>><?php echo $v["title"]; ?></option>
                                                                <?php endforeach; endif; else: echo "" ;endif; endif; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; endif; else: echo "" ;endif; endif; endif; ?>
                                                <!--<div class="ad_box_case">-->
                                                    <!--<input type="text" class="form-control banner_position" name="banner_position">-->
                                                    <!--<span >随机广告</span>-->
                                                    <!--<input type="radio" class="form-control banner_radio" name="banner_radio" onclick="rand_ads()">-->
                                                    <!--<span  >固定广告</span>-->
                                                    <!--<input type="radio" class="form-control banner_radio" name="banner_radio" onclick="fix_ads()">-->
                                                    <!--<span class="banner_radio1" hidden>请选择广告位置</span>-->
                                                    <!--<div class="banner_radio1" hidden>-->
                                                        <!--<select  class="form-control slt"  name="ad_position">-->
                                                            <!--<option value="1" >11</option>-->
                                                            <!--<option value="2" >22</option>-->
                                                        <!--</select>-->
                                                    <!--</div>-->
                                                <!--</div>-->
                                                <!--<button class="btn self_ad_fix" type="button" id="aa" onclick="selfAdFix(this.id)" style="">固定广告</button>-->


                                            <!--<div class="ad_select">-->
                                                <!--<span class="banner_radio1" hidden>请选择广告位置</span>-->
                                                <!--<div class="banner_radio1" hidden>-->
                                                    <!--<select  class="form-control slt"  name="ad_position">-->
                                                        <!--<option value="1" >11</option>-->
                                                        <!--<option value="2" >22</option>-->
                                                    <!--</select>-->
                                                <!--</div>-->
                                            <!--</div>-->

                                        </div>
                                        </div>
                                    </div>
                                    <!--<div class="form-group">-->
                                        <!--<label class="col-lg-2 control-label">banner图片</label>-->
                                        <!--<div class="col-lg-10">-->
                                            <!--<input type="file" class="form-control" required="required" name="banner_img">-->
                                        <!--</div>-->
                                    <!--</div>-->
                                </fieldset>
                                <!--<input type="submit" class="finish btn btn-danger" value="提交"/>-->
                                <button class="btn btn-primary bt" style="margin-left: 150px" name="publish" type="submit">提交</button>
                            </form>
                        </div>
    </div>

</div>

<script src="/__BOOT__/js/jquery.js"></script>
<script src="/__BOOT__/js/bootstrap.min.js"></script>
<script class="include" type="text/javascript" src="/__BOOT__/js/jquery.dcjqaccordion.2.7.js"></script>
<script src="/__BOOT__/js/respond.min.js" ></script>
<script src="/__BOOT__/js/jquery.stepy.js"></script>
<!--layer插件-->
<script src="__JS__/plugins/layer/laydate/laydate.js"></script>
<script src="__JS__/plugins/suggest/bootstrap-suggest.min.js"></script>
<script src="__JS__/plugins/layer/layer.min.js"></script>

<script>
    $(function() {
        $('#default').stepy({
            backLabel: 'Previous',
            block: true,
            nextLabel: 'Next',
            titleClick: true,
            titleTarget: '.stepy-tab'
        });
    });
    //
    function rand_ads(i) {
        $(".ad_title"+i).attr('hidden','hidden');
        $(".ad_cont"+i).attr('hidden','hidden');
    }
    function fix_ads(i) {
        $(".ad_title"+i).removeAttr('hidden');
        $(".ad_cont"+i).removeAttr('hidden');
    }

    //选择固定广告
//    function bannerNum(x){
//    function bannerNum(){
////        var num = $("#"+x).val();
//        var num = $("#banner_num").val();
//        var ad_box_case = $(".ad_box_case").length;
//        alert(ad_box_case);
////        var html_ = '';
////        for (var i=0;i<num;i++)
////        {
//            //html_+= '<input type="text" class="form-control banner_position" name="banner_position" value="'+(i+1)+'">';
////            html_+= '<div class="banner_ad_box" style="width: 100px;display: inline-block;">'+
////                        '<input type="text" class="form-control banner_position" name="banner_position" value="'+(i+1)+'">'+
////                        '<span class="">固定广告</span>'+
////                        '<input type="checkbox" class="form-control banner_check" name="banner_check[]" value="'+(i+1)+'">'+
////                    '</div>';
////        $("div[class='banner_ad_box']").remove();
////        $(".banner_radio1").before(html_);
//            html_= '<div class="ad_box_case">'+
//                        '<input type="text" class="form-control banner_position" name="banner_position[]" value="'+(i+1)+'">'+
//                        '<span >随机广告</span>'+
//                        '<input type="radio" class="form-control banner_radio" name="banner_radio'+(i+1)+'" onclick="rand_ads('+(i+1)+')" value="0">'+
//                        '<span  >固定广告</span>'+
//                        '<input type="radio" class="form-control banner_radio" name="banner_radio'+(i+1)+'" onclick="fix_ads('+(i+1)+')" value="1">'+
//                        '<span class="banner_radio'+(i+1)+'" hidden>请选择广告位置</span>'+
//                        '<div class="banner_radio'+(i+1)+'" hidden>'+
//                        '<select  class="form-control slt"  name="ad_position[]">'+
//                            '<option value="0" >--请选择--</option>'+
//                            '<?php if(!empty($a_list[0]["id"])): ?>'+
//                            '<?php if(is_array($a_list) || $a_list instanceof \think\Collection): if( count($a_list)==0 ) : echo "" ;else: foreach($a_list as $key=>$vo): ?>'+
//                            '<option value="<?php echo $vo["id"]; ?>" ><?php echo $vo["title"]; ?></option>'+
//                            '<?php endforeach; endif; else: echo "" ;endif; endif; ?>'+
//                        '</select>'+
//                        '</div>'+
//                    '</div>';
////        }
////        $("div[class='ad_box_case']").remove();
//        $(".banner_ad_box").append(html_);
//    }
    //点击切换按钮背景色


    function bannerNum(){
        var num = $("#banner_num").val();
        if(!num){
            num = 0;
        }
        var ad_box_case = $(".ad_box_case").length;

        if(ad_box_case<num){
            html_= '<div class="ad_box_case">'+
                '<input type="number" class="form-control banner_position" name="banner_position[]" value="'+(ad_box_case+1)+'">'+
                '<span >随机广告</span>'+
                '<input type="radio" class="form-control banner_radio" name="banner_radio'+(ad_box_case+1)+'" onclick="rand_ads('+(ad_box_case+1)+')" value="0">'+
                '<span  >固定广告</span>'+
                '<input type="radio" class="form-control banner_radio" name="banner_radio'+(ad_box_case+1)+'" onclick="fix_ads('+(ad_box_case+1)+')" value="1">'+
                '<div style="margin-top: 5px;margin-bottom: 5px;">'+
                '<span class="ad_title'+(ad_box_case+1)+'" hidden>请选择广告位置</span>'+
                '<div class="ad_cont'+(ad_box_case+1)+'" hidden>'+
                '<select  class="form-control slt"  name="ad_position[]">'+
                '<option value="0" >--请选择--</option>'+
                '<?php if(!empty($a_list[0]["id"])): ?>'+
                '<?php if(is_array($a_list) || $a_list instanceof \think\Collection): if( count($a_list)==0 ) : echo "" ;else: foreach($a_list as $key=>$vo): ?>'+
                '<option value="<?php echo $vo["id"]; ?>" ><?php echo $vo["title"]; ?></option>'+
                '<?php endforeach; endif; else: echo "" ;endif; endif; ?>'+
                '</select>'+
                '</div>'+
                '</div>'+
                '</div>';
            $(".banner_ad_box").append(html_);
        }else{
            layer.alert('只能添加'+num+'个,请修改banner位置数量再添加');
        }









//        if(ad_box_case<num){
//            html_= '<div class="ad_box_case">'+
//                '<input type="text" class="form-control banner_position" name="banner_position[]" value="'+(ad_box_case+1)+'">'+
//                '<span >随机广告</span>'+
//                '<input type="radio" class="form-control banner_radio" name="banner_radio'+(ad_box_case+1)+'" onclick="rand_ads('+(ad_box_case+1)+')" value="0">'+
//                '<span  >固定广告</span>'+
//                '<input type="radio" class="form-control banner_radio" name="banner_radio'+(ad_box_case+1)+'" onclick="fix_ads('+(ad_box_case+1)+')" value="1">'+
//                '<div style="margin-top: 5px;margin-bottom: 5px;">'+
//                '<span class="ad_title'+(ad_box_case+1)+'" hidden>请选择广告位置</span>'+
//                '<div class="ad_cont'+(ad_box_case+1)+'" hidden>'+
//                '<select  class="form-control slt"  name="ad_position[]">'+
//                '<option value="0" >--请选择--</option>'+
//                '<?php if(!empty($a_list[0]["id"])): ?>'+
//                '<?php if(is_array($a_list) || $a_list instanceof \think\Collection): if( count($a_list)==0 ) : echo "" ;else: foreach($a_list as $key=>$vo): ?>'+
//                '<option value="<?php echo $vo["id"]; ?>" ><?php echo $vo["title"]; ?></option>'+
//                '<?php endforeach; endif; else: echo "" ;endif; endif; ?>'+
//                '</select>'+
//                '</div>'+
//                '</div>'+
//                '</div>';
//            $(".banner_ad_box").append(html_);
//        }else{
//            layer.alert('只能添加'+num+'个,请修改banner位置数量再添加');
//        }

    }
    function bannerNumdel(){
        var num = $("#banner_num").val();
        var ad_box_case = $(".ad_box_case").length;
        $(".ad_box_case").last().remove();
    }
    //
    function selfAdFix(id){
        $("#"+id).attr('style','margin-left: 2px;width: 96px;background-color:red;');
        $("#"+id).attr('onclick','selfAdFix_(this.id)');
    }
    function selfAdFix_(id){
        $("#"+id).attr('style','margin-left: 2px;width: 96px;background-color:grey;');
        $("#"+id).attr('onclick','selfAdFix(this.id)');
    }

    //表单提交
    function toVaild(){
        var load;
        var url = "./bannerAdd";
        var form = new FormData(document.getElementById('commentForm'));
        $.ajax({
            type:"POST",
            url:url,
            data:form,
            async: false,
            processData:false,
            contentType:false,
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
                }else{
                    layer.msg(data.msg, {time : 1000});
                }

            }
        });
        return false;
    }

</script>


</body>
</html>
