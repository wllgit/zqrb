<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:81:"D:\demo\zq\zqrb-server-php\public/../application/admin\view\columns\docolumn.html";i:1536055662;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>栏目</title>
    <link href="__CSS__/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="__CSS__/font-awesome.min.css?v=4.4.0" rel="stylesheet">
    <link href="__CSS__/animate.min.css" rel="stylesheet">
    <link href="__CSS__/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="__CSS__/style.min.css?v=4.1.0" rel="stylesheet">
    <link href="__CSS__/plugins/sweetalert/sweetalert.css" rel="stylesheet">
    <!--日期插件样式-->
    <link rel="stylesheet" href="__JS__/jedate/jedate.css" type="text/css" />
    <link href="__CSS__/columnedit.css?1.0.1" rel="stylesheet">
    <style>
        .tips{color: red;margin-left: 20px;line-height: 30px;}
    </style>
</head>
<body class="gray-bg">
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="ibox-content">
            <header class="panel-heading tab-bg-dark-navy-blue">
                <h2 class="nav nav-tabs">栏目添加</h2>
            </header>
            <div class="content">
                <form class="form-horizontal m-t" id="commentForm" method="post" onsubmit="return getContent()">
                    <input type="hidden" name="column_cont" id="column_cont" value="">
                    <div class="form-group col-sm-12">
                        <label class="col-sm-2">栏目级别</label>
                        <label>
                            <input type="radio" checked class="radio-left sel_lab_radio top_column" value="1" name="has_child" required="required">一级</input>
                        </label>
                        <label class="redio-4">
                            <input type="radio" class="radio-right sel_lab_radio top_column" value="0" name="has_child" required="required">二级</input>
                        </label>
                    </div>
                    <div class="form-group col-sm-12 top_tag" style="display: none;">
                        <label class="col-sm-2">选择一级栏目</label>
                        <div class="input-group col-sm-8">
                            <select  class="form-control slt"  name="top_id">
                                <option value="0">请选择一级栏目</option>
                                <?php if(!empty($top_arr)): if(is_array($top_arr) || $top_arr instanceof \think\Collection): if( count($top_arr)==0 ) : echo "" ;else: foreach($top_arr as $key=>$vo): ?>
                                    <option value="<?php echo $vo['id']; ?>"><?php echo $vo['title']; ?></option>
                                <?php endforeach; endif; else: echo "" ;endif; endif; ?>
                            </select>
                            <span class="tips">*必填</span>
                        </div>
                    </div>
                    <div class="form-group col-sm-12 top_tag second_tag" style="display: none;">
                        <label class="col-sm-2">跳转链接</label> <span class="tips">*必填</span>
                        <input type="text" class="col-sm-5"  name="column_url">
                    </div>
                    <div class="form-group col-sm-12 top_tag second_tag" style="display: none;">
                        <label class="col-sm-2">图片</label> <span class="tips">*必填,请上传750*336尺寸的jpg图片</span>
                        <input type="file" class="col-sm-5"  name="pic_path" style="padding: 0;" onchange="fileChange(this);">
                    </div>
                    <div class="form-group col-sm-12">
                        <label class="col-sm-2">栏目名称</label>  <span class="tips">*必填</span>
                        <input type="text" class="col-sm-5" required="required"  name="column_name">
                    </div>
                    <!--<div class="form-group col-sm-12">-->
                        <!--<label class="col-sm-2">栏目次序</label>-->
                        <!--<input type="text" class="col-sm-5" required="required"  name="column_order" onkeyup="if(/\D/.test(this.value)){alert('只能输入数字');this.value='';}">-->
                    <!--</div>-->
                    <div class="form-group col-sm-12">
                        <label class="col-sm-2">栏目固定</label>
                        <label>
                            <input type="radio" checked class="radio-left sel_lab_radio" value="1" name="column_fixed" required="required">是</input>
                        </label>
                        <label class="redio-4">
                        <input type="radio" class="radio-right sel_lab_radio" value="0" name="column_fixed" required="required">否</input>
                        </label>
                    </div>
                    <div class="form-group col-sm-12">
                        <label class="col-sm-2">设置新闻最早显示天数</label><span class="tips">*必填</span>
                        <input type="text" class="col-sm-5"  required="required" name="column_date"  onkeyup="if(/\D/.test(this.value)){alert('只能输入数字');this.value='';}">
                    </div>
                    <div class="form-group col-sm-12">
                        <label class="col-sm-2">设置新闻显示数量</label>
                            <input type="text" class="col-sm-5"  required="required" name="column_num" onkeyup="if(/\D/.test(this.value)){alert('只能输入数字');this.value='';}">
                            <span class="tips">*必填</span>
                    </div>
                    <div>
                        <div class="form-group col-sm-12">
                            <label class="col-sm-2">是否显示广告</label>
                            <div class="col-sm-5">
                                <input class="sel_lab_radio"  type="radio" value="1" name="ad_show" required="required" onclick="add_show()">是</input>
                                <label class="redio-4">
                                    <input class="sel_lab_radio" checked type="radio" value="0" name="ad_show" required="required" onclick="ad_hide()">否</input>
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-sm-12">
                            <button class="btn btn-info self_ad_fix btn-sm btn-add" type="button" onclick="bannerNum()">添加</button>
                            <button class="btn btn-danger self_ad_fix btn-sm btn-dele" type="button"  onclick="bannerNumdel()">删除</button>
                        </div>
                        <div class="sel_ad_cont" hidden>
                            <div class="column_select">
                                <div class="banner_ad_box" style="width: 1500px;display: inline-block;">
                                    <!--<div class="ad_box_case">-->
                                        <!--栏目第 <input type="text" required="required" name="ad_sort" placeholder="请输入数字"> 条新闻列表前-->
                                        <!--<div class="sel_ad_fix">-->
                                            <!--<input type="radio" name="banner_radio" onclick="rand_ads()">随机广告</input>-->
                                            <!--<input type="radio" name="banner_radio" onclick="fix_ads()">固定广告</input>-->
                                            <!--<div class="banner_radio1" hidden>-->
                                                <!--<span class="banner_radio1">请选择广告位置</span>-->
                                                <!--<select  class="form-control slt"  name="ad_position">-->
                                                    <!--<option value="1" >11</option>-->
                                                    <!--<option value="2" >22</option>-->
                                                <!--</select>-->
                                            <!--</div>-->
                                      <!--</div>-->
                                    <!--</div>-->
                                </div>
                            </div>
                            <div class="sel_rand_ad"></div>
                            <!--<div class="adv_select" hidden>-->
                                <!--<label class="col-sm-2 control-label sel_lab">请选择广告位置：</label>-->
                                <!--<select  class="form-control slt"  name="ad_position">-->
                                    <!--<?php if(!empty($a_list)): ?>-->
                                    <!--<?php if(is_array($a_list) || $a_list instanceof \think\Collection): if( count($a_list)==0 ) : echo "" ;else: foreach($a_list as $key=>$vo): ?>-->
                                    <!--<option value="<?php echo $vo['id']; ?>" ><?php echo $vo['title']; ?></option>-->
                                    <!--<?php endforeach; endif; else: echo "" ;endif; ?>-->
                                    <!--<?php endif; ?>-->
                                <!--</select>-->
                            <!--</div>-->
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary bt" name="publish" type="submit">提交</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<script src="__JS__/jquery.min.js?v=2.1.4"></script>
<script src="__JS__/bootstrap.min.js?v=3.3.6"></script>
<script src="__JS__/content.min.js?v=1.0.0"></script>
<script src="__JS__/plugins/validate/jquery.validate.min.js"></script>
<script src="__JS__/plugins/validate/messages_zh.min.js"></script>
<script src="__JS__/plugins/iCheck/icheck.min.js"></script>
<script src="__JS__/plugins/sweetalert/sweetalert.min.js"></script>
<script src="__JS__/judgeFileType.js"></script>
<!--layer插件-->
<script src="__JS__/plugins/layer/laydate/laydate.js"></script>
<script src="__JS__/plugins/suggest/bootstrap-suggest.min.js"></script>
<script src="__JS__/plugins/layer/layer.min.js"></script>
<!--时间插件-->
<script src="__JS__/jquery.js"></script>
<script src="__JS__/jedate/jquery.jedate.js"></script>
<script type="text/javascript">
    $(function(){
        //悬浮图片显示
        var $ = function(id){
            return document.getElementById(id);
        };
        var $t = function(tag, cot){
            cot = cot || document;
            return cot.getElementsByTagName(tag);
        };
        $t('img')[0].onmouseover = function(){
            $t('div', this.parentNode)[0].style.display = 'block';
        }
        $t('img')[0].onmouseout = function(){
            $t('div', this.parentNode)[0].style.display = 'none';
        }
    })



    //是否顶级栏目
    $(".top_column").change(function(){
        var _this = $(this);
        var val = _this.val();
        if(val==0){
            $(".top_tag").css("display","block");
            $(".second_tag").children('input').attr('required','required');
        }else{
            $(".top_tag").css("display","none");
            $(".second_tag").children('input').removeAttr('required');
        }
    });


    //是否显示广告
    //保存
    $(".self_ad_fix").hide();
    function add_show(){
        $(".sel_ad_cont").removeAttr('hidden');
        $(".self_ad_fix").show();
//        $(".column_select").append('<div class="column_psi">栏目第 <input type="text" required="required" name="ad_sort" placeholder="请输入数字"> 条新闻列表前 <a href="#" onclick="add_()" style="margin-left: 30px;">添加</a></div>');
//        $(".sel_rand_ad").append(' <div class="ad_rand"><input type="radio" value="0" required="required" name="ad_rand_show" onclick="ad_rand_shows()">随机广告</input> <input type="radio" value="1" required="required" name="ad_rand_show" onclick="ad_fix_show()">固定广告</input></div>');
    }
    function ad_hide(){
        $(".self_ad_fix").hide();
        $(".sel_ad_cont").attr('hidden','hidden');
//        $(".column_psi").remove();
//        $(".ad_rand").remove();
    }

    //添加栏目广告位
    function bannerNum(){
        var num = $(".ad_box_case").length;
        num = num+1;
        html_= '<div class="ad_box_case form-group col-sm-12">'+
                    '<label class="col-sm-1">栏目第</label>'+
                    '<div class="col-sm-5">' +
                        '<input type="text" class="col-sm-1 label-3" required="required" name="ad_sort[]" placeholder="请输入数字" value="'+num+'" >'+
                        '<label class="label-3">条新闻列表前</label>'+
                        '<div class="sel_ad_fix">'+
                            '<input type="radio" required="required" name="banner_radio'+num+'" onclick="rand_ads('+num+')" value="0">随机广告</input>'+
                            '<input type="radio" required="required" name="banner_radio'+num+'" onclick="fix_ads('+num+')" value="1">固定广告</input>'+
                            '<div class="div-3 banner_radio'+num+'" hidden>'+
                                '<span class="div-5 banner_radio'+num+'">请选择广告位置</span>'+
                                '<div class="div-4">'+
                                    '<select  class="form-control slt"  name="ad_position'+num+'">'+
                                    '<?php if(!empty($a_list)): ?>'+
                                    '<?php if(is_array($a_list) || $a_list instanceof \think\Collection): if( count($a_list)==0 ) : echo "" ;else: foreach($a_list as $key=>$vo): ?>'+
                                        '<option value="<?php echo $vo["id"]; ?>" ><?php echo $vo["title"]; ?></option>'+
                                    '<?php endforeach; endif; else: echo "" ;endif; ?>'+
                                    '<?php endif; ?>'+
                                    '</select>'+
                                '</div>'+
                            '</div>'+
                        '</div>'+
                    '</div>'+
                '</div>';
        $(".banner_ad_box").append(html_);
    }
    //删除栏目广告位
    function bannerNumdel(){
        var num = $("#banner_num").val();
        var ad_box_case = $(".ad_box_case").length;
        $(".ad_box_case").last().remove();
    }

    //随机栏目广告位
    function rand_ads(i) {
        $(".banner_radio"+i).attr('hidden','hidden');
        $(".banner_radio"+i).attr('hidden','hidden');
    }
    //固定栏目广告位
    function fix_ads(i) {
        $(".banner_radio"+i).removeAttr('hidden');
        $(".banner_radio"+i).removeAttr('hidden');
    }

    //表单提交
    function getContent(){
        var jz;
        var url = "./doColumn";
        var form = new FormData(document.getElementById('commentForm'));
        $.ajax({
            url:url,
            type:"post",
            data:form,
            async: false,
            processData:false,
            contentType:false,
            beforeSend:function(){
                jz = layer.load(0, {shade: false}); //0代表加载的风格，支持0-2
            },
            error: function(data) {
                layer.close(jz);
                //layer.msg('页面加载错误！',{time : 1000});
                swal("网络错误!", "", "error");
            },
            success: function(data) {
                //关闭加载层
                layer.close(jz);
                if(data.code == 1){
                    layer.msg(data.msg, {time : 1000});
                    setTimeout("javascript:location.href='./doColumn'", 1000);
                }else{
                    swal(data.msg, "", "error");
                }

            }
        })
        return false;
    }
</script>
</body>
</html>



















