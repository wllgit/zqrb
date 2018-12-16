<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:83:"D:\demo\zq\zqrb-server-php\public/../application/admin\view\columns\editcolumn.html";i:1535952501;}*/ ?>
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
    <link href="__CSS__/columnedit.css?v=1.0.1" rel="stylesheet">
    <!--<style>-->
        <!--.slt{width: 180px!important;height: 21px;padding: 0;margin: 0;}-->
        <!--.col-sm-2{padding-top: 0px!important;}-->
        <!--.btn{width: 30px;margin-left: -135px;margin-top: 20px;}-->
        <!--.bt{width: 50px;}-->
        <!--.sel_lab{width:135px;}-->
        <!--.sel_lab_radio{width:20px;}-->
        <!--.sel_ad_cont{width: 1000px;}-->
        <!--.banner_radio{width: 14px!important;height: 14px!important;display: inline-block;}-->
    <!--</style>-->
    <style>
        .tips{color: red;margin-left: 20px;line-height: 30px;}
    </style>
</head>
<body class="gray-bg">
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="ibox-content">
            <header class="panel-heading tab-bg-dark-navy-blue">
                <h2 class="nav nav-tabs">栏目设置</h2>
            </header>
            <div class="content">
                <section class="panel">
                    <header class="panel-heading tab-bg-dark-navy-blue ">
                        <ul class="nav nav-tabs">
                            <?php if(!empty($c_list)): ?>
                            <li class='active' >
                                <?php if($c_list['title'] == '热点'): ?>
                                <a data-toggle="tab" href="#about" onclick="hot_cont('<?php echo $c_list['title']; ?>')" name="a"><?php echo $c_list['title']; ?></a>
                                <?php else: ?>
                                <a data-toggle="tab" href="#about" onclick="column_cont('<?php echo $c_list['title']; ?>')"><?php echo $c_list['title']; ?></a>
                                <?php endif; ?>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </header>
                    <div id="about" class="tab-pane active">
                    <form class="form-horizontal m-t" id="commentForm" method="post" onsubmit="return getContent()">
                        <input type="hidden" name="hot_cont" id="hot_cont" value="">
                        <input type="hidden" name="cid" id="cid" value="<?php echo $c_list['id']; ?>">
                        <input type="hidden" name="wid" id="wid" value="<?php if(!empty($weight['click'])): ?><?php echo $weight['id']; endif; ?>">
                        <div class="form-group col-sm-12">
                            <label class="col-sm-2">栏目名称</label><span class="tips">*必填</span>
                            <input type="text" class="col-sm-5" readonly required="required" placeholder="热点"  name="news_name" value="<?php echo $list['title']; ?>">
                        </div>
                        <?php if(!empty($list)): if($list['parent_id'] != 0): ?>
                            <div class="form-group col-sm-12 second_tag" >
                                <label class="col-sm-2">跳转链接</label><span class="tips">*必填</span>
                                <input type="text" class="col-sm-5" required="required"  name="column_url" value="<?php echo $c_list['column_url']; ?>">
                            </div>
                            <div class="form-group col-sm-12 top_tag second_tag">
                                <label class="col-sm-2">图片</label>
                                <!--<input type="file" class="col-sm-5" required="required" name="img" style="padding: 0;" onchange="fileChange(this);">-->
                                <input type="file" id="file1" class="col-sm-5 self_df" required="required" name="pic_path" onchange="fileChange(this);" style="display: none;">
                                <label for="file1">
                                    <img class="del_img del_img" src="<?php echo isset($c_list['pic_path']) ? $c_list['pic_path'] :  ''; ?>" onclick="del_pic1()" style="width: 100px;" >
                                </label><span class="tips">*必填,请上传750*336尺寸的jpg图片</span>
                            </div>
                        <?php endif; endif; ?>

                        <div class="form-group col-sm-12">
                            <label class="col-sm-2">栏目固定</label>
                            <label>
                                <input type="radio" class="radio-left sel_lab_radio" value="1" name="column_fixed" required="required" <?php if($list['is_fixed'] == 1): ?>checked<?php endif; ?>>是</input>
                            </label>
                            <label class="redio-4">
                                <input type="radio" class="radio-right sel_lab_radio" value="0" name="column_fixed" required="required" <?php if($list['is_fixed'] == 0): ?>checked<?php endif; ?>>否</input>
                            </label>
                        </div>
                        <div class="form-group col-sm-12">
                            <label class="col-sm-2">设置新闻最早显示天数</label><span class="tips">*必填</span>
                            <input type="text" class="col-sm-5"  required="required" name="news_day" value="<?php echo $list['day_num']; ?>">
                        </div>
                        <div class="form-group col-sm-12">
                            <label class="col-sm-2">设置新闻显示数量</label>
                            <input type="text" class="col-sm-5"  required="required" name="news_num" value="<?php echo $list['news_num']; ?>" style="width: 180px;">
                            <span class="tips">*必填</span>
                        </div>
                        <?php if($c_list['title'] == '热点'): ?>
                        <div class="form-group col-sm-12">
                            <label class="col-sm-2">热点权重设置</label><span class="tips">*必填</span>
                            <div class="col-sm-5">
                                <div class="wrap">
                                    <img style="width: 80px;" src="__IMG__/hot_1.png" />
                                    <div class="inner">
                                        <img src="__IMG__/hot_3.png" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-sm-12">
                            <div class="col-lg-3" style="margin-left: 140px;width: 1000px;">
                                <?php if(!empty($weight['click'])): ?>
                                <div><span style="float: left;margin: 0 10px 0 15px;">阅读量权重</span><input type="text" class="form-control"  required="required"  name="hot_read" value="<?php echo $weight['click']; ?>" placeholder="%" style="width: 80px;height: 23px;"></div>
                                <div><span style="float: left;margin: 0 10px 0 15px;">点赞量权重</span><input type="text" class="form-control"  required="required"  name="hot_praise" value="<?php echo $weight['praise']; ?>" placeholder="%" style="width: 80px;height: 23px;"></div>
                                <div><span style="float: left;margin: 0 10px 0 15px;">转发量权重</span> <input type="text" class="form-control"  required="required"  name="hot_transmit" value="<?php echo $weight['transmit']; ?>" placeholder="%" style="width: 80px;height: 23px;"></div>
                                <div><span style="float: left;margin: 0 10px 0 15px;">收藏量权重</span> <input type="text" class="form-control"  required="required"  name="hot_collection" value="<?php echo $weight['colletion']; ?>" placeholder="%" style="width: 80px;height: 23px;"></div>
                                <?php else: ?>
                                <div><span style="float: left;margin: 0 10px 0 15px;">阅读量权重</span><input type="text" class="form-control"  required="required"  name="hot_read"  placeholder="%" style="width: 80px;height: 23px;"></div>
                                <div><span style="float: left;margin: 0 10px 0 15px;">点赞量权重</span><input type="text" class="form-control"  required="required"  name="hot_praise"  placeholder="%" style="width: 80px;height: 23px;"></div>
                                <div><span style="float: left;margin: 0 10px 0 15px;">转发量权重</span> <input type="text" class="form-control"  required="required"  name="hot_transmit"  placeholder="%" style="width: 80px;height: 23px;"></div>
                                <div><span style="float: left;margin: 0 10px 0 15px;">收藏量权重</span> <input type="text" class="form-control"  required="required"  name="hot_collection" placeholder="%" style="width: 80px;height: 23px;"></div>
                                 <?php endif; ?>
                           </div>
                        </div>
                        <?php endif; ?>
                        <div class="form-group">
                        </div>
                        <div class="form-group col-sm-12">
                            <label class="col-sm-2">是否显示广告</label>
                            <div class="col-sm-5">
                                <input class="sel_lab_radio" type="radio"  value="1" name="ad_show" required="required" onclick="add_show()" <?php if($num>0): ?>checked<?php endif; ?>>是</input>
                                <label class="redio-4">
                                    <input class="sel_lab_radio" type="radio" value="0" name="ad_show" required="required" onclick="ad_hide()" <?php if($num==0): ?>checked<?php endif; ?>>否</input>
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-sm-12">
                            <button class="btn self_ad_fix btn-info btn-sm btn-add" type="button" onclick="bannerNum()">添加</button>
                            <button class="btn self_ad_fix btn-danger btn-sm btn-dele" type="button"  onclick="bannerNumdel()">删除</button>
                        </div>
                            <div class="sel_ad_cont">
                                <div class="column_select">
                                    <div class="banner_ad_box" style="width: 1500px;display: inline-block;">
                                        <?php if(!empty($column_ad)): if(is_array($column_ad) || $column_ad instanceof \think\Collection): if( count($column_ad)==0 ) : echo "" ;else: foreach($column_ad as $key=>$vo): ?>
                                        <div class="ad_box_case form-group col-sm-12">
                                            <label class="col-sm-2">栏目第</label>
                                            <input type="text" class="col-sm-1" required="required" name="ad_sort[]" placeholder="请输入数字" value="<?php echo $vo['position']; ?>">
                                            <label class="label-3">条新闻列表前</label>
                                            <div class="sel_ad_fix div-7">
                                                <input type="radio" name="banner_radio<?php echo $key+1; ?>" value="0" onclick="rand_ads('<?php echo $key+1; ?>')" <?php if($vo['adver_id']==0): ?>checked<?php endif; ?>>随机广告</input>
                                                <input type="radio" name="banner_radio<?php echo $key+1; ?>" value="1" onclick="fix_ads('<?php echo $key+1; ?>')" <?php if($vo['adver_id']!=0): ?>checked<?php endif; ?>>固定广告</input>
                                                <div class="div-3 banner_radio<?php echo $key+1; ?>" <?php if($vo['adver_id']==0): ?>hidden<?php endif; ?>>
                                                <span class="div-5 banner_radio<?php echo $key+1; ?>">请选择广告位置</span>
                                                <select  class="div-4 form-control slt"  name="ad_position<?php echo $key+1; ?>">
                                                    <?php if(!empty($a_list)): if(is_array($a_list) || $a_list instanceof \think\Collection): if( count($a_list)==0 ) : echo "" ;else: foreach($a_list as $key=>$vo1): ?>
                                                    <option value="<?php echo $vo1['id']; ?>" <?php if($vo['adver_id']==$vo1['id']): ?>selected<?php endif; ?>><?php echo $vo1['title']; ?></option>
                                                    <?php endforeach; endif; else: echo "" ;endif; endif; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; endif; else: echo "" ;endif; endif; ?>
                                </div>
                            </div>
                            <div class="sel_rand_ad"></div>
                        <div class="form-group">
                            <button class="btn btn-primary bt" name="publish" value="1" type="submit">提交</button>
                        </div>
                    </form>
                    </div>
                </section>
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
        $t('img')[0].onmouseout = function(){
            $t('div', this.parentNode)[0].style.display = 'none';
        }
        $t('img')[0].onmouseover = function(){
            $t('div', this.parentNode)[0].style.display = 'block';
        }


        //栏目二级分类方法调用
        getSelectVal();

    })

    $("#first_name").change(function(){
        getSelectVal();
    });
    //栏目二级分类方法
    function getSelectVal(){
        $.getJSON("<?php echo url('columns/secondColumn'); ?>",{first_name:$("#first_name").val()},function(json){
            var last_name = $("#last_name");
            $("option",last_name).remove(); //清空原有的选项
            $.each(json,function(key,array){
                var option = "<option value='"+array['title']+"'>"+array['title']+"</option>";
                last_name.append(option);
            });
            if(json==''){
                $("#last_name").hide();
            }else{
                $("#last_name").show();
            }
        });
    }


    //是否显示广告
    //保存
    if(<?php echo $num; ?>==0){
        $(".self_ad_fix").hide();//不存在广告相关栏目
    }
    function add_show(){
        $(".sel_ad_cont").removeAttr('hidden');
        $(".self_ad_fix").show();
    }
    function ad_hide(){
        $(".self_ad_fix").hide();
        $(".sel_ad_cont").attr('hidden','hidden');
    }
    //添加栏目广告位
    function bannerNum(){
        var num = $(".ad_box_case").length;
        num = num+1;
        html_= '<div class="ad_box_case form-group col-sm-12">'+
            '<label class="col-sm-2">栏目第</label> <input type="text" required="required" name="ad_sort[]" placeholder="请输入数字" value="'+num+'" > 条新闻列表前'+
            '<div class="sel_ad_fix div-7">'+
            '<input type="radio" required="required" name="banner_radio'+num+'" onclick="rand_ads('+num+')" value="0">随机广告</input>'+
            '<input type="radio" required="required" name="banner_radio'+num+'" onclick="fix_ads('+num+')" value="1">固定广告</input>'+
            '<div class="div-3 banner_radio'+num+'" hidden>'+
            '<span class="div-5 banner_radio'+num+'">请选择广告位置</span>'+
            '<select  class="div-4 form-control slt"  name="ad_position'+num+'">'+
            '<?php if(!empty($a_list)): ?>'+
            '<?php if(is_array($a_list) || $a_list instanceof \think\Collection): if( count($a_list)==0 ) : echo "" ;else: foreach($a_list as $key=>$vo): ?>'+
            '<option value="<?php echo $vo["id"]; ?>" ><?php echo $vo["title"]; ?></option>'+
            '<?php endforeach; endif; else: echo "" ;endif; ?>'+
            '<?php endif; ?>'+
            '</select>'+
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

    //图片编辑
    function del_pic1(){
        $(".del_img").remove();
        $(".self_df").css("display","block");
    }

    //提交表单
    function getContent(){
        var jz;
        var url = "./editColumn";
//        var url = "http://www.zqrb.com/admin/columns/editColumn";
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
            error: function(request) {
                layer.close(jz);
                swal("网络错误!", "", "error");
            },
            success: function(data) {
                //关闭加载层
                layer.close(jz);
                if(data.code == 1){
                    //alert(data.msg);
                    layer.msg(data.msg, {time : 1000});
                    console.log(data.err_msg);
//                    setTimeout("javascript:location.href='./advertisementPublish'", 1000);
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