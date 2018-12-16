<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:83:"D:\demo\zq\zqrb-server-php\public/../application/admin\view\newsmanage\publish.html";i:1535960062;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新闻发布</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link href="__CSS__/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="__CSS__/font-awesome.min.css?v=4.4.0" rel="stylesheet">
    <link href="__CSS__/animate.min.css" rel="stylesheet">
    <link href="__CSS__/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="__CSS__/style.min.css?v=4.1.0" rel="stylesheet">
    <!-- Sweet Alert -->
    <link href="__CSS__/plugins/sweetalert/sweetalert.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.2/summernote.css" rel="stylesheet">
    <link rel="stylesheet" href="__CSS__/fileup.css" type="text/css" />
    <link rel="stylesheet" href="__CSS__/upload_file.css" type="text/css" />
    <link href="__CSS__/adpublish.css?v=1.0.8" rel="stylesheet">
    <link href="__CSS__/newspublish.css?v=1.0.1" rel="stylesheet">
    <style>
        line-height: 10px;margin-top: -30px;
    </style>
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="ibox-content">
        <header class="panel-heading tab-bg-dark-navy-blue">
            <h2 class="nav nav-tabs">新闻发布</h2>
        </header>
        <form class="form-horizontal m-t" id="commentForm" method="post" onsubmit="return getContent()">
            <!-- 新闻id -->
            <?php if(isset($detail['id'])): ?>
            <!-- <input type="hidden" name="id" value="<?php echo isset($detail['id']) ? $detail['id'] :  ''; ?>"> -->
            <input type="hidden" name="id" value="<?php echo isset($detail['id']) ? $detail['id'] :  ''; ?>">
            <?php endif; ?>
            <div class="form-group">
                <div class="input-group col-sm-8" id="addr">
                    <div class="news-position form-group">
                        <label class="col-sm-2 control-label fb_posi" for="inputSuccess" style="padding-top: 0px;"><h3><label style="color:red;">*</label>新闻发布位置选择</h3></label>
                        <?php if(!empty($column)): if(is_array($column) || $column instanceof \think\Collection): if( count($column)==0 ) : echo "" ;else: foreach($column as $key=>$vo): if($vo['title']=='新闻'): ?>
                        <!-- 新闻栏目标签 -->
                        <label class="news checkbox-inline">
                            <p onclick="self_list_show()" class="list_p" >
                                <input class="columns-select" type="checkbox" attr="checkbox" name="publish_position[]" value="<?php echo $vo['title']; ?>" <?php if(isset($detail)): if(in_array(($vo['title']), is_array($detail['column_ids'])?$detail['column_ids']:explode(',',$detail['column_ids']))): ?>checked<?php endif; endif; ?>/> <?php echo $vo['title']; ?> <span class="banner_span"></span><span class="news_span"></span>
                            </p>
                            <ul class="dropdown-menu self_menu" role="menu">
                                <li><a href="#" onclick="banner_select()">banner</a></li>
                                <li><a href="#" onclick="news_select()">新闻列表</a></li>
                            </ul>
                        </label>
                        <?php if(isset($detail)): if(in_array(($vo['title']), is_array($detail['column_ids'])?$detail['column_ids']:explode(',',$detail['column_ids']))): if($detail['is_banner'] == 1): ?>
                        <label class="child-columns-inline">banner</label>
                        <?php else: ?>
                        <label class="child-columns-inline">新闻列表</label>
                        <?php endif; endif; endif; elseif($vo['title'] == '深度'): ?>
                        <!-- 深度栏目标签 -->
                        <label class="column_sl checkbox-inline deep">
                            <p onclick="shows()" class="list_deep"  style="">
                                <input type="checkbox" attr="checkbox" name="publish_position[]" value="<?php echo $vo['title']; ?>" <?php if(isset($detail)): if(in_array(($vo['title']), is_array($detail['column_ids'])?$detail['column_ids']:explode(',',$detail['column_ids']))): ?>checked<?php endif; endif; ?>/> <?php echo $vo['title']; ?>
                            </p>
                            <div class="child-columns" style="display: none;">
                                <ul class="dropdown-menu self_deep_menu" role="menu">
                                    <?php if(!empty($zt)): if(is_array($zt) || $zt instanceof \think\Collection): if( count($zt)==0 ) : echo "" ;else: foreach($zt as $key=>$vo): ?>
                                    <li><input type="checkbox" name="zt[]" value="<?php echo $vo['title']; ?>"><?php echo $vo['title']; ?></li>
                                    <?php endforeach; endif; else: echo "" ;endif; endif; ?>
                                    <li><a class="zt_conf" href="#" onclick="zt_confirm_show()">确定</a></li>
                                </ul>
                            </div>
                        </label>
                        <?php if(!empty($zt)): if(is_array($zt) || $zt instanceof \think\Collection): if( count($zt)==0 ) : echo "" ;else: foreach($zt as $key=>$vo): if(isset($detail)): if(in_array(($vo['title']), is_array($detail['column_ids'])?$detail['column_ids']:explode(',',$detail['column_ids']))): ?>
                        <label class="child-columns-inline"><?php echo $vo['title']; ?></label>
                        <?php endif; endif; endforeach; endif; else: echo "" ;endif; endif; else: ?>
                        <!-- 普通栏目标签 -->
                        <label class="column_sl checkbox-inline">
                            <p onclick="hot_shows()">
                                <input type="checkbox" attr="checkbox" name="publish_position[]" value="<?php echo $vo['title']; ?>" class="deep_from"/ <?php if(isset($detail)): if(in_array(($vo['title']), is_array($detail['column_ids'])?$detail['column_ids']:explode(',',$detail['column_ids']))): ?>checked<?php endif; endif; ?>> <?php echo $vo['title']; ?>
                            </p>
                            <?php endif; ?>
                        </label>
                        <?php endforeach; endif; else: echo "" ;endif; endif; ?>
                    </div>
                    <div class="all_content">
                        <!--banner-->
                        <div class="banner_content img-parent" <?php if(isset($detail)): if($detail['is_banner'] != 1): ?>hidden<?php endif; else: ?>hidden<?php endif; ?>>
                            <div class="pic-note-tip col-sm-12">
                                <lable class="control-label pic-tip col-sm-2"><label style="color:red;">*</label>上传banner图片：</lable>
                                <span class="input-group pic-note col-sm-5">注：请上传750*340尺寸的jpg图片文件 </span>
                            </div>
                            <div class="ber_file">
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-2 control-label"></label>
                                    <div class="col-sm-3 news_pic" <?php if(isset($detail['banner_pic'])): if(!(empty($detail['banner_pic']) || ($detail['banner_pic'] instanceof \think\Collection && $detail['banner_pic']->isEmpty()))): ?> style="height:120px;" <?php endif; endif; ?>>
                                        <div class="uploadfile-box uploadfile-banner" <?php if(isset($detail['banner_pic'])): if(!(empty($detail['banner_pic']) || ($detail['banner_pic'] instanceof \think\Collection && $detail['banner_pic']->isEmpty()))): ?> style="opacity:0;" <?php endif; endif; ?>>
                                            <div class="plus-img">
                                                <img src="__IMG__/imgplus.png" alt="">
                                            </div>
                                            <div class="plus-note">点击上传图片</div>
                                            <span class="fake-file">
                                                <input type="file"  class="inputstyle check_file" onchange="fileChange(this);" accept="image/*" data-class="uploadfile-banner" name="bannerfile[]" <?php if(isset($detail)): if($detail['is_banner'] == 1): ?>required="required"<?php endif; else: ?>required="required"<?php endif; ?>/>
                                            </span>
                                            <span class="real-file"></span>
                                        </div>
                                        <div class="pre-img pre-banner" <?php if(isset($detail['banner_pic'])): if(!(empty($detail['banner_pic']) || ($detail['banner_pic'] instanceof \think\Collection && $detail['banner_pic']->isEmpty()))): ?> style="top:-120px;display: block;" <?php endif; endif; ?>>
                                            <img src="<?php echo isset($detail['banner_pic']) ? $detail['banner_pic'] :  ''; ?>" alt="" <?php if(isset($detail['banner_pic'])): if(!(empty($detail['banner_pic']) || ($detail['banner_pic'] instanceof \think\Collection && $detail['banner_pic']->isEmpty()))): ?> style="display:block;width:100%;height:100%;" <?php endif; endif; ?>>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--banner-->
                        <!--新闻-->
                        <div class="news_content img-parent" <?php if(isset($detail)): if($detail['is_banner'] == 1): ?>hidden<?php endif; else: ?>hidden<?php endif; ?>>
                            <div class="form-group col-sm-12">
                                <label class="control-label col-sm-2" for="inputSuccess"><label style="color:red;">*</label>请选择新闻列表样式：</label>
                                <label class ="radio-inline style-wrap" for="inputSuccess">
                                    <input  type="radio" value="1" required="required" class="wrap style_one newsStyle-select" name="is_style" <?php if(isset($detail)): if($detail['source_type'] == 3): ?>checked<?php endif; endif; ?> data-type="common">三图</input>
                                    <p class="inner">
                                        <img src="__IMG__/news_triPic_3x.png" />
                                    </p>
                                </label>
                                <label class ="radio-inline style-wrap" for="inputSuccess">
                                    <input  type="radio" value="2" required="required" class="wrap style_two newsStyle-select"  name="is_style" <?php if(isset($detail)): if($detail['source_type'] == 5): ?>checked<?php endif; endif; ?> data-type="common">单图（小）</input>
                                    <p class="inner">
                                        <img src="__IMG__/news_oneSmall_3x.png" />
                                    </p>
                                </label>
                                <label class ="radio-inline style-wrap" for="inputSuccess">
                                    <input  type="radio" value="3" required="required" class="wrap style_three newsStyle-select"  name="is_style" <?php if(isset($detail)): if($detail['source_type'] == 2): ?>checked<?php endif; endif; ?> data-type="common">单图（大）</input>
                                    <p class="inner">
                                        <img src="__IMG__/news_oneBig_3x.png" />
                                    </p>
                                </label>
                                <label class ="radio-inline style-wrap" for="inputSuccess">
                                    <input  type="radio" value="3" required="required" class="wrap style_three newsStyle-select"  name="is_style" <?php if(isset($detail)): if($detail['source_type'] == 4): ?>checked<?php endif; if($detail['detail_type'] == 3): ?>disabled<?php endif; endif; ?> data-type="video" id="data-type-video">视频</input>
                                    <p class="inner">
                                        <img src="__IMG__/news_oneBig_3x.png" />
                                    </p>
                                </label>
                                <label class ="radio-inline style-wrap" for="inputSuccess">
                                    <input  type="radio" value="4" checked="checked" required="required" class="wrap style_four newsStyle-select"  name="is_style" <?php if(isset($detail)): if($detail['source_type'] == 1): ?>checked<?php endif; endif; ?> data-type="common">纯文本</input>
                                    <p class="inner">
                                        <img src="__IMG__/news_pureText_3x.png" />
                                    </p>
                                </label>
                            </div>
                            <div class="form-group col-sm-12 news_pic" <?php if(isset($detail['banner_pic'])): if(($detail['source_type'] == 2) OR ($detail['source_type'] == 4)): ?> style="height:120px;" <?php elseif(($detail['source_type'] == 3) OR ($detail['source_type'] == 5)): ?> style="height:60px;" <?php endif; endif; ?>>
                                <?php if(isset($detail)): ?>
                                <!-- 单图大 -->
                                <?php if($detail['source_type'] == 2): ?>
                                <label class="control-label col-sm-2" style="color:red;">注：请上传750*340尺寸的jpg图片</label>
                                <div class="control-label col-sm-1 inline-block">
                                    <div class="uploadfile-box uploadfile-vedio" style="opacity:0;">
                                        <div class="plus-img">
                                            <img src="__IMG__/imgplus.png" alt="">
                                        </div>
                                        <div class="plus-note">点击上传图片或视频</div>
                                        <span class="fake-file">
                                            <input type="file"  class="inputstyle check_file" onchange="videoFileChange(this);" accept="image/*" data-class="uploadfile-vedio" name="newsfile"/>
                                        </span>
                                        <span class="real-file"></span>
                                    </div>
                                    <div class="pre-img pre-vedio" style="top:-120px;display: block;">
                                        <img src="<?php echo isset($detail['source_pic'][0]) ? $detail['source_pic'][0] :  ''; ?>" alt="" style="display:block;width:100%;height:100%;">
                                    </div>
                                </div>
                                <!-- 三图 -->
                                <?php elseif($detail['source_type'] == 3): ?>
                                <label class="control-label col-sm-2" style="color:red;">注：请上传220*140尺寸的jpg图片</label>
                                <div class="control-label col-sm-1 inline-block">
                                    <div class="uploadfile-box uploadfile-three uploadfile-triOne" style="opacity:0;">
                                        <div class="plus-img">
                                            <img src="__IMG__/imgplus.png" alt="">
                                        </div>
                                        <span class="fake-file">
                                            <input type="file"  class="inputstyle check_file" onchange="fileChange(this,1);" accept="image/*" data-class="uploadfile-triOne" name="newsfile[]"/>
                                        </span>
                                        <span class="real-file"></span>
                                    </div>
                                    <div class="pre-img pre-three1" style="top:-60px;display: block;">
                                        <img src="<?php echo isset($detail['source_pic'][0]) ? $detail['source_pic'][0] :  ''; ?>" alt="">
                                    </div>
                                </div>
                                <div class="control-label col-sm-1 inline-block">
                                    <div class="uploadfile-box uploadfile-three uploadfile-triTwo" style="opacity:0;">
                                        <div class="plus-img">
                                            <img src="__IMG__/imgplus.png" alt="">
                                        </div>
                                        <span class="fake-file">
                                            <input type="file"  class="inputstyle check_file" onchange="fileChange(this,2);" accept="image/*" data-class="uploadfile-triTwo" name="newsfile[]"/>
                                        </span>
                                        <span class="real-file"></span>
                                    </div>
                                    <div class="pre-img pre-three2" style="top:-60px;display: block;">
                                        <img src="<?php echo isset($detail['source_pic'][1]) ? $detail['source_pic'][1] :  ''; ?>" alt="">
                                    </div>
                                </div>
                                <div class="control-label col-sm-1 inline-block">
                                    <div class="uploadfile-box  uploadfile-three uploadfile-triThree" style="opacity:0;">
                                        <div class="plus-img">
                                            <img src="__IMG__/imgplus.png" alt="">
                                        </div>
                                         <span class="fake-file">
                                            <input type="file"  class="inputstyle check_file" onchange="fileChange(this,3);" accept="image/*" data-class="uploadfile-triThree" name="newsfile[]"/>
                                        </span>
                                        <span class="real-file"></span>
                                    </div>
                                    <div class="pre-img pre-three3" style="top:-60px;display: block;">
                                        <img src="<?php echo isset($detail['source_pic'][2]) ? $detail['source_pic'][2] :  ''; ?>" alt="">
                                    </div>
                                </div>
                                <!-- 视频 -->
                                <?php elseif($detail['source_type'] == 4): ?>
                                <label class="control-label col-sm-2"></label>
                                <div class="control-label col-sm-1 inline-block">
                                    <div class="uploadfile-box uploadfile-vedio" style="opacity:0;">
                                        <div class="plus-img">
                                            <img src="__IMG__/imgplus.png" alt="">
                                        </div>
                                        <div class="plus-note">点击上传图片或视频</div>
                                        <span class="fake-file">
                                            <input type="file"  class="inputstyle check_file" onchange="videoFileChange(this);" accept="image/*" data-class="uploadfile-vedio" name="newsfile"/>
                                        </span>
                                        <span class="real-file"></span>
                                    </div>
                                    <div class="pre-img pre-vedio" style="top:-120px;display: block;">
                                        <video src="<?php echo isset($detail['source_pic'][0]) ? $detail['source_pic'][0] :  ''; ?>" style="display:block;width:100%;height:100%;" controls="controls"></video>
                                    </div>
                                </div>
                                <!-- 单图小 -->
                                <?php elseif($detail['source_type'] == 5): ?>
                                <label class="control-label col-sm-2" style="color:red;">注：请上传140*140尺寸的jpg图片</label>
                                <div class="control-label col-sm-1 inline-block">
                                    <div class="uploadfile-box uploadfile-three" style="opacity:0;">
                                        <div class="plus-img">
                                            <img src="__IMG__/imgplus.png" alt="">
                                        </div>
                                        <span class="fake-file">
                                            <input type="file"  class="inputstyle check_file" onchange="fileChange(this);" accept="image/*" data-class="uploadfile-three" name="newsfile"/>
                                        </span>
                                        <span class="real-file"></span>
                                    </div>
                                    <div class="pre-img pre-three" style="top:-60px;display: block;">
                                        <img src="<?php echo isset($detail['source_pic'][0]) ? $detail['source_pic'][0] :  ''; ?>" alt="" style="display:block;width:100%;height:100%;">
                                    </div>
                                </div>
                                <?php endif; endif; ?>
                            </div>
                        </div>
                        <!--新闻-->
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><label style="color:red;">*</label>标题：</label>
                            <div class="input-group col-sm-8">
                                <input type="text" placeholder="标题" required="required" name="news_title" class="form-control" value="<?php echo isset($detail['title']) ? $detail['title'] :  ''; ?>">
                            </div>
                        </div>
                        <!--新闻详情样式开始-->
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><label style="color:red;">*</label>新闻详情样式：</label>
                            <div class="input-group col-sm-8">
                                <label class="radio-inline" style="padding-top:0px;">
                                    <input class="detail-type-transverse" type="radio" value="1" name="news_detail" required="required" <?php if(isset($detail)): if($detail['detail_type'] == 3): ?>checked<?php endif; if($detail['source_type'] == 4): ?>disabled<?php endif; endif; ?>>横向文本流</input>
                                </label>
                                <label class="radio-inline" style="padding-top:0px;">
                                    <input class="detail-type-vertical" type="radio" checked="checked" value="0" name="news_detail" required="required" <?php if(isset($detail)): if(($detail['detail_type'] == '') OR ($detail['detail_type'] == 1)): ?>checked<?php endif; endif; ?>>正常文本流</input>
                                </label>
                            </div>
                            <div class="col-sm-2 detail_file" style="margin-top: 20px;">
                                <?php if(isset($detail)): if($detail['detail_type'] == 3): if(!empty($detail_arr)): if(is_array($detail_arr) || $detail_arr instanceof \think\Collection): if( count($detail_arr)==0 ) : echo "" ;else: foreach($detail_arr as $key=>$vo): ?>
                                <input type="file" id="file_<?php echo $key+1; ?>" onchange="fileChange(this);" class="self_df self_df<?php echo $key+1; ?>" name="newsdetail[]" style="display: none;">
                                <label for="file_<?php echo $key+1; ?>">
                                    <img class="del_img<?php echo $key+1; ?> del_img" src="<?php echo $vo['source_path']; ?>" onclick="del_pic1('<?php echo $key+1; ?>')" style="width: 100px;" >
                                </label>
                                <textarea rows="5" cols="100" class="textA newsDetail" required="required" name="detailcont[]" ><?php echo $vo['detail']; ?></textarea>
                                <?php endforeach; endif; else: echo "" ;endif; endif; endif; endif; ?>
                            </div>
                            <div class="col-sm-4 detail_do" <?php if(isset($detail)): if($detail['detail_type'] == 3): ?>style="display:block;"<?php else: ?>style="display:none;"<?php endif; else: ?>style="display:none;"<?php endif; ?>>
                                <button class="btn self_ad_fix" type="button" onclick="fileAdd()">添加</button>
                                <button class="btn self_ad_fix" type="button" onclick="fileDel()">删除</button>
                            </div>
                        </div>
                        <!--新闻详情样式结束-->
                        <div class="form-group zw_cont" <?php if(isset($detail)): if($detail['detail_type'] == 3): ?>style="display:none;"<?php else: ?>style="display:block;"<?php endif; endif; ?>>
                            <label class="col-sm-2 control-label"><label style="color:red;">*</label>正文：</label>
                            <div class="input-group col-sm-8">
                                <div class="input-group col-sm-8">
                                    <!-- 文本编辑器  开始 -->
                                    <textarea name="content" class="newsDetail" id="summernote" required="required"><?php echo isset($detail['detail']) ? $detail['detail'] :  ''; ?></textarea>
                                    <!-- 文本编辑器  结束-->
                                </div>
                            </div>
                        </div>
                        <!-- 新闻生成简介 -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label">自动生成简介：</label>
                            <div class="input-group col-sm-8">
                                <span class="btn btn-primary" onclick="makeSummary()">生成</span>
                            </div>
                        </div>
                        <!-- 新闻简介 -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label">简介：</label>
                            <div class="input-group col-sm-8">
                                <input type="text" placeholder="简介" required="required" name="news_summary" class="form-control newsSummary" value="<?php echo isset($detail['summary']) ? $detail['summary'] :  ''; ?>">
                            </div>
                        </div>
                        <!-- 新闻关键字 -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><label style="color:red;">*</label>新闻关键字：</label>
                            <div class="input-group col-sm-8">
                                <input type="text" placeholder="新闻关键字" required="required" name="news_keyword" class="form-control" value="<?php echo isset($detail['keywords']) ? $detail['keywords'] :  ''; ?>">
                            </div>
                        </div>
                        <!-- 新闻作者 -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label">作者：</label>
                            <div class="input-group col-sm-8">
                                <input type="text" placeholder="新闻作者" name="news_author" class="form-control" value="<?php echo isset($detail['author']) ? $detail['author'] :  ''; ?>">
                            </div>
                        </div>
                        <!-- 新闻来源 -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><label style="color:red;">*</label>来源：</label>
                            <div class="input-group col-sm-8">
                                <input type="text" placeholder="新闻来源" required="required" name="source" class="form-control" value="<?php echo isset($detail['source']) ? $detail['source'] :  ''; ?>">
                            </div>
                        </div>
                        <!-- 上传新闻音频文件 -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label">音频（非必填项）：</label>
                            <div class="input-group col-sm-8 fixed_send">
                                <input type="file" name="audio" class="banner_req" value="<?php echo isset($detail['audio']) ? $detail['audio'] :  ''; ?>">
                            </div>
                        </div>
                        <?php if(isset($detail)): if(!(empty($detail['audio']) || ($detail['audio'] instanceof \think\Collection && $detail['audio']->isEmpty()))): ?>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"></label>
                            <div class="input-group col-sm-8" style="display: block;">
                                <audio src="<?php echo $detail['audio']; ?>" controls="controls"></audio>
                            </div>
                        </div>
                        <?php endif; endif; ?>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">权限设置：</label>
                            <div class="input-group col-sm-8">
                                <label class="checkbox-inline" style="padding-top:0px;">
                                    <input type="checkbox" checked="checked" name="auth_discuss" value="1" <?php if(isset($detail)): if($detail['allow_comment'] == 1): ?>checked<?php endif; endif; ?>/> 评论
                                </label>
                                <label class="checkbox-inline" style="padding-top:0px;">
                                    <input type="checkbox" checked="checked" name="auth_forward" value="1" <?php if(isset($detail)): if($detail['allow_transmit'] == 1): ?>checked<?php endif; endif; ?>/> 转发
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">是否显示广告：</label>
                            <div class="input-group col-sm-8">
                                <label class="radio-inline" style="padding-top:0px;">
                                    <input  type="radio" checked="checked" value="1" name="allow_ad" required="required" <?php if(isset($detail)): if($detail['allow_ad'] == 1): ?>checked<?php endif; endif; ?>>是</input>
                                </label>
                                <label class="radio-inline" style="padding-top:0px;">
                                    <input  type="radio" value="0" name="allow_ad" required="required" <?php if(isset($detail)): if($detail['allow_ad'] == 0): ?>checked<?php endif; endif; ?>>否</input>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">相关新闻推送(0表示不推送)：</label>
                            <div class="input-group col-sm-8">
                                <select  class="form-control slt"  name="is_recommend">
                                    <option value="0" <?php if(isset($detail)): if($detail['is_recommend'] == 0): ?>selected<?php endif; endif; ?>>0</option>
                                    <option value="1" <?php if(isset($detail)): if($detail['is_recommend'] == 1): ?>selected<?php endif; endif; ?>>1</option>
                                    <option value="2" <?php if(isset($detail)): if($detail['is_recommend'] == 2): ?>selected<?php endif; endif; ?>>2</option>
                                    <option value="3" <?php if(isset($detail)): if($detail['is_recommend'] == 3): ?>selected<?php endif; endif; ?>>3</option>
                                    <option value="4" <?php if(isset($detail)): if($detail['is_recommend'] == 4): ?>selected<?php endif; else: ?>selected="selected"<?php endif; ?>>4</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group self_order" <?php if(isset($detail)): if($detail['banner_sort'] != ''): ?>style="display:block;"<?php else: ?>style="display:none;"<?php endif; else: ?>style="display:none;"<?php endif; ?>>
                            <label class="col-sm-2 control-label">新闻次序：</label>
                            <div class="input-group col-sm-8">
                                <select  class="form-control slt"  name="sort">
                                    <?php if(!empty($order)): if(is_array($order) || $order instanceof \think\Collection): if( count($order)==0 ) : echo "" ;else: foreach($order as $key=>$vo): ?>
                                        <option value="<?php echo $vo; ?>" <?php if(isset($detail)): if($detail['banner_sort'] == $vo): ?>selected<?php endif; endif; ?>><?php echo $vo; ?></option>
                                    <?php endforeach; endif; else: echo "" ;endif; endif; ?>
                                </select>
                            </div>
                        </div>
                        <!-- 发布状态 -->
                        <div class="form-group col-sm-12">
                            <label class="control-label col-sm-2" for="inputSuccess">发布状态:</label>
                            <div>
                                <label class="radio-inline">
                                    <input type="radio" value="1" name="publish_time" required="required" <?php if(isset($detail)): if($detail['is_show'] == 1): ?>checked<?php endif; else: ?>checked<?php endif; ?>> 立即发布
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" value="0" name="publish_time" required="required" <?php if(isset($detail)): if($detail['is_show'] == 0): ?>checked<?php endif; endif; ?>> 待发布
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-3">
                                <button class="btn btn-primary" style="margin-left:150px;" type="submit">提交</button>
                            </div>
                        </div>
                        <input type="hidden" id="aa" name="dd" value="">
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script src="__JS__/jquery.min.js?v=2.1.4"></script>
<script src="__JS__/bootstrap.min.js?v=3.3.6"></script>
<script src="__JS__/content.min.js?v=1.0.0"></script>
<script src="__JS__/plugins/validate/jquery.validate.min.js"></script>
<script src="__JS__/plugins/validate/messages_zh.min.js"></script>
<script src="__JS__/plugins/iCheck/icheck.min.js"></script>
<script src="__JS__/plugins/sweetalert/sweetalert.min.js"></script>
<script src="__JS__/judgeVideoFileType.js"></script>
<script src="__JS__/judgeFileType.js"></script>
<!--layer插件-->
<script src="__JS__/plugins/layer/laydate/laydate.js"></script>
<script src="__JS__/plugins/suggest/bootstrap-suggest.min.js"></script>
<script src="__JS__/plugins/layer/layer.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.2/summernote.js"></script>
<script src="__JS__/newImg.js?v=1.2.11"></script>
<script type="text/javascript">
    var is_vertical = true;// 新闻详情排版方式，默认正常文本流
    var banner_content = $(".banner_content").html();
    var news_content   = $(".news_content").html();
    var new_detail = '<input type="file" onchange="fileChange(this);" class="df" required="required" name="newsdetail[]"><textarea rows="5" cols="100" class="textA newsDetail" required="required" name="detailcont[]"></textarea>';
    $(function(){
        var url = '/admin/Newsmanage/sourceblack';
        $("input[name='source']").on('input propertychange',function(){
            var data = {'data':$(this).val()};
            $.get(url,data,function (data) {
                var info = $.parseJSON(data);
                if (info.isSuccess ==1) {
                    layer.msg(info.msg,{icon:5,time:3000});
                }
            });
        });
    })

    $(function(){
        $('#summernote').summernote({
            height:1200,
            lang:'zh-CN',
            placeholder: '',
            toolbar: [
                ['fontname', ['fontname']], //字体系列
                ['style', ['bold', 'italic', 'underline', 'clear']], // 字体粗体、字体斜体、字体下划线、字体格式清除
                ['font', ['strikethrough', 'superscript', 'subscript']], //字体划线、字体上标、字体下标
                ['fontsize', ['fontsize']], //字体大小
                ['color', ['color']], //字体颜色
                ['style', ['style']],//样式
                ['para', ['ul', 'ol', 'paragraph']], //无序列表、有序列表、段落对齐方式
                ['height', ['height']], //行高
                ['table',['table']], //插入表格
                ['hr',['hr']],//插入水平线
                //['link',['link']], //插入链接
                ['picture',['picture']], //插入图片
                // ['video',['video']], //插入视频
                ['fullscreen',['fullscreen']], //全屏
                ['codeview',['codeview']], //查看html代码
                ['undo',['undo']], //撤销
                ['redo',['redo']], //取消撤销
                ['help',['help']], //帮助
            ],
            callbacks:{
                onImageUpload:function (files) {
                    var formdata = new FormData();
                    formdata.append("image", files[0]);
                    $.ajax({
                        url: '/admin/newsmanage/test',
                        method: 'POST',
                        data:formdata,
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            if (data['message']=='ok') {
                                alert(data.message);
                                $('#summernote').summernote('insertImage', data['payload']['imgPath']);
                            }
                            else{
                                op.utils.toastr('插入图片失败','error');
                            }
                        }
                    });
                }
            }
        });
        //设置文本框的高度
        $(".note-editable").attr('style','height:527px;');


    })
    //广告样式
    $('.news_content').on('mouseover','.style-wrap',function() {
        var val = $(this).find('input').val();
        var inner = $('.news_content').find(".inner")[val-1];
        inner.style.display = 'block';
    }) 
    $('.news_content').on('mouseout','.style-wrap',function() {
        var val = $(this).find('input').val();
        var inner = $('.news_content').find(".inner")[val-1];
        inner.style.display = 'none';
    })

    function self_list_show(){
        $(".self_menu").show();
        $(".list_p").attr('onclick','self_list_hide()');
    }
    function self_list_hide(){
        $(".self_menu").hide();
        $(".list_p").attr('onclick','self_list_show()');
        var child_columns = $(".news").next('.child-columns-inline');
        if(child_columns != '' || child_columns != null ) {
            child_columns.remove();
        }
    }
    function shows(){
        $(".banner_content").find(".inputstyle").removeAttr('required');
        $(".child-columns").show();
        $(".self_deep_menu").show();
        $(".list_deep").attr('onclick','hid()');
        $(".news_content").removeAttr('hidden');
    }
    function hid(){
        $(".self_deep_menu").hide();
        $(".list_deep").attr('onclick','shows()');
        $(".cont").remove();
    }
    function hot_shows(){
        $(".banner_content").find(".inputstyle").removeAttr('required');
        $(".news_content").removeAttr('hidden');
    }
    function banner_select(){
        $(".banner_content").removeAttr('hidden');
        $(".news_content").children().remove();
        $(".banner_content").html(banner_content);
        $(".news_content").attr('hidden','hidden');
        $(".column_sl").hide();
        $(".row").hide();
        $("[attr='checkbox']").each(function(e){
            if(e>0){
                $(this).removeAttr("checked");//取消选中
            }
        });
        $("input[name='is_style']").each(function(e){
            $(this).removeAttr("required");//取消样式必填
        });
        $("input[class='df']").each(function(e){
            $(this).removeAttr("required");//取消详情样式必填
        });
        $("input[class='self_df']").each(function(e){
            $(this).removeAttr("required");//取消详情样式必填
        });
        $(".self_menu").hide();
        var child_columns = $('.child-columns-inline');
        if(child_columns != '' || child_columns != null ) {
            child_columns.remove();
        }
        var content = '<label class="child-columns-inline">banner</label>';
        $('.news').after(content);
        $(".self_order").css("display","block");
    }
    function news_select(){
        $(".news_content").removeAttr('hidden');
        $(".banner_content").children().remove();
        $(".news_content").html(news_content);
        $(".banner_content").attr('hidden','hidden');
        $("input[class='banner_req']").each(function(e){
            $(this).removeAttr("required");//取消选中
        });
        $(".column_sl").show();
        $(".self_menu").hide();
        var child_columns = $(".news").next('.child-columns-inline');
        if(child_columns != '' || child_columns != null ) {
            child_columns.remove();
        }
        var content = '<label class="child-columns-inline">新闻列表</label>';
        $('.news').after(content);
        $(".self_order").css("display","none");
        if(!is_vertical) {
            $(".news_content").find("#data-type-video").attr("disabled","disabled");
        }else {
            $(".news_content").find("#data-type-video").removeAttr("disabled");
        }
    }
    function zt_confirm_show(){
        var content = '';
        var child_columns = $(".deep").nextAll('.child-columns-inline');
        if(child_columns != '' || child_columns != null ) {
            child_columns.remove();
        }
        $("input[name='zt[]']").each(function(){
            if ($(this).is(':checked')) {
                content += '<label class="child-columns-inline">'+$(this).val()+'</label>'
            }
        });
        $('.deep').after(content);
        $(".self_deep_menu").hide();
    }
    //新闻详情
    $("input[name='news_detail']").change(function(){
        var discount = $(this).val();
        if(discount=="0"){
            $(".detail_do").css("display","none");
            $(".df").remove();
            $(".self_df").remove();
            $(".textA").remove();
            $(".zw_cont").css("display","block");
            $("#summernote").attr("required","required");
            is_vertical = true;
            $(".news_content").find("#data-type-video").removeAttr("disabled");
        }
        if(discount=="1"){
            $(".detail_do").css("display","block");
            $(".detail_file").html(new_detail);
            $(".zw_cont").css("display","none");
            $("#summernote").removeAttr("required");
            is_vertical = false;
        }
    });

    $('.news_content').on('change','.newsStyle-select',function(){
        var _this = $(this);
        var val = _this.val();
        var data_type = _this.attr('data-type');
        var content = '';
        var height = 0;
        var parent = _this.parent().parent().parent();
        var news_pic  = parent.find('.news_pic');
        var child_img = news_pic.children();
        if(child_img != '' && child_img != null) {
            child_img.remove();
        }
        if(val == 1) {
            height = 60;
            content = '<label class="control-label col-sm-2" style="color:red;">注：请上传220*140尺寸的jpg图片</label><div class="control-label col-sm-1 inline-block"><div class="uploadfile-box uploadfile-three uploadfile-triOne"><div class="plus-img"><img src="__IMG__/imgplus.png" alt=""></div><span class="fake-file"><input type="file" onchange="fileChange(this,1);"  class="inputstyle check_file"  accept="image/*" data-class="uploadfile-triOne" required="required" name="newsfile[]"/></span><span class="real-file"></span></div><div class="pre-img pre-three1"><img src="" alt=""></div></div><div class="control-label col-sm-1 inline-block"><div class="uploadfile-box uploadfile-three uploadfile-triTwo"><div class="plus-img"><img src="__IMG__/imgplus.png" alt=""></div><span class="fake-file"><input type="file" onchange="fileChange(this,2);"  class="inputstyle check_file"  accept="image/*" data-class="uploadfile-triTwo" required="required" name="newsfile[]"/></span><span class="real-file"></span></div><div class="pre-img pre-three2"><img src="" alt=""></div></div><div class="control-label col-sm-1 inline-block"><div class="uploadfile-box  uploadfile-three uploadfile-triThree"><div class="plus-img"><img src="__IMG__/imgplus.png" alt=""></div><span class="fake-file"><input type="file" onchange="fileChange(this,3);" class="inputstyle check_file"  accept="image/*" data-class="uploadfile-triThree" required="required" name="newsfile[]"/></span><span class="real-file"></span></div><div class="pre-img pre-three3"><img src="" alt=""></div></div>';
        }else if(val == 2) {
            height = 60;
            content = '<label class="control-label col-sm-2" style="color:red;">注：请上传140*140尺寸的jpg图片</label><div class="control-label col-sm-1 inline-block"><div class="uploadfile-box uploadfile-three"><div class="plus-img"><img src="__IMG__/imgplus.png" alt=""></div><span class="fake-file"><input type="file" onchange="fileChange(this);" class="inputstyle check_file"  accept="image/*" data-class="uploadfile-three" required="required" name="newsfile"/></span><span class="real-file"></span></div><div class="pre-img pre-three"><img src="" alt=""></div></div>';
        }else if(val == 3) {
            height = 120;
            content = '<label class="control-label col-sm-2" style="color:red;">注：请上传750*340尺寸的jpg图片</label><div class="control-label col-sm-1 inline-block"><div class="uploadfile-box uploadfile-vedio"><div class="plus-img"><img src="__IMG__/imgplus.png" alt=""></div><div class="plus-note">点击上传图片或视频</div><span class="fake-file"><input type="file" onchange="videoFileChange(this);"  class="inputstyle check_file"  accept="image/*" data-class="uploadfile-vedio" required="required" name="newsfile"/></span><span class="real-file"></span></div><div class="pre-img pre-vedio"><img src="" alt=""> <div class="fake-video"></div></div></div>';
        }
        if(data_type == 'video') {
            $(".detail-type-transverse").attr('disabled','disabled');
        }else{
            $(".detail-type-transverse").removeAttr("disabled");
        }
        news_pic.css('height',height+'px');
        news_pic.html(content)
    })
    //新闻详情图片增加
    function fileAdd(){
        $(".detail_file").append('<input type="file" onchange="fileChange(this);" class="self_df" required="required" name="newsdetail[]"><textarea rows="5" cols="100" class="textA" required="required" name="detailcont[]"></textarea>');
        $("input[class='df']").each(function(e){
            $(this).attr("required","required");//取消选中
        });
    }
    //新闻详情图片删除
    function fileDel(){
        $(".self_df").last().remove();
        $(".textA").last().remove();
        $(".del_img").last().remove();
        $(".pic_a").last().remove();
    }
    //新闻详情图片编辑
    function del_pic1(num){
        $(".del_img"+num).remove();
        $(".self_df"+num).css("display","block");
    }
    //表单提交
    function getContent(){
        var jz;
        var url = "/admin/Newsmanage/publish";
        var form = new FormData(document.getElementById("commentForm"));
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
                    layer.msg(data.msg, {time : 1000});
                    console.log(data.err_msg);
                    no_submit();
                    setTimeout("javascript:location.href='./publish'", 1000);
                }else{
                    console.log(data.err_msg);
                    layer.msg('编辑成功', {time : 1000});
                    setTimeout("javascript:location.href='/admin/newsmanage/newslist'", 1000);
                }
            }
        });
        return false;
    }
    function no_submit(){
        $(".btn-primary").attr("disabled","disabled");
    }
    // 自动生成简介
    function makeSummary() {
        var detailObj = $('.newsDetail');
        var text =  delHtmlTag(detailObj.val());
        text = text.substring(0,100) + '...';
        $('.newsSummary').val(text);
    }
    // 正则去掉html标签
    function delHtmlTag(str){
        return str.replace(/<[^>]+>/g,"");//去掉所有的html标记
    }
</script>
</body>
</html>



