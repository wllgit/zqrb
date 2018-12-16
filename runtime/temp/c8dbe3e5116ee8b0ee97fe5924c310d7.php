<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:94:"E:\demo\zqrb-server-php\public/../application/admin\view\advermanage\advertisementpublish.html";i:1536050520;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新闻发布</title>
    <link href="__CSS__/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="__CSS__/font-awesome.min.css?v=4.4.0" rel="stylesheet">
    <link href="__CSS__/animate.min.css" rel="stylesheet">
    <link href="__CSS__/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="__CSS__/style.min.css?v=4.1.1" rel="stylesheet">
    <!-- Sweet Alert -->
    <link href="__CSS__/adpublish.css?v=1.0.3" rel="stylesheet">
    <link href="__CSS__/plugins/sweetalert/sweetalert.css" rel="stylesheet">
    <!--日期插件样式-->
    <link rel="stylesheet" href="__JS__/jedate/jedate.css" type="text/css" />
    <!-- 富文本编辑开始-->
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
    <script type="text/javascript" charset="utf-8" src="__FILE_UPLOAD__/ueditor.config.js"></script>
    <!--<script type="text/javascript" charset="utf-8" src="__FILE_UPLOAD__/ueditor.all.min.js"> </script>-->
    <script type="text/javascript" charset="utf-8" src="__FILE_UPLOAD__/ueditor.all.js"> </script>
    <!--建议手动加在语言，避免在ie下有时因为加载语言失败导致编辑器加载失败-->
    <!--这里加载的语言文件会覆盖你在配置项目里添加的语言类型，比如你在配置项目里配置的是英文，这里加载的中文，那最后就是中文-->
    <script type="text/javascript" charset="utf-8" src="__FILE_UPLOAD__/lang/zh-cn/zh-cn.js"></script>
    <!-- 富文本编辑结束-->

</head>
<body class="gray-bg">
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="ibox-content">
            <header class="panel-heading tab-bg-dark-navy-blue">
                <h2 class="nav nav-tabs">广告发布</h2>
            </header>
            <div class="position-select">
                <div class="form-group col-sm-12">
                    <label class="col-sm-2 control-label" for="inputSuccess"><h3>广告位置选择：</h3></label>
                    <div class="col-sm-10">
                        <label class="checkbox-inline col-sm-2">
                            <input type="radio" value="start" name="position" <?php if(isset($detail)): if($detail['posi_type'] == 1): ?>checked<?php endif; else: ?>checked<?php endif; ?>> 启动页
                        </label>
                        <label class="checkbox-inline col-sm-2">
                            <input type="radio" value="banner" name="position" <?php if(isset($detail)): if($detail['posi_type'] == 2): ?>checked<?php endif; else: endif; ?>> banner
                        </label>
                        <label class="checkbox-inline col-sm-2">
                            <input type="radio" value="newsList" name="position" <?php if(isset($detail)): if($detail['posi_type'] == 3): ?>checked<?php endif; else: endif; ?>> 新闻列表
                        </label>
                        <label class="checkbox-inline col-sm-2">
                            <input type="radio" value="detailList" name="position" <?php if(isset($detail)): if($detail['posi_type'] == 4): ?>checked<?php endif; else: endif; ?>> 新闻详情
                        </label>
                    </div>
                </div>
            </div>
            <div class="content">
                <div class="position-form">
                    <!-- 启动页发布广告 -->
                    <div class="form-group">
                         <form class="start-form" id="commentForm" method="post" onsubmit="return getContent('commentForm')" <?php if(isset($detail)): if($detail['posi_type'] != 1): ?>hidden<?php endif; endif; ?>>
                            <?php if(isset($detail['id'])): ?>
                            <input type="hidden" name="id" value="<?php echo isset($detail['id']) ? $detail['id'] :  ''; ?>">
                            <?php endif; ?>
                            <div class="form-group col-sm-12">
                                <div class="control-label pic-note-tip col-sm-10">
                                    <label class="control-label pic-tip col-sm-2"><label style="color:red;">*</label>上传广告图片</label>
                                    <span class="pic-note col-sm-7" style="color:red;">注：请上传750*1334尺寸的jpg图片或者750*1334尺寸flash文件</span>
                                </div>
                                <!-- 广告图片 -->
                                <div class="control-label col-sm-10">
                                    <div class="col-sm-2 news_pic" <?php if(isset($detail)): if($detail['posi_type'] == 1): if(!(empty($detail['pic_path'][0]) || ($detail['pic_path'][0] instanceof \think\Collection && $detail['pic_path'][0]->isEmpty()))): ?> style="height: 200px;"<?php endif; endif; endif; ?>>
                                        <div class="uploadfile-box uploadfile-start" <?php if(isset($detail)): if($detail['posi_type'] == 1): if(!(empty($detail['pic_path'][0]) || ($detail['pic_path'][0] instanceof \think\Collection && $detail['pic_path'][0]->isEmpty()))): ?> style="opacity: 0;"<?php endif; endif; endif; ?>>
                                            <div class="plus-img">
                                                <img src="__IMG__/imgplus.png" alt="">
                                            </div>
                                            <div class="plus-note">点击上传图片</div>
                                            <input type="file"  class="inputstyle check_file" onchange="fileChange(this);"  accept="image/*" <?php if(!isset($detail)): ?> required="required" <?php endif; ?> name="startfile" />
                                        </div>
                                        <div class="pre-img pre-start" <?php if(isset($detail)): if($detail['posi_type'] == 1): if(!(empty($detail['pic_path'][0]) || ($detail['pic_path'][0] instanceof \think\Collection && $detail['pic_path'][0]->isEmpty()))): ?> style="display:block;top:-200px;"<?php endif; endif; endif; ?>>
                                            <img src="<?php if(isset($detail)): if($detail['posi_type'] == 1): ?><?php echo isset($detail['pic_path'][0]) ? $detail['pic_path'][0] :  ''; endif; endif; ?>" alt="" <?php if(isset($detail)): if($detail['posi_type'] == 1): if(!(empty($detail['pic_path'][0]) || ($detail['pic_path'][0] instanceof \think\Collection && $detail['pic_path'][0]->isEmpty()))): ?> style="display:block;width:100%;height:100%;" <?php endif; endif; endif; ?>>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- 启动页位置 -->
                            <!-- <div class="form-group col-sm-12">
                                <label class="control-label col-sm-2">请选择启动页位置：</label>
                                <div class="input-group col-sm-2 fixed_send">
                                    <select  class="form-control slt"  name="start_position">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                    </select>
                                </div>
                            </div> -->
                            <!-- 推广周期 -->
                            <div class="form-group col-sm-12">
                                <div>
                                    <label class="control-label col-sm-2">推广周期:</label>
                                    <div>
                                        <input type="text" name="start_stime" required="required" readonly='readonly' id="txtbeginDate" onclick="$.jeDate('#txtbeginDate',{insTrigger:false,isTime:true,format:'YYYY-MM-DD hh:mm:ss'})" value="<?php if(isset($detail)): if($detail['posi_type'] == 1): ?><?php echo isset($detail['circle_time_start']) ? $detail['circle_time_start'] :  ''; endif; endif; ?>" >
                                        至
                                        <input type="text" name="start_etime" readonly='readonly' id="txtEndDate" onclick="$.jeDate('#txtEndDate',{insTrigger:false,isTime:true,format:'YYYY-MM-DD hh:mm:ss'})" value="<?php if(isset($detail)): if($detail['posi_type'] == 1): ?><?php echo isset($detail['circle_time_end']) ? $detail['circle_time_end'] :  ''; endif; endif; ?>" >
                                    </div>
                                    
                                </div>
                            </div>
                            <!-- 发布状态 -->
                            <div class="form-group col-sm-12">
                                <label class="control-label col-sm-2" for="inputSuccess">发布状态:</label>
                                <div>
                                    <label class="radio-inline">
                                        <input type="radio" value="1" name="publish1" required="required" <?php if(isset($detail)): if($detail['posi_type'] == 1): if($detail['is_show'] == 1): ?>checked<?php endif; endif; else: ?>checked<?php endif; ?>> 立即发布
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" value="0" name="publish1" required="required" <?php if(isset($detail)): if($detail['posi_type'] == 1): if($detail['is_show'] == 0): ?>checked<?php endif; endif; endif; ?>> 待发布
                                    </label>
                                </div>
                            </div>
                            <div class="ss col-sm-12">
                                <button class="btn btn-primary bt" id="btn1" name="publish">提交</button>
                            </div>
                        </form>
                    </div>
                     <!-- banner发布广告 -->
                    <div class="form-group">
                        <form class="banner-form" id="commentForm2" method="post" onsubmit="return getContent('commentForm2')" <?php if(isset($detail)): if($detail['posi_type'] != 2): ?>hidden<?php endif; else: ?>hidden<?php endif; ?>>
                            <?php if(isset($detail['id'])): ?>
                            <input type="hidden" name="id" value="<?php echo isset($detail['id']) ? $detail['id'] :  ''; ?>">
                            <?php endif; ?>
                            <div class="form-group col-sm-12">
                                <div class="control-label pic-note-tip col-sm-10">
                                    <label class="control-label pic-tip col-sm-2"><label style="color:red;">*</label>上传广告图片</label>
                                    <span class="pic-note col-sm-7" style="color:red;">注：请上传750*340尺寸的jpg图片</span>
                                </div>
                                <!-- 广告图片 -->
                                <div class="control-label col-sm-10">
                                    <div class="control-label col-sm-4 news_pic" <?php if(isset($detail)): if($detail['posi_type'] == 2): if(!(empty($detail['pic_path'][0]) || ($detail['pic_path'][0] instanceof \think\Collection && $detail['pic_path'][0]->isEmpty()))): ?> style="height: 120px;"<?php endif; endif; endif; ?>>
                                        <div class="uploadfile-box uploadfile-banner" <?php if(isset($detail)): if($detail['posi_type'] == 2): if(!(empty($detail['pic_path'][0]) || ($detail['pic_path'][0] instanceof \think\Collection && $detail['pic_path'][0]->isEmpty()))): ?> style="opacity: 0;"<?php endif; endif; endif; ?>>
                                            <div class="plus-img">
                                                <img src="__IMG__/imgplus.png" alt="">
                                            </div>
                                            <div class="plus-note">点击上传图片</div>
                                            <input type="file"  class="inputstyle check_file" onchange="fileChange(this);" accept="image/*" <?php if(!isset($detail)): ?> required="required" <?php endif; ?> name="bannerfile"/>
                                        </div>
                                        <div class="pre-img pre-banner" <?php if(isset($detail)): if($detail['posi_type'] == 2): if(!(empty($detail['pic_path'][0]) || ($detail['pic_path'][0] instanceof \think\Collection && $detail['pic_path'][0]->isEmpty()))): ?> style="display:block;top:-120px;"<?php endif; endif; endif; ?>>
                                            <img src="<?php echo isset($detail['pic_path'][0]) ? $detail['pic_path'][0] :  ''; ?>" alt="" <?php if(isset($detail)): if($detail['posi_type'] == 2): if(!(empty($detail['pic_path'][0]) || ($detail['pic_path'][0] instanceof \think\Collection && $detail['pic_path'][0]->isEmpty()))): ?> style="display:block;width:100%;height:100%;" <?php endif; endif; endif; ?>>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- banner位置 -->
                            <!-- <div class="form-group col-sm-12">
                                <label class="control-label col-sm-2" for="inputSuccess">请选择banner位置:</label>
                                <div class="input-group col-sm-7 fixed_send">
                                    <select  class="form-control slt"  name="banner_position">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                    </select>
                                </div>
                            </div> -->
                            <!-- 广告形式 -->
                            <div class="form-group col-sm-12">
                                <label class="control-label col-sm-2" for="inputSuccess">广告发布形式:</label>
                                <div>
                                    <label class="radio-inline">
                                        <input class="select-ad-type" form-type="banner" type="radio" value="0" name="bner_show" required="required" <?php if(isset($detail)): if($detail['posi_type'] == 2): if($detail['state'] == 0): ?>checked<?php endif; endif; else: ?>checked<?php endif; ?>> 填写链接地址
                                    </label>
                                    <label class="radio-inline">
                                        <input class="select-ad-type" form-type="banner" type="radio" value="1" name="bner_show" required="required" <?php if(isset($detail)): if($detail['posi_type'] == 2): if($detail['state'] == 1): ?>checked<?php endif; endif; endif; ?>> 填写广告详情
                                    </label>
                                </div>
                            </div>
                            <!-- 默认链接 -->
                            <div class="hid_url">
                                <div class="form-group col-sm-12" <?php if(isset($detail)): if($detail['posi_type'] == 2): if($detail['state'] == 0): ?>style="display:block;"<?php else: ?>style="display:none;"<?php endif; endif; endif; ?>>
                                    <label class="control-label col-sm-2"><label style="color:red;">*</label>请输入链接地址:</label><input class="col-sm-5" type="text" name="baner_url" value="<?php if(isset($detail)): if($detail['posi_type'] == 2): ?><?php echo isset($detail['out_url']) ? $detail['out_url'] :  ''; endif; endif; ?>">
                                </div>
                                <div class="form-group col-sm-12" <?php if(isset($detail)): if($detail['posi_type'] == 2): if($detail['state'] == 0): ?>style="display:block;"<?php else: ?>style="display:none;"<?php endif; endif; endif; ?>>
                                    <label class="control-label col-sm-2">请输入标题:</label><input class="col-sm-5" type="text" name="baner_title" value="<?php if(isset($detail)): if($detail['posi_type'] == 2): ?><?php echo isset($detail['title']) ? $detail['title'] :  ''; endif; endif; ?>">
                                </div>
                                <!--<div class="form-group col-sm-12" <?php if(isset($detail)): if($detail['posi_type'] == 2): if($detail['state'] == 0): ?>style="display:block;"<?php else: ?>style="display:none;"<?php endif; endif; endif; ?>>-->
                                    <!--<label class="control-label col-sm-2">请选择banner位置:</label>-->
                                    <!--<select  class="form-control slt"  name="baner_sort">-->
                                        <!--<?php if(!empty($b_position)): ?>-->
                                        <!--<?php if(is_array($b_position) || $b_position instanceof \think\Collection): if( count($b_position)==0 ) : echo "" ;else: foreach($b_position as $key=>$vo): ?>-->
                                        <!--<option value="<?php echo $vo; ?>"><?php echo $vo; ?></option>-->
                                        <!--<?php endforeach; endif; else: echo "" ;endif; ?>-->
                                        <!--<?php endif; ?>-->
                                    <!--</select>-->
                                    <!--&lt;!&ndash;<input class="col-sm-5" type="text" name="baner_sort" value="<?php if(isset($detail)): if($detail['posi_type'] == 2): ?><?php echo isset($detail['sort']) ? $detail['sort'] :  ''; endif; endif; ?>">&ndash;&gt;-->
                                <!--</div>-->
                            </div>
                            <?php if(isset($detail)): if($detail['posi_type'] == 2): if($detail['state'] == 1): ?>
                            <div class="form-group col-sm-12">
                                <label class="control-label col-sm-2">标题:</label>
                                <input class="col-sm-5" type="text" name="banner_title" required="required" value="<?php if(isset($detail)): if($detail['posi_type'] == 2): if($detail['state'] == 1): ?><?php echo isset($detail['title']) ? $detail['title'] :  ''; endif; endif; endif; ?>">
                            </div>
                            <div class="form-group col-sm-12">
                                <label class="control-label col-sm-2"> 简介:</label>
                                <textarea class="col-sm-5" name="banner_summary"  cols="80" rows="3" required="required"><?php if(isset($detail)): if($detail['posi_type'] == 2): if($detail['state'] == 1): ?><?php echo isset($detail['summary']) ? $detail['summary'] :  ''; endif; endif; endif; ?></textarea>
                            </div>
                            <div class="form-group col-sm-12">
                                <label class="control-label col-sm-2">正文:</label>
                                <textarea class="col-sm-5" name="banner_cont"  cols="80" rows="6" required="required"><?php if(isset($detail)): if($detail['posi_type'] == 2): if($detail['state'] == 1): ?><?php echo isset($detail['description']) ? $detail['description'] :  ''; endif; endif; endif; ?></textarea>
                             </div>
                            <?php endif; endif; endif; ?>
                            <!-- <div class="hid_cont form-group col-sm-12"></div> -->
                            <!-- 推广周期 -->
                            <div class="form-group col-sm-12">
                                <label class="control-label col-sm-2">推广周期:</label>
                                <input type="text" required="required" name="banner_stime" readonly='readonly' id="txtbeginDate2" onclick="$.jeDate('#txtbeginDate2',{insTrigger:false,isTime:true,format:'YYYY-MM-DD hh:mm:ss'})" value="<?php if(isset($detail)): if($detail['posi_type'] == 2): ?><?php echo isset($detail['circle_time_start']) ? $detail['circle_time_start'] :  ''; endif; endif; ?>">
                                至
                                <input type="text" name="banner_etime" readonly='readonly' id="txtbeginDate3" onclick="$.jeDate('#txtbeginDate3',{insTrigger:false,isTime:true,format:'YYYY-MM-DD hh:mm:ss'})" value="<?php if(isset($detail)): if($detail['posi_type'] == 2): ?><?php echo isset($detail['circle_time_end']) ? $detail['circle_time_end'] :  ''; endif; endif; ?>">
                            </div>
                            <!-- 发布状态 -->
                            <div class="form-group col-sm-12">
                                <label class="control-label col-sm-2" for="inputSuccess">发布状态:</label>
                                <div>
                                    <label class="radio-inline">
                                        <input type="radio" value="1" name="publish2" required="required" <?php if(isset($detail)): if($detail['posi_type'] == 2): if($detail['is_show'] == 1): ?>checked<?php endif; endif; else: ?>checked<?php endif; ?>> 立即发布
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" value="0" name="publish2" required="required" <?php if(isset($detail)): if($detail['posi_type'] == 2): if($detail['is_show'] == 0): ?>checked<?php endif; endif; endif; ?>> 待发布
                                    </label>
                                </div>
                            </div>
                            <div class="ss">
                                <button class="btn btn-primary bt" id="btn2" name="publish">提交</button>
                            </div>
                        </form>
                    </div>
                    <!-- 新闻列表发布广告 -->
                    <div class="form-group">
                        <form class="newsList-form" id="commentForm3" method="post" onsubmit="return getContent('commentForm3')" <?php if(isset($detail)): if(($detail['posi_type'] != 3) AND ($detail['posi_type'] != 4)): ?>hidden<?php endif; else: ?>hidden<?php endif; ?>>
                            <input type="hidden" name="detail" id="detail" value="<?php echo isset($detail['posi_type']) ? $detail['posi_type'] :  ''; ?>">
                            <?php if(isset($detail['id'])): ?>
                            <!--<input type="hidden" name="id" value="<?php if(isset($detail)): if($detail['posi_type'] == 4): ?><?php echo isset($detail['id']) ? $detail['id'] :  ''; endif; endif; ?>">-->
                            <input type="hidden" name="id" value="<?php echo isset($detail['id']) ? $detail['id'] :  ''; ?>">
                            <?php endif; ?>
                            <!-- 广告发布样式 -->
                            <div class="form-group col-sm-12">
                                <label class="control-label col-sm-2" for="inputSuccess"><label style="color:red;">*</label>请选择广告样式:</label>
                                <div class="newsList-advertisementStyle">
                                    <!-- 纯文本 -->
                                    <label class="radio-inline style-wrap">
                                        <input class="wrap style_one adStyle-select" type="radio" value="0" name="is_style" required="required" <?php if(isset($detail)): if(($detail['posi_type'] == 3) OR ($detail['posi_type'] == 4)): if($detail['source_type'] == 1): ?>checked<?php endif; endif; else: ?>checked<?php endif; ?>> 纯文本
                                        <p class="inner">
                                            <img src="__IMG__/ad_pureText_3x.png" />
                                        </p>
                                    </label>
                                    <!-- 三图 -->
                                    <label class="radio-inline style-wrap">
                                        <input class="wrap style_one adStyle-select" type="radio" value="1" name="is_style" required="required" <?php if(isset($detail)): if(($detail['posi_type'] == 3) OR ($detail['posi_type'] == 4)): if($detail['source_type'] == 3): ?>checked<?php endif; endif; endif; ?>> 三图
                                         <p class="inner">
                                            <img src="__IMG__/ad_triPic_3x.png" />
                                        </p>
                                    </label>
                                    <!-- 单图 -->
                                    <label class="radio-inline style-wrap">
                                        <input class="wrap style_two adStyle-select" type="radio" value="2" name="is_style" required="required" <?php if(isset($detail)): if(($detail['posi_type'] == 3) OR ($detail['posi_type'] == 4)): if($detail['source_type'] == 5): ?>checked<?php endif; endif; endif; ?>> 单图(小)
                                        <p class="inner">
                                            <img src="__IMG__/ad_oneSmall_3x.png" />
                                        </p>
                                    </label>
                                    <!-- 单图或视频 -->
                                    <label class="radio-inline style-wrap">
                                        <input class="wrap style_three adStyle-select" type="radio" value="3" alt="123" name="is_style" required="required" <?php if(isset($detail)): if(($detail['posi_type'] == 3) OR ($detail['posi_type'] == 4)): if(($detail['source_type'] == 2) OR ($detail['source_type'] == 4)): ?>checked<?php endif; endif; endif; ?>> 单图或视频(大)
                                        <p class="inner">
                                            <img src="__IMG__/ad_oneBig_3x.png" />
                                        </p>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group col-sm-12 news_pic">
                                <?php if(isset($detail)): if(($detail['posi_type'] == 3) OR ($detail['posi_type'] == 4)): ?>
                                <!-- 单图大 -->
                                <?php if($detail['source_type'] == 2): ?>
                                <label class="control-label col-sm-2" style="color:red;">注：请上传750*340尺寸的jpg图片</label>
                                <div class="control-label col-sm-1 inline-block" style="height: 120px;">
                                    <div class="uploadfile-box uploadfile-vedio" style="opacity:0;">
                                        <div class="plus-img">
                                            <img src="__IMG__/imgplus.png" alt="">
                                        </div>
                                        <div class="plus-note">点击上传图片或视频</div>
                                        <input type="file"  class="inputstyle check_file" onchange="fileChange(this);" accept="image/*" name="newsfile"/>
                                        <!-- <input style="opacity: 0;height: 120px;" type="text" required="required" name="newsfile" value="<?php echo isset($detail['source_pic'][0]) ? $detail['source_pic'][0] :  ''; ?>"/> -->
                                    </div>
                                    <div class="pre-img pre-vedio" style="top:-120px;display: block;">
                                        <img src="<?php echo isset($detail['pic_path'][0]) ? $detail['pic_path'][0] :  ''; ?>" alt="" style="display:block;width:100%;height:100%;">
                                    </div>
                                </div>
                                <!-- 三图 -->
                                <?php elseif($detail['source_type'] == 3): ?>
                                <label class="control-label col-sm-2" style="color:red;">注：请上传220*140尺寸的jpg图片</label>
                                <div class="control-label col-sm-1 inline-block" style="height: 60px;">
                                    <div class="uploadfile-box uploadfile-three" style="opacity:0;">
                                        <div class="plus-img">
                                            <img src="__IMG__/imgplus.png" alt="">
                                        </div>
                                        <input type="file"  class="inputstyle check_file" onchange="fileChange(this,1);" accept="image/*" name="newsfile[]"/>
                                        <!-- <input style="opacity: 0;height: 60px;width:60px;" type="text" required="required" name="newsfile[]" value="<?php echo isset($detail['source_pic'][0]) ? $detail['source_pic'][0] :  ''; ?>"/> -->
                                    </div>
                                    <div class="pre-img pre-three1" style="top:-60px;display: block;">
                                        <img src="<?php echo isset($detail['pic_path'][0]) ? $detail['pic_path'][0] :  ''; ?>" alt="">
                                    </div>
                                </div>
                                <div class="control-label col-sm-1 inline-block" style="height: 60px;">
                                    <div class="uploadfile-box uploadfile-three" style="opacity:0;">
                                        <div class="plus-img">
                                            <img src="__IMG__/imgplus.png" alt="">
                                        </div>
                                        <input type="file"  class="inputstyle check_file" onchange="fileChange(this,2);" accept="image/*" name="newsfile[]"/>
                                        <!-- <input style="opacity: 0;height: 60px;width:60px;" type="text" required="required" name="newsfile[]" value="<?php echo isset($detail['source_pic'][1]) ? $detail['source_pic'][1] :  ''; ?>"/> -->
                                    </div>
                                    <div class="pre-img pre-three2" style="top:-60px;display: block;">
                                        <img src="<?php echo isset($detail['pic_path'][1]) ? $detail['pic_path'][1] :  ''; ?>" alt="">
                                    </div>
                                </div>
                                <div class="control-label col-sm-1 inline-block" style="height: 60px;">
                                    <div class="uploadfile-box  uploadfile-three" style="opacity:0;">
                                        <div class="plus-img">
                                            <img src="__IMG__/imgplus.png" alt="">
                                        </div>
                                        <input type="file"  class="inputstyle check_file" onchange="fileChange(this,3);" accept="image/*" name="newsfile[]"/>
                                        <!-- <input style="opacity: 0;height: 60px;width:60px;" type="text" required="required" name="newsfile[]"/ value="<?php echo isset($detail['source_pic'][2]) ? $detail['source_pic'][2] :  ''; ?>"/> -->
                                    </div>
                                    <div class="pre-img pre-three3" style="top:-60px;display: block;">
                                        <img src="<?php echo isset($detail['pic_path'][2]) ? $detail['pic_path'][2] :  ''; ?>" alt="">
                                    </div>
                                </div>
                                <!-- 视频 -->
                                <?php elseif($detail['source_type'] == 4): ?>
                                <label class="control-label col-sm-2"></label>
                                <div class="control-label col-sm-1 inline-block" style="height: 120px;">
                                    <div class="uploadfile-box uploadfile-vedio" style="opacity:0;">
                                        <div class="plus-img">
                                            <img src="__IMG__/imgplus.png" alt="">
                                        </div>
                                        <div class="plus-note">点击上传图片或视频</div>
                                        <input type="file"  class="inputstyle check_file" onchange="videoFileChange(this);" accept="image/*" name="newsfile"/>
                                        <!-- <input style="opacity: 0;height: 120px;" type="text" required="required" name="newsfile" value="<?php echo isset($detail['source_pic'][0]) ? $detail['source_pic'][0] :  ''; ?>"/> -->
                                    </div>
                                    <div class="pre-img pre-vedio" style="top:-120px;display: block;">
                                        <video src="<?php echo isset($detail['pic_path'][0]) ? $detail['pic_path'][0] :  ''; ?>" style="display:block;width:100%;height:100%;" controls="controls"></video>
                                    </div>
                                </div>
                                <!-- 单图小 -->
                                <?php elseif($detail['source_type'] == 5): ?>
                                <label class="control-label col-sm-2" style="color:red;">注：请上传140*140尺寸的jpg图片</label>
                                <div class="control-label col-sm-1 inline-block" style="height: 60px;">
                                    <div class="uploadfile-box uploadfile-three" style="opacity:0;">
                                        <div class="plus-img">
                                            <img src="__IMG__/imgplus.png" alt="">
                                        </div>
                                        <input type="file"  class="inputstyle check_file" onchange="fileChange(this);" accept="image/*" name="newsfile"/>
                                        <!-- <input style="opacity: 0;height: 60px;width:60px;" type="text" required="required" name="newsfile" value="<?php echo isset($detail['source_pic'][0]) ? $detail['source_pic'][0] :  ''; ?>"/> -->
                                    </div>
                                    <div class="pre-img pre-three" style="top:-60px;display: block;">
                                        <img src="<?php echo isset($detail['pic_path'][0]) ? $detail['pic_path'][0] :  ''; ?>" alt="" style="display:block;width:100%;height:100%;" />
                                    </div>
                                </div>
                                <?php endif; endif; endif; ?>
                                <!-- <label class="control-label col-sm-2"></label>
                                <div class="control-label col-sm-1 inline-block">
                                    <div class="uploadfile-box uploadfile-three">
                                        <div class="plus-img">
                                            <img src="__IMG__/imgplus.png" alt="">
                                        </div>
                                        <input type="file"  class="inputstyle check_file"  accept="image/*" required="required" name="newsfile[]"/>
                                    </div>
                                    <div class="pre-img pre-three">
                                        <img src="" alt="">
                                    </div>
                                </div>
                                <div class="control-label col-sm-1 inline-block">
                                    <div class="uploadfile-box uploadfile-three">
                                        <div class="plus-img">
                                            <img src="__IMG__/imgplus.png" alt="">
                                        </div>
                                        <input type="file"  class="inputstyle check_file"  accept="image/*" required="required" name="newsfile[]"/>
                                    </div>
                                    <div class="pre-img pre-three">
                                        <img src="" alt="">
                                    </div>
                                </div>
                                <div class="control-label col-sm-1 inline-block">
                                    <div class="uploadfile-box  uploadfile-three">
                                        <div class="plus-img">
                                            <img src="__IMG__/imgplus.png" alt="">
                                        </div>
                                        <input type="file"  class="inputstyle check_file"  accept="image/*" required="required" name="newsfile[]"/>
                                    </div>
                                    <div class="pre-img pre-three">
                                        <img src="" alt="">
                                    </div>
                                </div> -->
                            </div>
                            <!-- 广告发布形式 -->
                            <div class="form-group col-sm-12">
                                <label class="control-label col-sm-2" for="inputSuccess">广告发布形式:</label>
                                <div>
                                    <label class="radio-inline fixed_send">
                                        <input class="select-ad-type" form-type="newsList" type="radio" value="0" required="required" name="ad_state" <?php if(isset($detail)): if(($detail['posi_type'] == 3) OR ($detail['posi_type'] == 4)): if($detail['state'] == 0): ?>checked<?php endif; endif; else: ?>checked<?php endif; ?>> 填写链接地址
                                    </label>
                                    <label class="radio-inline fixed_send">
                                        <input class="select-ad-type" form-type="newsList" type="radio" value="1" required="required" name="ad_state" <?php if(isset($detail)): if(($detail['posi_type'] == 3) OR ($detail['posi_type'] == 4)): if($detail['state'] == 1): ?>checked<?php endif; endif; endif; ?>> 填写新闻详情
                                    </label>
                                </div>
                            </div>
                            <div class="hid_url" >
                                <div class="form-group col-sm-12" <?php if(isset($detail)): if(($detail['posi_type'] == 3) OR ($detail['posi_type'] == 4)): if($detail['state'] == 0): ?>style="display:block;"<?php else: ?>style="display:none;"<?php endif; endif; endif; ?>>
                                    <label class="control-label col-sm-2"><label style="color:red;">*</label>请输入链接地址:</label><input class="col-sm-5" type="text" name="news_url" value="<?php if(isset($detail)): if(($detail['posi_type'] == 3) OR ($detail['posi_type'] == 4)): ?><?php echo isset($detail['out_url']) ? $detail['out_url'] :  ''; endif; endif; ?>"> <a href="#" onclick="a_del()"></a>
                                </div>
                                <div class="form-group col-sm-12" <?php if(isset($detail)): if(($detail['posi_type'] == 3) OR ($detail['posi_type'] == 4)): if($detail['state'] == 0): ?>style="display:block;"<?php else: ?>style="display:none;"<?php endif; endif; endif; ?>>
                                    <label class="control-label col-sm-2">请输入标题:</label><input class="col-sm-5" type="text" name="news_title" value="<?php if(isset($detail)): if(($detail['posi_type'] == 3) OR ($detail['posi_type'] == 4)): ?><?php echo isset($detail['title']) ? $detail['title'] :  ''; endif; endif; ?>"> <a href="#" onclick="a_del()"></a>
                                </div>
                            </div>
                            <?php if(isset($detail)): if(($detail['posi_type'] == 3) OR ($detail['posi_type'] == 4)): if($detail['state'] == 1): ?>
                            <div class="form-group col-sm-12 self_">
                                <div class="form-group col-sm-12">
                                    <label class="control-label col-sm-2">标题:</label>
                                    <input class="col-sm-5" type="text" name="baner_title" required="required" value="<?php if(isset($detail)): if(($detail['posi_type'] == 3) OR ($detail['posi_type'] == 4)): if($detail['state'] == 1): ?><?php echo isset($detail['title']) ? $detail['title'] :  ''; endif; endif; endif; ?>">
                                </div>
                                <div class="form-group col-sm-12">
                                    <div class="form-group col-sm-12">
                                        <label class="control-label col-sm-2"> 简介:</label>
                                        <textarea class="col-sm-5" name="baner_summary"  cols="80" rows="3" required="required"><?php if(isset($detail)): if(($detail['posi_type'] == 3) OR ($detail['posi_type'] == 4)): if($detail['state'] == 1): ?><?php echo isset($detail['summary']) ? $detail['summary'] :  ''; endif; endif; endif; ?></textarea>
                                    </div>
                                    <div class="form-group col-sm-12">
                                        <label class="control-label col-sm-2">正文:</label>
                                         <textarea class="col-sm-5" name="baner_cont"  cols="80" rows="6" required="required"><?php if(isset($detail)): if(($detail['posi_type'] == 3) OR ($detail['posi_type'] == 4)): if($detail['state'] == 1): ?><?php echo isset($detail['description']) ? $detail['description'] :  ''; endif; endif; endif; ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <?php endif; endif; endif; ?>
                            <!-- <div class="form-group col-sm-12 hid_cont" ></div> -->
                            <!-- 推广周期 -->
                            <div class="form-group col-sm-12" >
                                <label class="control-label col-sm-2">推广周期:</label>
                                <input type="text" name="new_stime" readonly='readonly' id="txtbeginDate5" onclick="$.jeDate('#txtbeginDate5',{insTrigger:false,isTime:true,format:'YYYY-MM-DD hh:mm:ss'})" value="<?php if(isset($detail)): if(($detail['posi_type'] == 3) OR ($detail['posi_type'] == 4)): ?><?php echo isset($detail['circle_time_start']) ? $detail['circle_time_start'] :  ''; endif; endif; ?>">
                                至
                                <input type="text" name="news_etime" readonly='readonly' id="txtbeginDate4" onclick="$.jeDate('#txtbeginDate4',{insTrigger:false,isTime:true,format:'YYYY-MM-DD hh:mm:ss'})" value="<?php if(isset($detail)): if(($detail['posi_type'] == 3) OR ($detail['posi_type'] == 4)): ?><?php echo isset($detail['circle_time_end']) ? $detail['circle_time_end'] :  ''; endif; endif; ?>">
                            </div>
                            <!-- 发布状态 -->
                            <div class="form-group col-sm-12">
                                <label class="control-label col-sm-2" for="inputSuccess">发布状态:</label>
                                <div>
                                    <label class="radio-inline">
                                        <input type="radio" value="1" name="publish3" required="required" <?php if(isset($detail)): if(($detail['posi_type'] == 3) OR ($detail['posi_type'] == 4)): if($detail['is_show'] == 1): ?>checked<?php endif; endif; else: ?>checked<?php endif; ?>> 立即发布
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" value="0" name="publish3" required="required" <?php if(isset($detail)): if(($detail['posi_type'] == 3) OR ($detail['posi_type'] == 4)): if($detail['is_show'] == 0): ?>checked<?php endif; endif; endif; ?>> 待发布
                                    </label>
                                </div>
                            </div>
                            <div class="ss">
                                <button class="btn btn-primary bt" id="btn3" name="publish" type="submit">提交</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="__JS__/jquery.min.js?v=2.1.4"></script>
<script src="__JS__/img.js?v=1.0.2"></script>
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
<!--时间插件-->
<script src="__JS__/jquery.js"></script>
<script src="__JS__/jedate/jquery.jedate.js"></script>
<script type="text/javascript">
    //广告样式
    $('.style-wrap').mouseover(function() {
        var val = $(this).find('input').val();
        var inner = $('.newsList-advertisementStyle').find(".inner")[val];
        inner.style.display = 'block';
    })
    $('.style-wrap').mouseout(function() {
        var val = $(this).find('input').val();
        var inner = $('.newsList-advertisementStyle').find(".inner")[val];
        inner.style.display = 'none';
    })
    $(":radio[name='position']").change(function() {
        if(this.value == "start") {
            $(".start-form").show();
            $(".banner-form").hide();
            $(".newsList-form").hide();
        }else if(this.value == "banner") {
            $(".banner-form").show();
            $(".start-form").hide();
            $(".newsList-form").hide();
        }else if(this.value == "newsList") {
            $(".newsList-form").show();
            $(".start-form").hide();
            $(".banner-form").hide();
        }else if(this.value == "detailList") {
            $(".newsList-form").show();
            $(".start-form").hide();
            $(".banner-form").hide();
            $("#detail").val(4);
        }
    });

    //保存
    var profile_box=$("#profile_box");
    var home_box=$("#home_box");
    var about_box=$("#about_box");
    // $(".banner-form").hide();
    // $(".newsList-form").hide();


    function about(){
        $("#about_box").show();
        $("#profile_box").hide();
        $("#home_box").hide();

    }
    function home(){
        $("#home_box").show();
        $("#profile_box").hide();
        $("#about_box").hide();
    }
    function profile(){
        $("#profile_box").show();
        $("#home_box").hide();
        $("#about_box").hide();
    }



    $('.adStyle-select').change(function(){
        var _this = $(this);
        var val = _this.val();
        var content = '';
        var parent = _this.parent().parent().parent().parent();
        var news_pic  = parent.find('.news_pic');
        var child_img = news_pic.children();
        if(child_img != '' && child_img != null) {
            child_img.remove();
        }
        if(val == 1) {
            content = '<label class="control-label col-sm-2" style="color:red;">注：请上传220*140尺寸的jpg图片</label><div class="control-label col-sm-1 inline-block"><div class="uploadfile-box uploadfile-three"><div class="plus-img"><img src="__IMG__/imgplus.png" alt=""></div><input type="file" onchange="fileChange(this,1);"  class="inputstyle check_file"  accept="image/*" required="required" name="newsfile[]"/></div><div class="pre-img pre-three1"><img src="" alt=""></div></div><div class="control-label col-sm-1 inline-block"><div class="uploadfile-box uploadfile-three"><div class="plus-img"><img src="__IMG__/imgplus.png" alt=""></div><input type="file" onchange="fileChange(this,2);"  class="inputstyle check_file"  accept="image/*" required="required" name="newsfile[]"/></div><div class="pre-img pre-three2"><img src="" alt=""></div></div><div class="control-label col-sm-1 inline-block"><div class="uploadfile-box  uploadfile-three"><div class="plus-img"><img src="__IMG__/imgplus.png" alt=""></div><input type="file" onchange="fileChange(this,3);"  class="inputstyle check_file"  accept="image/*" required="required" name="newsfile[]"/></div><div class="pre-img pre-three3"><img src="" alt=""></div></div>';
        }else if(val == 2) {
            content = '<label class="control-label col-sm-2" style="color:red;">注：请上传140*140尺寸的jpg图片</label><div class="control-label col-sm-1 inline-block"><div class="uploadfile-box uploadfile-three"><div class="plus-img"><img src="__IMG__/imgplus.png" alt=""></div><input type="file" onchange="fileChange(this);" class="inputstyle check_file"  accept="image/*" required="required" name="newsfile"/></div><div class="pre-img pre-three"><img src="" alt=""></div></div>';
        }else if(val == 3) {
            content = '<label class="control-label col-sm-2" style="color:red;">注：请上传750*340尺寸的jpg图片</label><div class="control-label col-sm-1 inline-block"><div class="uploadfile-box uploadfile-vedio"><div class="plus-img"><img src="__IMG__/imgplus.png" alt=""></div><div class="plus-note">点击上传图片或视频</div><input type="file" onchange="videoFileChange(this);" class="inputstyle check_file"  accept="image/*" required="required" name="newsfile"/></div><div class="pre-img pre-vedio"><img src="" alt=""></div></div>';
        }
        news_pic.html(content)
    })
    //选择广告形式
    $('.select-ad-type').change(function(){
        var name = '';
        var content = '';
        var _this = $(this); //当前对象
        var val = $(this).val();
        var formType = _this.attr('form-type');//banner formList
        var contentObj = _this.parent().parent().parent().next();
        var child_content = contentObj.children();
        if(child_content != '' && child_content != null) {
            child_content.remove();
        }
        if(formType == 'banner') {
            name = 'baner';
        }else if(formType == 'newsList') {
            name = 'news';
        }
        if(val == 0) {
            $(".self_").children().remove();
            content = '<div class="form-group col-sm-12"><label class="control-label col-sm-2"><label style="color:red;">*</label>请输入链接地址:</label><input class="col-sm-5" type="text" name="'+name+'_url" value=""></div>' +
                '<div class="form-group col-sm-12"><label class="control-label col-sm-2">请输入标题:</label><input class="col-sm-5" type="text" name="'+name+'_title" value=""></div>';
        }else if(val == 1) {
//            content = ' <div class="form-group col-sm-12"><label class="control-label col-sm-2">标题:</label><input class="col-sm-5" type="text" name="'+name+'_title" required="required"></div><div class="form-group col-sm-12"><label class="control-label col-sm-2"> 简介:</label><textarea class="col-sm-5" name="'+name+'_summary"  cols="80" rows="3" required="required"></textarea></div><div class="form-group col-sm-12"><label class="control-label col-sm-2">正文:</label><textarea class="col-sm-5" name="'+name+'_cont"  cols="80" rows="6" required="required"></textarea></div>';
            content = ' <div class="form-group col-sm-12">'+
                            '<label class="control-label col-sm-2">标题:</label>'+
                            '<input class="col-sm-5" type="text" name="'+name+'_title" required="required" value="<?php if(isset($detail)): if($detail['posi_type'] == 3): if($detail['state'] == 1): ?><?php echo isset($detail['title']) ? $detail['title'] :  ''; endif; endif; endif; ?>">'+
                       '</div>'+
                        '<div class="form-group col-sm-12">'+
                            '<div class="form-group col-sm-12">'+
                                '<label class="control-label col-sm-2"> 简介:</label>'+
                                '<textarea class="col-sm-5" name="'+name+'_summary"  cols="80" rows="3" required="required"><?php if(isset($detail)): if($detail['posi_type'] == 3): if($detail['state'] == 1): ?><?php echo isset($detail['summary']) ? $detail['summary'] :  ''; endif; endif; endif; ?></textarea>'+
                            '</div>'+
                            '<div class="form-group col-sm-12">'+
                            '<label class="control-label col-sm-2">正文:</label>'+
                            '<textarea class="col-sm-5" name="'+name+'_cont"  cols="80" rows="6" required="required"><?php if(isset($detail)): if($detail['posi_type'] == 3): if($detail['state'] == 1): ?><?php echo isset($detail['description']) ? $detail['description'] :  ''; endif; endif; endif; ?></textarea>'+
                            '</div>'+
                        '</div>';
        }
        contentObj.append(content);
    })

    function add_start_img() {
        var s_num = $(".self_start p").length;
        if(s_num==0){
            $(".del_s").show();
        }
        $(".self_start").append('<p class="start_position"><input type="file" onchange="fileChange(this);" name="startfile[]" class="check_file"/></p>');
    }
    function del_start_img() {
        var s_num = $(".self_start p").length;
        $(".self_start p").last().remove();
        if(s_num==1){
            $(".del_s").hide();
        }
    }

    //是否可发布到banner
    function banner_n(){
        $(".banner_position").remove();
        $(".self_banner").attr("hidden","hidden");
        $(".b_y").attr('onclick','banner_y()');
    }
    function banner_y(){
        $(".self_banner").append(
            '<div style="margin-top: 10px;" class="banner_position">第 <input type="text" name="banner_sort" value="" /> 位</div>' +
            '<p style="margin-top: 10px;" class="banner_position"> 链接：' +
            '<input  type="radio" value="0" name="is_column" onclick="column_n()">否</input>' +
            '<input  type="radio" value="1" name="is_column" class="c_y" onclick="column_y()">是</input>' +
            '<input  type="text" name="banner_url" class="b_url" >' +
            '</p>'+
            '<div style="width: 300px;height: 30px;display: inline-block;margin-top: 10px;" class="banner_position">' +
            '<div style="float: right;width: 300px;">' +
            '<input style="float: left;" type="file" onchange="fileChange(this);" name="bannerfile[]" class="check_file"/>' +
            '<a style="float: right;line-height: 30px;margin-left: 20px;" href="#" onclick="add_banner_img()" ">添加</a>' +
            '<a style="float: right;line-height: 30px;" href="#" class="del_p" onclick="del_banner_img()">删除</a>' +
            '</div>'+
            '</div>'
        );
        $(".self_banner").removeAttr('hidden');
        $(".b_y").attr('onclick','banner_n()');
    }

    function add_banner_img() {
        var p_num = $(".self_banner p").length;
        if(p_num==0){
            $(".del_p").show();
        }
        $(".self_banner").append('<p class="banner_position"><input type="file" onchange="fileChange(this);" name="bannerfile[]" class="check_file"/></p>');
    }
    function del_banner_img() {
        var p_num = $(".self_banner p").length;
        $(".self_banner p").last().remove();
        if(p_num==1){
            $(".del_p").hide();
        }
    }

    //是否可发布到栏目
    function column_n(){
        $(".column_position").remove();
        $(".self_column").attr("hidden","hidden");
        $(".c_y").attr('onclick','column_y()');
    }
    function column_y(){
        $(".self_column").append(
            '<br><div class="column_position">第 <input type="text" name="column_sort" value="" /> 位</div>' +
            '<div style="width: 300px;height: 30px;display: inline-block;" class="column_position">' +
            '<div style="float: right;width: 300px;">' +
            '<br><input style="float: left;" type="file" onchange="fileChange(this);" name="columnfile[]" class="check_file"/>' +
            '<a style="float: right;line-height: 30px;margin-left: 20px;" href="#" onclick="add_column_img()" ">添加</a>' +
            '<a style="float: right;line-height: 30px;" href="#" class="del_c" onclick="del_column_img()">删除</a>' +
            '</div>'+
            '</div><br><br>'
        );
        $(".self_column").removeAttr('hidden');
        $(".c_y").attr('onclick','column_n()');
    }

    function add_column_img() {
        var c_num = $(".self_column p").length;
        if(c_num==0){
            $(".del_c").show();
        }
        $(".self_column").append('<p class="column_position"><input type="file" onchange="fileChange(this);" name="columnfile[]" class="check_file"/></p>');
    }
    function del_column_img() {
        var c_num = $(".self_column p").length;
        $(".self_column p").last().remove();
        if(c_num==1){
            $(".del_c").hide();
        }
    }
    //选择发布新闻到深度时触发
    function show_deep() {
        $("#addr").append('<p class="deep_addr"> 专题1： <input type="text" placeholder="选择深度时必填" name="zt[]" value="" class="deep_to"/> <a class="zt" href="javascript:void(0)" onclick="zt_add()">添加专题</a><span style="margin-left: 26px;" class="sp_zt">1</span>个专题</p>');
        $(".deep_from").attr('onclick','hide_deep()');
    }
    function hide_deep(){
        $(".deep_addr").remove();
        $(".deep_from").attr('onclick','show_deep()');
    }

    //添加专题
    function zt_add(){
        var zt_num = $(".deep_addr").length;
        $(".sp_zt").text(zt_num+1);
        $("#addr").append('<p class="deep_addr"> 专题'+(zt_num+1)+'： <input type="text" placeholder="专题" name="zt[]" value="" class="deep_to"/></p>');
    }


    function getContent(id){
        var jz;
        var url = "/admin/advermanage/advertisementPublish";
        var form = new FormData(document.getElementById(id));
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
                    no_submit();
                    setTimeout("javascript:location.href='./advertisementPublish'", 1000);
                }else{
                    layer.msg('编辑成功', {time : 1000});
                }

            }
        })
        return false;
    }
    function no_submit(){
        $(".btn-primary").attr("disabled","disabled");
    }

</script>
</html>



