[TOC]

### 0.api调用说明

* 测试域名：https://zqrb.stockalert.cn
* 预发布域名：暂无


* 调用每个接口时都需要验证签名,签名的生成规则如下

   1.每个请求接口中必须在header中传入timestamp(当前时间戳，精确到秒，例如：1529463231，过期时间300秒)、x-sg-agent('系统/设备号',例如:ios/DB8F13EDB-871C-4218-B37C)

   2.按照 "时间戳@设备号@scs2018Signkey"的形式连接字符串

   3.对字符串进行md5加密

   4.将参数sign随其他参数一同放在请求体中传递给接口

   ```
   例：
   对 1529480785@DB8F13EDB-871C-4218-B37C@scs2018Signkey 进行md5加密
   ```

* 每个接口都会返回msg(提示信息)和code(提示代号)和status(状态)和data(返回的数据都放在data中)

* 除了1.1.账号密码登录，1.3.发送手机短信，1.4.验证手机短信，1.7.手机改密时验证手机短信，1.8手机短信验证成功后修改密码 这些接口不需验证token外，其余接口都需要验证token和uid，token和uid会在1.1和1.4这两个登录接口中返回，之后请求其他接口时都把token以Authorization的值和uid放在header头中传过来



### 1. 用户接口

#### 1.1.登录接口

+ 地址：`https://zqrb.stockalert.cn/user`


+ HTTP请求方式: `GET` 

+ 请求参数

    | 参数        | 类型   | 必填 | 中文名         | 描述         |
    | :---------- | :----- | :--- | :------------- | :----------- |
    | phone       | string | 否   | 手机号         |              |
    | code        | string | 否   | 验证码         |              |
    | wx_openid   | string | 否   | 微信openID     |              |
    | qq_openid   | string | 否   | QQopenID       |              |
    | sina_openid | string | 否   | 新浪微博openID |              |
    | nickname    | string | 否   | 第三方昵称     |              |
    | avatar      | string | 否   | 第三方头像     |              |
    | sex         | string | 否   | 第三方性别     | 男、女、未知 |
    | birthday    | string | 否   | 第三方生日     |              |
    | sign        | string | 是   | 加密签名       |              |

    注：如果用户使用手机号登录，则验证码必填

+ 返回格式(json),以下为data中的数据

    | 参数     | 类型   | 默认值 | 中文名        | 描述 |
    | :------- | :----- | :----- | :------------ | :--- |
    | token    | string |        | 用户的token   |      |
    | id       | int    |        | 用户的id      |      |
    | nickname | string |        | 用户名        |      |
    | avatar   | string |        | 用户头像的url |      |
    | phone    | String |        | 用户手机号    |      |
    | sex      | string |        | 用户性别      |      |
    | birthday | string |        | 用户生日      |      |

- 返回：

```
{
    "code": 200,
    "msg": "操作成功",
    "status": "OK",
    "data": {
        "user_id": 1,
        "nickname": "aaaa",
        "token": "IJ6n654933",
        "avatar": null,
        "phone": "17719032010",
        "sex": "男",
        "birthday": null
    }
}
```

#### 1.2发送手机短信

- 地址：`https://zqrb.stockalert.cn/sendCode`
- HTTP请求方式：`POST`
- 请求参数

| 参数  | 类型   | 默认值 | 中文名   | 描述 |
| ----- | ------ | ------ | -------- | ---- |
| phone | string |        | 手机号码 |      |
| sign  | string |        | 加密签名 |      |

- 返回格式(json)

```
{
    "code": 200,
    "msg": "操作成功",
    "status": "OK",
    "data": {
        "code": 211918
    }
}
```

#### 1.3首页栏目接口

- 地址：`https://zqrb.stockalert.cn/userColumn`

- HTTP请求方式：`GET`

- 请求参数

  | 参数   | 类型   | 默认值 | 中文名   | 描述                                          |
  | ------ | ------ | ------ | -------- | --------------------------------------------- |
  | action | string |        | 动作     | 参数:"index"首页栏目列表,"more"更多页栏目列表 |
  | sign   | string |        | 加密签名 |                                               |


- 注：若用户已登录，则在header中传入uid和token

- 返回数据

| 参数      | 类型   | 默认值 | 中文名   | 描述                                                  |
| --------- | ------ | ------ | -------- | ----------------------------------------------------- |
| leftList  | Array  |        | 栏目列表 | 底部用户未选择的栏目(在action=more的时候才有这个数据) |
| topList   | Array  |        | 栏目列表 | 顶部用户已选择的栏目和固定栏目                        |
| column_id | String |        | 栏目id   |                                                       |
| title     | string |        | 栏目标题 |                                                       |
| is_fixed  | int    |        | 是否固定 | 0 :否,1是                                             |



- 返回格式(json)

```
{
    "code": 200,
    "msg": "操作成功",
    "status": "OK",
    "data": {
        "leftList": [   //底部用户未选择的栏目(在action=more的时候才有这个数据)
            {
                "column_id": 3,
                "title": "快讯",
                "is_fixed": 0
            }
        ],
        "topList": [   //顶部用户已选择的栏目和固定栏目
            {
                "column_id": 1,
                "title": "首页",
                "is_fixed": 1
            },
            {
                "column_id": 2,
                "title": "热点",
                "is_fixed": 1
            },
            {
                "column_id": 4,
                "title": "深度",
                "is_fixed": 0
            },
            {
                "column_id": 5,
                "title": "电子报",
                "is_fixed": 0
            },
            {
                "column_id": 6,
                "title": "财经",
                "is_fixed": 0
            }
        ]
    }
}

```

#### 1.4栏目用户编辑接口

- 地址：`https://zqrb.stockalert.cn/userColumn`

- HTTP请求方式：`POST`

- 请求参数

  | 参数       | 类型   | 默认值 | 中文名       | 描述               |
  | ---------- | ------ | ------ | ------------ | ------------------ |
  | column_ids | string |        | 栏目id字符串 | 例如:"1,2,3,6,4,5" |
  | sign       | string |        | 加密签名     |                    |


- 注：需在header中传入uid和Authorization，注意column_ids中值的顺序即为用户选择的顺序。

- 返回数据

| 参数   | 类型   | 默认值 | 中文名   | 描述                     |
| ------ | ------ | ------ | -------- | ------------------------ |
| code   | Int    |        | 状态码   | 200表示成功 -100代表失败 |
| msg    | String |        | 提示信息 |                          |
| status | String |        | 状态值   | OK 或者 FAIL             |



- 返回格式(json)

```
{
    "code": 200,
    "msg": "操作成功",
    "status": "OK"
}
```


#### 1.5用户登出接口

- 地址：`https://zqrb.stockalert.cn/logout`

- HTTP请求方式：`GET`

- 请求参数

  | 参数       | 类型   | 默认值 | 中文名       | 描述               |
  | ---------- | ------ | ------ | ------------ | ------------------ |
  | sign       | string |        | 加密签名     |                    |


- 注：需在header中传入uid和Authorization。

- 返回数据

| 参数   | 类型   | 默认值 | 中文名   | 描述                     |
| ------ | ------ | ------ | -------- | ------------------------ |
| code   | Int    |        | 状态码   | 200表示成功 -100代表失败 |
| msg    | String |        | 提示信息 |                          |
| status | String |        | 状态值   | OK 或者 FAIL             |



- 返回格式(json)

```
{
    "code": 200,
    "msg": "退出成功",
    "status": "OK"
}
```

#### 1.6首页新闻列表接口

- 地址：`https://zqrb.stockalert.cn/news`

- HTTP请求方式：`GET`

- 请求参数

  | 参数        | 类型   | 默认值 | 中文名             | 描述                                |
  | ----------- | ------ | ------ | ------------------ | ----------------------------------- |
  | sign        | string |        | 加密签名           |                                     |
  | offset      | Int    | 5      | 每页显示的数量     |                                     |
  | min_news_id | int    |        | 当前页最小的新闻id | 第一页可不填此数据                  |
  | column_id   | Int    | 1      | 栏目ID             |                                     |
  | action      | String |        | 列表动作           | 值为hot 时为热点新闻,其他情况可不传 |
  | page        | Int    |        | 页码               |                                     |



- 返回数据

| 参数              | 类型   | 默认值 | 中文名       | 描述                                                         |
| ----------------- | ------ | ------ | ------------ | ------------------------------------------------------------ |
| code              | Int    |        | 状态码       | 200表示成功 -100代表失败                                     |
| msg               | String |        | 提示信息     |                                                              |
| status            | String |        | 状态值       | OK 或者 FAIL                                                 |
| data              | Array  |        | 数据包       |                                                              |
| news_id           | Int    |        | 新闻id       |                                                              |
| title             | String |        | 新闻标题     |                                                              |
| summary           | Text   |        | 新闻摘要     |                                                              |
| comments_num      | int    |        | 评论数目     |                                                              |
| publish_time      | string |        | 发布时间     |                                                              |
| source_type       | int    |        | 资源类型     | 0:栏目列表,1:纯文本,2:单图(长方形),3多图,4:视频,5:单图(正方形) |
| detail_type       | Int    |        | 详情显示方式 | 1 正常文本流 2 横向滚动文本流                                |
| columns           | Array  |        | 栏目数组集合 |                                                              |
| columns[ "title"] | string |        | 栏目标题     |                                                              |
| listSource        | Array  |        | 新闻资源     | 列表图片 或 视屏,或栏目单图                                  |



- 返回格式(json)

```
{
    "code": 200,
    "msg": "操作成功",
    "status": "OK",
    "data": [
        {
            "news_id": 11,
            "title": "中石油无偿划转19.4亿股 或是混改前奏",
            "detail": "昨日晚间，中石油发布公告称，公司于2018年6月7日接到公司控股股东中国石油天然气集团有限公司（以下简称“中国石油集团”）通知，经国务院国有资产监督管理委员会批准，中国石油集团拟将其持有的公司9.7亿股A股股份（约占公司总股本的0.53%）无偿划转给北京诚通金控投资有限公司（以下简称“诚通金控”），9.7亿股A股股份（约占公司总股本的0.53%）无偿划转给国新投资有限公司（以下简称“国新投资”）。",
            "comments_num": 0,
            "publish_time": "2018-06-07 15:45:55",
            "source_type": 1,
            "detail_type": 1,
            "listSource": [],
            "columns": [
                {
                    "id": 0,
                    "title": "热点",
                    "parent_id": 0
                },
                {
                    "id": 1,
                    "title": "新闻",
                    "parent_id": 0
                }
            ]
        },
        {
            "news_id": 6,
            "title": "中石油无偿划转19.4亿股 或是混改前奏",
            "detail": "昨日晚间，中石油发布公告称，公司于2018年6月7日接到公司控股股东中国石油天然气集团有限公司（以下简称“中国石油集团”）通知，经国务院国有资产监督管理委员会批准，中国石油集团拟将其持有的公司9.7亿股A股股份（约占公司总股本的0.53%）无偿划转给北京诚通金控投资有限公司（以下简称“诚通金控”），9.7亿股A股股份（约占公司总股本的0.53%）无偿划转给国新投资有限公司（以下简称“国新投资”）。",
            "comments_num": 0,
            "publish_time": "2018-05-28 16:15:18",
            "source_type": 2,
            "detail_type": 1,
            "listSource": [
                {
                    "id": 5,
                    "news_id": 6,
                    "source_path": "https://www.zqrb.cn/2018-05-31/img_15975598815b0f436b8664e.jpg"
                }
            ],
            "columns": [
                {
                    "id": 0,
                    "title": "热点",
                    "parent_id": 0
                },
                {
                    "id": 1,
                    "title": "新闻",
                    "parent_id": 0
                }
            ]
        }
    ]
}
```

#### 1.7新闻搜索接口

- 地址：`https://zqrb.stockalert.cn/newsSearch`

- HTTP请求方式：`GET`

- 请求参数

  | 参数        | 类型   | 默认值 | 中文名             | 描述               |
  | ----------- | ------ | ------ | ------------------ | ------------------ |
  | sign        | string |        | 加密签名           |                    |
  | offset      | Int    | 5      | 每页显示的数量     |                    |
  | min_news_id | int    |        | 当前页最小的新闻id | 第一页可不填此数据 |
  | keywords    | String | 1      | 关键词             |                    |



- 返回数据

| 参数                 | 类型   | 默认值 | 中文名       | 描述                         |
| -------------------- | ------ | ------ | ------------ | ---------------------------- |
| code                 | Int    |        | 状态码       | 200表示成功 -100代表失败     |
| msg                  | String |        | 提示信息     |                              |
| status               | String |        | 状态值       | OK 或者 FAIL                 |
| data                 | Array  |        | 数据包       |                              |
| news_id              | Int    |        | 新闻id       |                              |
| title                | String |        | 新闻标题     |                              |
| detail               | Text   |        | 新闻详情     |                              |
| comments_num         | int    |        | 评论数目     |                              |
| publish_time         | string |        | 发布时间     |                              |
| source_type          | int    |        | 资源类型     | 1:纯文本,2:单图,3多图,4:视频 |
| column_ids           | Array  |        | 栏目数组集合 |                              |
| column_ids[ "title"] | string |        | 栏目标题     |                              |
| listSource           | Array  |        | 新闻资源     | 列表图片 或 视屏             |



- 返回格式(json)

```
同新闻列表接口
```

#### 1.8首页banner列表接口

- 地址：`https://zqrb.stockalert.cn/bannerList`

- HTTP请求方式：`POST`

- 请求参数

  | 参数        | 类型   | 默认值 | 中文名                     | 描述               |
  | ----------- | ------ | ------ | -------------------------- | ------------------ |
  | sign        | string |        | 加密签名                   |                    |



- 返回数据

| 参数                 | 类型   | 默认值 | 中文名       | 描述                         |
| -------------------- | ------ | ------ | ------------ | ---------------------------- |
| code                 | Int    |        | 状态码       | 200表示成功 -100代表失败     |
| msg                  | String |        | 提示信息     |                              |
| status               | String |        | 状态值       | OK 或者 FAIL                 |
| data                 | Array  |        | 数据包       |                              |
| out_id               | Int    |        | 广告或新闻id |                              |
| url                  | String |        | 链接        |广告跳转链接                              |
| picture_path         | String |        | 图片地址     |                              |
| type                 | Int    |        | banner类型     |   0:新闻,1:广告                            |
| title                | String |        | banner标题       |  |
| adv_type             | Int    |        | 广告跳转类型 |1：链接跳转；2：根据out_id请求广告详情接口                              |



- 返回格式(json)

```
{
    "code": 200,
    "msg": "操作成功",
    "status": "OK",
    "data": [
        {
            "id": 1,
            "out_id": 1,
            "url": "https://www.baidu.com",
            "picture_path": "public/img",
            "type": 1,
            "sort": 1,
            "title": "广告测试",
            "adv_type": 1
        },
        {
            "id": 3,
            "out_id": 2,
            "url": "sjdk",
            "picture_path": "dsjf",
            "type": 1,
            "sort": 2,
            "title": "广告测试2",
            "adv_type": 1
        },
        {
            "id": 2,
            "out_id": 1,
            "url": "sdn",
            "picture_path": "sdj",
            "type": 0,
            "sort": 3,
            "title": "证监会：严格掌握试点企业家数和筹资数量"
        }
    ]
}
```


#### 1.9广告列表接口

- 地址：`https://zqrb.stockalert.cn/adverList`

- HTTP请求方式：`POST`

- 请求参数 

  | 参数        | 类型   | 默认值 | 中文名                     | 描述               |
  | ----------- | ------ | ------ | -------------------------- | ------------------ |
  | sign        | string |        | 加密签名                   |                    |
  | posi_type   | string |        | 位置类型                   |1：起始页；4：新闻详情   |



- 返回数据

| 参数                 | 类型   | 默认值 | 中文名       | 描述                         |
| -------------------- | ------ | ------ | ------------ | ---------------------------- |
| code                 | Int    |        | 状态码       | 200表示成功 -100代表失败     |
| msg                  | String |        | 提示信息     |                              |
| status               | String |        | 状态值       | OK 或者 FAIL                 |
| data                 | Array  |        | 数据包       |                              |
| id                   | Int    |        | 广告id       |                              |
| title                | String |        | 标题        |                              |
| position             | Int    |        | 广告定位     |                              |
| out_url              | Str    |        | 广告外链     |                               |
| source_type          | Str    |        | 资源类型     | 1:纯文本,2:单图(长方形),3多图,4:视频,5:单图(正方形)   |
| state                | int    |        | 跳转方式     | 0链接,1详情                 |
| pic                  | array  |        | 图片地址     |                              |
| pic_path             | String |        | 图片地址    |                             |
| pic_time             | int    |        | 起始页显示时间    |  （传1时存在）            |
| time                 | String |        | 时间     |（直接显示）                    |



- 返回格式(json)

```
{
    "code": 200,
    "msg": "操作成功",
    "status": "OK",
    "data": [
        {
            "id": 9,
            "title": "",
            "position": 1,
            "out_url": "",
            "source_type": 2,
            "update_time": 1530864274,
            "state": 0,
            "pic": [
                {
                    "pic_path": "http://oss-cn-hangzhou.aliyuncs.com/zhengquanrb/serverData/advertising/2018070609540019761.jpg"
                }
            ],
            "pic_time": 3,
            "time": "2018-07-06"
        }
    ]
}
```

#### 2.0广告详情接口

- 地址：`https://zqrb.stockalert.cn/advertisements`

- HTTP请求方式：`POST`

- 请求参数 

  | 参数        | 类型   | 默认值 | 中文名                     | 描述               |
  | ----------- | ------ | ------ | -------------------------- | ------------------ |
  | sign        | string |        | 加密签名                   |                    |
  | id          | Int    |        | 广告id                   |            |

- 返回数据

| 参数                 | 类型   | 默认值 | 中文名       | 描述                         |
| -------------------- | ------ | ------ | ------------ | ---------------------------- |
| code                 | Int    |        | 状态码       | 200表示成功 -100代表失败     |
| msg                  | String |        | 提示信息     |                              |
| status               | String |        | 状态值       | OK 或者 FAIL                 |
| data                 | Array  |        | 数据包       |                              |
| id                   | Int    |        | 广告id       |                              |
| title                | String |        | 标题        |                              |
| detail          | String |        | 内容        |                              |
| out_url              | String |        | 广告外链     |                               |
| publish_time          | String |        | 时间        |                              |
| auther               | String |        | 作者        |                             |
| source               | String |        | 来源        |                             |
| source_type          | Int    |        | 新闻资源类型 |广告资源类型 1:纯文本,2:单图(长方形),3多图,4:视频,5:单图(正方形) |
| top_pic               | String |        | 图片地址        |                             |
| video               | String |        | 视屏地址        |  当source_type是4的时候                           |

- 返回格式(json)

```
{
    "code": 200,
    "msg": "操作成功",
    "status": "OK",
    "data": {
        "id": 10,
        "title": "泉州模式：“海上丝绸之路”起点的大跨越",
        "auther": "证券日报",
        "source": "人民日报",
        "detail": "    民营经济与外向型经济互相促进为泉州模式最大特色，《证券日报》记者在泉州调研10余天，充分感受到了小城市也有大作为的新时代气息",
        "publish_time": "2018-07-19 02:43",
        "source_type": 2,
        "top_pic": "http://oss-cn-hangzhou.aliyuncs.com/zhengquanrb/serverData/advertising/2018071810180640718.jpg",
        "video": ""
    }
}
```
#### 2.1新闻详情接口

- author : `An`

- 地址：`https://zqrb.stockalert.cn/news/:id`

- HTTP请求方式：`GET`

- 请求参数

  | 参数        | 类型   | 默认值 | 中文名                     | 描述               |
  | ----------- | ------ | ------ | -------------------------- | ------------------ |
  | sign        | string |        | 加密签名                   |                    |


注释：':id'代表新闻id，生成sign时仍需将id=:id参与加密

- 返回数据

| 参数                 | 类型   | 默认值 | 中文名       | 描述                         |
| -------------------- | ------ | ------ | ------------ | ---------------------------- |
| code                 | Int    |        | 状态码       | 200表示成功 -100代表失败     |
| msg                  | String |        | 提示信息     |                              |
| status               | String |        | 状态值       | OK 或者 FAIL                 |
| data                 | Array  |        | 数据包       |                              |
| news_id     | Int    |        | 新闻id |                              |
| title              | String |        | 新闻标题   |                              |
| author | String |        | 新闻作者 |                              |
| source             | String |        | 新闻来源 |   0:新闻,1:广告                            |
| audio | String | | 音频地址 |  |
| publish_time     | String |        | 新闻发布时间 |  |
| praise_num  | Int    |        | 点赞量 |                              |
| comments_num | Int | | 评论量 | |
| collect_num | Int | | 收藏量 | |
| allow_comment | int | | 是否允许可评论 |0：否  1：可以 |
| allow_transmit | int | | 是否允许可转发 |0：否  1：可以 |
| allow_ad | int | | 是否可插入广告 |0：否  1：可以 |
| source_type | Int | | 新闻资源(图片、视频)的类型 |当值为2时，值为4时为视频文件地址, top_pic不为空,并将其显示到详情页顶部， |
| detail_type | Int | | 新闻详情文本流类型 |1 正常文本流 2 横向滚动文本流 |
| is_praise | string | | 点赞状态 |1 已点赞 0 未点赞 |
| is_collection | string | | 收藏状态 |1 已收藏 0 未收藏 |
| top_pic | string | | 详情页顶部图 | |
| sourceList | array | | 横向文本流资源列表 | |
| sourceList【0】['source_id'] | Int | | 资源id |  |
| sourceList【0】['source_path'] | String | | 图片路径 | |
| sourceList【0】['detail'] | text | | 图片详情 | |
| related_news | Array | | 相关新闻列表 | |
| related_news[0].['news_id'] | Int | | 新闻id | |
| related_news[0].['title'] | string | | 新闻题目 | |
| related_news[0].['detail'] | text | | 新闻详情 | |
| related_news[0].['column_ids'] | array | | 新闻栏目 | 同新闻列表 |
| related_news[0].['listSource'] | array | | 新闻资源 | 同新闻列表 |



- 返回格式(json)

```
{
    "code": 200,
    "msg": "操作成功",
    "status": "OK",
    "data": {
        "news_id": 2,
        "title": "央行收短放长 短期置换式降准概率低",
        "author": "author2",
        "source": "source2",
        "publish_time": "2018-05-28 16:15:18",
        "detail": "本次无偿划转前，中国石油集团持有公司1511亿股A股股份，约占公司总股本的82.55%。本次无偿划转后，中国石油集团持有公司1491亿股A股股份，约占公司总股本的81.49%；诚通金控将持有公司9.7亿股A股股份，约占公司总股本的0.53%，国新投资将持有公司9.7亿股A股股份，约占公司总股本的0.53%。本次无偿划转不会导致公司的控股股东及实际控制人发生变更。",
        "praise_num": 1,
        "comments_num": 0,
        "collect_num": 0,
        "allow_comment": 1,
        "allow_transmit": 1,
        "allow_ad": 0,
        "source_type": 2,
        "detail_type": 2,
        "is_recommend": 1,
        "is_praise": 0,
        "is_collection": 0,
        "top_pic": "https://www.zqrb.cn/2018-05-31/img_15975598815b0f436b8664e.jpg",
        "sourceList": [
            {
                "source_id": 22,
                "source_path": "http://oss-cn-hangzhou.aliyuncs.com/zhengquanrb/serverData/advertising/2018070413491787136.jpg",
                "detail": "图一"
            },
            {
                "source_id": 23,
                "source_path": "http://oss-cn-hangzhou.aliyuncs.com/zhengquanrb/serverData/advertising/2018070413491787136.jpg",
                "detail": "图二"
            },
            {
                "source_id": 24,
                "source_path": "http://oss-cn-hangzhou.aliyuncs.com/zhengquanrb/serverData/advertising/2018070413491787136.jpg",
                "detail": "图三"
            }
        ],
        "related_news": [
            {
                "news_id": 5,
                "title": "中石油无偿划转19.4亿股 或是混改前奏",
                "detail": "昨日晚间，中石油发布公告称，公司于2018年6月7日接到公司控股股东中国石油天然气集团有限公司（以下简称“中国石油集团”）通知，经国务院国有资产监督管理委员会批准，中国石油集团拟将其持有的公司9.7亿股A股股份（约占公司总股本的0.53%）无偿划转给北京诚通金控投资有限公司（以下简称“诚通金控”），9.7亿股A股股份（约占公司总股本的0.53%）无偿划转给国新投资有限公司（以下简称“国新投资”）。",
                "comments_num": 0,
                "publish_time": "2018-05-28 16:15:18",
                "source_type": 2,
                "column_ids": [
                    {
                        "title": "财经"
                    },
                    {
                        "title": "电子报"
                    }
                ],
                "listSource": [
                    {
                        "id": 6,
                        "news_id": 5,
                        "source_path": "https://www.zqrb.cn/2018-05-31/img_15975598815b0f436b8664e.jpg"
                    }
                ]
            },
            {
                "news_id": 4,
                "title": "中石油无偿划转19.4亿股 或是混改前奏",
                "detail": "昨日晚间，中石油发布公告称，公司于2018年6月7日接到公司控股股东中国石油天然气集团有限公司（以下简称“中国石油集团”）通知，经国务院国有资产监督管理委员会批准，中国石油集团拟将其持有的公司9.7亿股A股股份（约占公司总股本的0.53%）无偿划转给北京诚通金控投资有限公司（以下简称“诚通金控”），9.7亿股A股股份（约占公司总股本的0.53%）无偿划转给国新投资有限公司（以下简称“国新投资”）。",
                "comments_num": 0,
                "publish_time": "2018-05-28 16:15:18",
                "source_type": 4,
                "column_ids": [
                    {
                        "title": "新闻"
                    }
                ],
                "listSource": []
            },
            {
                "news_id": 3,
                "title": "中石油无偿划转19.4亿股 或是混改前奏",
                "detail": "昨日晚间，中石油发布公告称，公司于2018年6月7日接到公司控股股东中国石油天然气集团有限公司（以下简称“中国石油集团”）通知，经国务院国有资产监督管理委员会批准，中国石油集团拟将其持有的公司9.7亿股A股股份（约占公司总股本的0.53%）无偿划转给北京诚通金控投资有限公司（以下简称“诚通金控”），9.7亿股A股股份（约占公司总股本的0.53%）无偿划转给国新投资有限公司（以下简称“国新投资”）。",
                "comments_num": 0,
                "publish_time": "2018-05-28 16:15:18",
                "source_type": 1,
                "column_ids": [],
                "listSource": []
            }
        ]
    }
}
```

#### 2.2用户新闻点赞接口

- author : `An`

- 地址：`https://zqrb.stockalert.cn/userPraise`

- HTTP请求方式：`POST`

- 请求参数

  | 参数    | 类型   | 默认值 | 中文名   | 描述               |
  | ------- | ------ | ------ | -------- | ------------------ |
  | sign    | string |        | 加密签名 |                    |
  | news_id | Int    |        | 新闻id   |                    |
  | status  | int    |        | 点赞状态 | 1  取消点赞 ;0点赞 |




- 返回数据

| 参数                 | 类型   | 默认值 | 中文名       | 描述                         |
| -------------------- | ------ | ------ | ------------ | ---------------------------- |
| code                 | Int    |        | 状态码       | 200表示成功 -100代表失败     |
| msg                  | String |        | 提示信息     |                              |
| status               | String |        | 状态值       | OK 或者 FAIL                 |

注：需在header中传入uid和Authorization。

- 返回格式(json)

```
{
    "code": 200,
    "msg": "操作成功",
    "status": "OK"
}
```

#### 2.3用户收藏和取消收藏接口

- author : `Wang`

- 地址：`https://zqrb.stockalert.cn/collection`

- HTTP请求方式：`POST`

- 请求参数

  | 参数    | 类型   | 是否必传 | 中文名   | 描述             |
  | ------- | ------ | ------ | -------- | ---------------- |
  | sign    | string |   Y     | 加密签名 |                  |
  | user_id | Int    |   Y     | 用户id   |                  |
  | status  | int    |   Y     | 收藏状态  | 0：未收藏；1：以收藏  (传0收藏，传1取消收藏) |
  | type    | Int    |   Y     | 收藏类型   |   0:普通新闻,1:快讯                |
  | id | Int    |   Y     | id   |   新闻id或快讯id               |
注：需在header中传入token和uid。



- 返回数据

| 参数                 | 类型   | 默认值 | 中文名       | 描述                         |
| -------------------- | ------ | ------ | ------------ | ---------------------------- |
| code                 | Int    |        | 状态码       | 200表示成功 -100代表失败     |
| msg                  | String |        | 提示信息     |                              |
| status               | String |        | 状态值       | OK 或者 FAIL                 |



- 返回格式(json)

```
{
    "code": 200,
    "msg": "收藏成功！",
    "status": "OK"
}
```

#### 2.4快讯接口

- author : `Wang`

- 地址：`https://zqrb.stockalert.cn/newsFlash`

- HTTP请求方式：`GET`

- 请求参数

  | 参数    | 类型   | 是否必传 | 中文名   | 描述             |
  | ------- | ------ | ------ | -------- | ---------------- |
  | sign    | string |   Y     | 加密签名 |                  |
  | page  | int    |   N     | 第几页  | 不传默认第一页 |
  | pageSize    | int    |   N     | 一页显示多少条   |  不传默认10条               |
注：如果用户登陆需在header中传入token和uid。


- 返回数据

| 参数                 | 类型   | 默认值 | 中文名       | 描述                         |
| -------------------- | ------ | ------ | ------------ | ---------------------------- |
| code                 | Int    |        | 状态码       | 200表示成功 -100代表失败     |
| msg                  | String |        | 提示信息     |                              |
| status               | String |        | 状态值       | OK 或者 FAIL                 |
| id                   | int |        | 快讯id       | （收藏接口需传）                 |
| type                   | string |        | 类型       |    |
| title               | String |        | 标题       |                  |
| summary               | String |        | 状态值       | 内容                 |
| update_time            | String |        | 日期       |                  |
| status               | String |        | 收藏状态       |   1：已收藏；0：未收藏               |

- 返回格式(json)

```
{
    "code": 200,
    "msg": "操作成功",
    "status": "OK",
    "data": [
        {
            "id": 17,
            "type": "研报",
            "title": "6",
            "summary": "56",
            "create_time": "2018-06-21 05:40",
            "is_delete": 0,
            "is_show": 1,
            "update_time": "2018-06-22 05:13",
            "status": 0
        },
        {
            "id": 16,
            "type": "股吧",
            "title": "5",
            "summary": "5",
            "create_time": "2018-06-21 05:39",
            "is_delete": 0,
            "is_show": 1,
            "update_time": "2018-06-22 05:13",
            "status": 0
        },
        {
            "id": 15,
            "type": "微信",
            "title": "撒都会疯狂的精神焕发",
            "summary": "但还是飞机可视电话",
            "create_time": "2018-06-21 05:39",
            "is_delete": 0,
            "is_show": 1,
            "update_time": "2018-06-22 05:13",
            "status": 0
        },
        {
            "id": 14,
            "type": "股吧",
            "title": "撒都会疯狂的精神焕发",
            "summary": "但还是飞机可视电话",
            "create_time": "2018-06-15 05:50",
            "is_delete": 0,
            "is_show": 1,
            "update_time": "2018-06-12 02:08",
            "status": 0
        },
        {
            "id": 13,
            "type": "微博",
            "title": "撒都会疯狂的精神焕发",
            "summary": "但还是飞机可视电话",
            "create_time": "2018-06-15 05:49",
            "is_delete": 0,
            "is_show": 1,
            "update_time": "2018-06-12 01:29",
            "status": 0
        },
        {
            "id": 12,
            "type": "股吧",
            "title": "撒都会疯狂的精神焕发",
            "summary": "但还是飞机可视电话",
            "create_time": "2018-06-15 05:10",
            "is_delete": 0,
            "is_show": 1,
            "update_time": "2018-06-12 02:08",
            "status": 0
        },
        {
            "id": 11,
            "type": "研报",
            "title": "撒都会疯狂的精神焕发",
            "summary": "但还是飞机可视电话",
            "create_time": "2018-06-15 05:09",
            "is_delete": 0,
            "is_show": 1,
            "update_time": "2018-06-12 02:08",
            "status": 0
        },
        {
            "id": 10,
            "type": "股吧",
            "title": "撒都会疯狂的精神焕发",
            "summary": "但还是飞机可视电话",
            "create_time": "2018-06-12 06:03",
            "is_delete": 0,
            "is_show": 1,
            "update_time": "2018-06-12 02:08",
            "status": 0
        },
        {
            "id": 9,
            "type": "微信",
            "title": "撒都会疯狂的精神焕发",
            "summary": "但还是飞机可视电话",
            "create_time": "2018-06-12 05:07",
            "is_delete": 0,
            "is_show": 1,
            "update_time": "2018-06-12 02:08",
            "status": 0
        },
        {
            "id": 8,
            "type": "股吧",
            "title": "圣诞节发空间哦1234567",
            "summary": "隔日 u 个 i 哦手机发代购 i 就是对佛 i 感觉哦 i",
            "create_time": "2018-06-12 02:08",
            "is_delete": 0,
            "is_show": 1,
            "update_time": "2018-06-12 01:29",
            "status": 0
        },
        {
            "id": 6,
            "type": "研报",
            "title": "圣诞节发空间哦12345",
            "summary": "隔日 u 个 i 哦手机发代购 i 就是对佛 i 感觉哦 i",
            "create_time": "2018-06-12 02:08",
            "is_delete": 0,
            "is_show": 1,
            "update_time": "2018-06-12 01:29",
            "status": 0
        },
        {
            "id": 5,
            "type": "微博",
            "title": "圣诞节发空间哦1234",
            "summary": "隔日 u 个 i 哦手机发代购 i 就是对佛 i 感觉哦 i",
            "create_time": "2018-06-12 02:08",
            "is_delete": 0,
            "is_show": 0,
            "update_time": "2018-06-12 02:08",
            "status": 1
        },
        {
            "id": 4,
            "type": "电报",
            "title": "圣诞节发空间哦123",
            "summary": "隔日 u 个 i 哦手机发代购 i 就是对佛 i 感觉哦 i",
            "create_time": "2018-06-12 02:08",
            "is_delete": 0,
            "is_show": 1,
            "update_time": "2018-06-12 02:08",
            "status": 1
        },
        {
            "id": 3,
            "type": "股吧",
            "title": "圣诞节发空间哦12",
            "summary": "隔日 u 个 i 哦手机发代购 i 就是对佛 i 感觉哦 i",
            "create_time": "2018-06-12 02:08",
            "is_delete": 0,
            "is_show": 1,
            "update_time": "2018-06-12 02:08",
            "status": 0
        },
        {
            "id": 2,
            "type": "微信",
            "title": "圣诞节发空间哦1",
            "summary": "隔日 u 个 i 哦手机发代购 i 就是对佛 i 感觉哦 i",
            "create_time": "2018-06-12 02:08",
            "is_delete": 0,
            "is_show": 1,
            "update_time": "2018-06-12 06:03",
            "status": 1
        },
        {
            "id": 1,
            "type": "股吧",
            "title": "圣诞节发空间哦",
            "summary": "隔日 u 个 i 哦手机发代购 i 就是对佛 i 感觉哦 i",
            "create_time": "2018-06-12 01:29",
            "is_delete": 0,
            "is_show": 1,
            "update_time": "2018-06-12 06:03",
            "status": 0
        }
    ]
}
```
#### 2.5评论列表接口

- author : `Angz`

- 地址：`https://zqrb.stockalert.cn/comment`

- HTTP请求方式：`GET`

- 请求参数

  | 参数    | 类型   | 是否必传 | 中文名   | 描述             |
  | ------- | ------ | ------ | -------- | ---------------- |
  | sign    | string |   Y     | 加密签名 |                  |
  | news_id | int    |   Y     | 新闻id |  |
  | offset    | int    |   N     | 一页显示多少条   |  不传默认3条               |
  | min_comments_id | Int | N | 当前页最小评论id | 首页可不传 |
  注：如果用户登陆需在header中传入Authorization和uid。


- 返回数据

| 参数                 | 类型   | 默认值 | 中文名       | 描述                         |
| -------------------- | ------ | ------ | ------------ | ---------------------------- |
| code                 | Int    |        | 状态码       | 200表示成功 -100代表失败     |
| msg                  | String |        | 提示信息     |                              |
| status               | String |        | 状态值       | OK 或者 FAIL                 |
| comment_id           | int |        | 评论id |                  |
| comment                | String |        | 评论内容 |  |
| create_time    | Strint |        | 评论时间 |                  |
| create_time            | String |        | 日期       |                  |
| reply_num      | int |        | 回复数量 |                  |
| userInfo | Array | | 用户信息 |  |
| userInfo['user_id'] | Int | | 用户id |  |
| userInfo['nickname'] | String | | 用户昵称 |  |
| userInfo['avatar'] | string | | 用户头像 |  |
| commentReply | array | | 评论回复列表 |  |
| commentReply['reply_id'] | Int |  | 回复id |  |
| commentReply['reply'] | text |  | 回复内容 |  |
| is_praise | Int |  | 用户是否已点赞 | 0 否 1是 |

- 返回格式(json)

```
{
    "code": 200,
    "msg": "操作成功",
    "status": "OK",
    "data": [
        {
            "comment_id": 5,
            "comment": "不错",
            "praise_num": 0,
            "create_time": "19小时前",
            "reply_num": 0,
            "userInfo": {
                "user_id": 4,
                "nickname": "dddd",
                "avatar": "adsfasdfd"
            },
            "commentReply": [],
            "is_praise": 0
        },
        {
            "comment_id": 4,
            "comment": "很有道理",
            "praise_num": 0,
            "create_time": "19小时前",
            "reply_num": 0,
            "userInfo": {
                "user_id": 4,
                "nickname": "dddd",
                "avatar": "adsfasdfd"
            },
            "commentReply": [],
            "is_praise": 0
        },
        {
            "comment_id": 3,
            "comment": "说的好",
            "praise_num": 0,
            "create_time": "23小时前",
            "reply_num": 1,
            "userInfo": {
                "user_id": 4,
                "nickname": "dddd",
                "avatar": "adsfasdfd"
            },
            "commentReply": [
                {
                    "reply_id": 4,
                    "reply": "希望美方言行一致",
                    "userInfo": {
                        "user_id": 2,
                        "nickname": "aaaaz"
                    }
                }
            ],
            "is_praise": 0
        }
    ]
}
```
#### 2.6评论点赞接口

- author : `Angz`

- 地址：`https://zqrb.stockalert.cn/commentPraise`

- HTTP请求方式：`POST`

- 请求参数

  | 参数    | 类型   | 是否必传 | 中文名   | 描述             |
  | ------- | ------ | ------ | -------- | ---------------- |
  | sign    | string |   Y     | 加密签名 |                  |
  | comment_id | int    |   Y    | 评论id |  |
  | status | int    |   Y    | 点赞状态 |  0 点赞 1取消赞      |
注：需在header中传入Authorization和uid。


- 返回数据

| 参数                 | 类型   | 默认值 | 中文名       | 描述                         |
| -------------------- | ------ | ------ | ------------ | ---------------------------- |
| code                 | Int    |        | 状态码       | 200表示成功 -100代表失败     |
| msg                  | String |        | 提示信息     |                              |
| status               | String |        | 状态值       | OK 或者 FAIL                 |

- 返回格式(json)

```
{
    "code": 200,
    "msg": "操作成功",
    "status": "OK"
}
```

#### 2.7新闻评论接口

- author : `Angz`

- 地址：`https://zqrb.stockalert.cn/comment`

- HTTP请求方式：`POST`

- 请求参数

  | 参数    | 类型   | 是否必传 | 中文名   | 描述             |
  | ------- | ------ | ------ | -------- | ---------------- |
  | sign    | string |   Y     | 加密签名 |                  |
  | news_id | int    |   Y    | 新闻id |  |
  | comment | string |   Y    | 评论内容 |                 |
注：需在header中传入Authorization和uid。


- 返回数据

| 参数                 | 类型   | 默认值 | 中文名       | 描述                         |
| -------------------- | ------ | ------ | ------------ | ---------------------------- |
| code                 | Int    |        | 状态码       | 200表示成功 -100代表失败     |
| msg                  | String |        | 提示信息     |                              |
| status               | String |        | 状态值       | OK 或者 FAIL                 |

- 返回格式(json)

```
{
    "code": 200,
    "msg": "操作成功",
    "status": "OK"
}
```
#### 2.8评论回复列表接口

- author : `Angz`

- 地址：`https://zqrb.stockalert.cn/reply`

- HTTP请求方式：`GET`

- 请求参数

  | 参数    | 类型   | 是否必传 | 中文名   | 描述             |
  | ------- | ------ | ------ | -------- | ---------------- |
  | sign    | string |   Y     | 加密签名 |                  |
  | comment_id | int    |   Y     | 评论id | 评论id |
  | offset    | int    |   N     | 一页显示多少条   |  不传默认5条               |
  | min_reply_id | int | N | 当前页评论回复id | 首页可不传 |
  注：如果用户登陆需在header中传入Authorization和uid。


- 返回数据

| 参数                 | 类型   | 默认值 | 中文名       | 描述                         |
| -------------------- | ------ | ------ | ------------ | ---------------------------- |
| code                 | Int    |        | 状态码       | 200表示成功 -100代表失败     |
| msg                  | String |        | 提示信息     |                              |
| status               | String |        | 状态值       | OK 或者 FAIL                 |
| data                 | Array |        | 数据包 |                  |
| comment       | Array |        | 评论数据 |  |
| comment_id | Int |        | 评论id |                  |
| comment | text |        | 评论内容  |                  |
| praise_num | Int |        | 点赞量 |                  |
| create_time | String | | 评论时间 | |
| userInfo          | Array |        | 用户信息 |                  |
| nickname | String | | 用户昵称 |  |
| avatar | String | | 用户头像 |  |
| reply | Array | | 评论回复 | |
| commentedUserInfo | array | | 被评论者或被回复者信息 | |
| commentedUserInfo[' reply'] | String | | 被评论的内容 | |

- 返回格式(json)

```
{
    "code": 200,
    "msg": "操作成功",
    "status": "OK",
    "data": {
        "comment": {
            "comment_id": 33,
            "comment": "俄罗斯出线形势大好",
            "praise_num": 0,
            "create_time": "2018-06-21 16:22:11",
            "user_id": 3,
            "userInfo": {
                "user_id": 3,
                "nickname": "用户XuS5634",
                "avatar": null
            },
            "is_praise": 0
        },
        "reply": [
            {
                "comment_id": 204,
                "comment": "我的天哪",
                "create_time": "14小时前",
                "parent_id": 89,
                "user_info": {
                    "user_id": 20,
                    "nickname": "积极",
                    "avatar": "http://zhengquanrb.oss-cn-hangzhou.aliyuncs.com/userPhoto/ios/1530090901451.jpg"
                },
                "commentedUserInfo": {
                    "user_id": 27,
                    "nickname": "囧囧囧",
                    "avatar": "",
                    "reply": "Goff"
                }
            },
            {
                "comment_id": 89,
                "comment": "Goff",
                "create_time": "2018-06-23 19:08:13",
                "parent_id": 0,
                "user_info": {
                    "user_id": 27,
                    "nickname": "囧囧囧",
                    "avatar": ""
                },
                "commentedUserInfo": []
            },
            {
                "comment_id": 28,
                "comment": "我的天哪",
                "create_time": "2018-06-21 17:13:27",
                "parent_id": 0,
                "user_info": {
                    "user_id": 2,
                    "nickname": "佩奇",
                    "avatar": "rewqasdfzvcxadfsdffddasdfasdfasdfasdfasdfasdfad"
                },
                "commentedUserInfo": []
            },
            {
                "comment_id": 27,
                "comment": "回复一下",
                "create_time": "2018-06-21 17:00:17",
                "parent_id": 0,
                "user_info": {
                    "user_id": 22,
                    "nickname": "aaaabbbbcccc",
                    "avatar": "rewqasdfzvcxadfsdffddasdfasdfasdfasdfasdfasdfad"
                },
                "commentedUserInfo": []
            }
        ]
    }
}
```

#### 2.9评论回复接口

- author : `Angz`

- 地址：`https://zqrb.stockalert.cn/reply`

- HTTP请求方式：`POST`

- 请求参数

  | 参数    | 类型   | 是否必传 | 中文名   | 描述             |
  | ------- | ------ | ------ | -------- | ---------------- |
  | sign    | string |   Y     | 加密签名 |                  |
  | comment_id | int    |   Y     | 评论ID |  |
  | reply | string |   Y    | 回复内容 |                 |
  | reply_id | Int | N | 回复id | 直接对评论进行回复时不传，对评论的回复进行回复时，要传 |
  | commenteduserid | String | Y | 被评论者或被回复者 | |
  注：如果用户登陆需在header中传入Authorization、uid。


- 返回数据

| 参数                 | 类型   | 默认值 | 中文名       | 描述                         |
| -------------------- | ------ | ------ | ------------ | ---------------------------- |
| code                 | Int    |        | 状态码       | 200表示成功 -100代表失败     |
| msg                  | String |        | 提示信息     |                              |
| status               | String |        | 状态值       | OK 或者 FAIL                 |

- 返回格式(json)

```
{
    "code": 200,
    "msg": "操作成功",
    "status": "OK"
}
```

#### 3.0 自选股添加、批量编辑、删除、置顶接口

- author : `Angz`

- 地址：`https://zqrb.stockalert.cn/userStock`

- HTTP请求方式：`POST`

- 请求参数

  | 参数    | 类型   | 是否必传 | 中文名   | 描述             |
  | ------- | ------ | ------ | -------- | ---------------- |
  | sign    | string |   Y     | 加密签名 |                  |
  | stockList | String |   Y     | 股票列表 |  |
  | is_delete | Int | Y | 是否是删除操作 | 0 否 1是(只有添加的时候传0，其他情况传1) |
  注：1.如果用户登陆需在header中传入Authorization、uid；

  ​	2.stockList的数据格式例如：

  ​	            "stockList":[{"code":"SH22203","is_top":0},{"code":"SH22203","is_top":1},{"code":"SH22203","is_top":0}]。

  ​	3.再生成sign的时候需要将stockList中的数组转换成json字符串

  ​	4.每一个数组里面的code的值需要在原来的code前面拼接上"SH"

  


- 返回数据

| 参数                 | 类型   | 默认值 | 中文名       | 描述                         |
| -------------------- | ------ | ------ | ------------ | ---------------------------- |
| code                 | Int    |        | 状态码       | 200表示成功 -100代表失败     |
| msg                  | String |        | 提示信息     |                              |
| status               | String |        | 状态值       | OK 或者 FAIL                 |

- 返回格式(json)

```
{
    "code": 200,
    "msg": "操作成功",
    "status": "OK"
}
```
#### 3.1 自选股列表接口

- author : `Angz`

- 地址：`https://zqrb.stockalert.cn/userStock`

- HTTP请求方式：`GET`

- 请求参数

  | 参数    | 类型   | 是否必传 | 中文名   | 描述             |
  | ------- | ------ | ------ | -------- | ---------------- |
  | sign    | string |   Y     | 加密签名 |                  |
  注：如果用户登陆需在header中传入Authorization、uid，否则返回空数组。


- 返回数据

| 参数     | 类型   | 默认值 | 中文名   | 描述                     |
| -------- | ------ | ------ | -------- | ------------------------ |
| code     | Int    |        | 状态码   | 200表示成功 -100代表失败 |
| msg      | String |        | 提示信息 |                          |
| status   | String |        | 状态值   | OK 或者 FAIL             |
| data     | Array  |        | 数据包   |                          |
| code     | String |        | 股票编码 |                          |
| user_id  | Int    |        | 用户ID   |                          |
| is_top   | int    |        | 是否置顶 |                          |
| mediaHot | float  |        | 媒体热度 |                          |

- 返回格式(json)

```
{
    "code": 200,
    "msg": "操作成功",
    "status": "OK",
    "data": [
        {
            "id": 2,
            "user_id": 2,
            "code": "SH600601",
            "is_top": 1,
            "mediaHot": "0.069091303882281588"
        },
        {
            "id": 4,
            "user_id": 2,
            "code": "SH600004",
            "is_top": 0,
            "mediaHot": "0.049635117973508031"
        },
        {
            "id": 1,
            "user_id": 2,
            "code": "SH600000",
            "is_top": 0,
            "mediaHot": "0.03347008221111851"
        }
    ]
}
```
#### 3.2 个股删除接口

- author : `Angz`

- 地址：`https://zqrb.stockalert.cn/userStock/:code`

- HTTP请求方式：`DELETE`

- 请求参数

  | 参数    | 类型   | 是否必传 | 中文名   | 描述             |
  | ------- | ------ | ------ | -------- | ---------------- |
  | sign    | string |   Y     | 加密签名 |                  |
  注：如果用户登陆需在header中传入token、uid，否则操作失败。


- 返回数据

| 参数                 | 类型   | 默认值 | 中文名       | 描述                         |
| -------------------- | ------ | ------ | ------------ | ---------------------------- |
| code                 | Int    |        | 状态码       | 200表示成功 -100代表失败     |
| msg                  | String |        | 提示信息     |                              |
| status               | String |        | 状态值       | OK 或者 FAIL                 |

- 返回格式(json)

```
{
    "code": 200,
    "msg": "操作成功",
    "status": "OK"
}
```

#### 3.3 个股详情接口

- author : `Angz`

- 地址：`https://zqrb.stockalert.cn/userStock/:code`

- HTTP请求方式：`GET`

- 请求参数

  | 参数    | 类型   | 是否必传 | 中文名   | 描述             |
  | ------- | ------ | ------ | -------- | ---------------- |
  | sign    | string |   Y     | 加密签名 |                  |
  | count | int    |   Y     | 显示的数量 |  |
  注：如果用户登陆需在header中传入Authorization、uid，否则返回空数组。


- 返回数据

| 参数   | 类型   | 默认值 | 中文名     | 描述                     |
| ------ | ------ | ------ | ---------- | ------------------------ |
| code   | Int    |        | 状态码     | 200表示成功 -100代表失败 |
| msg    | String |        | 提示信息   |                          |
| status | String |        | 状态值     | OK 或者 FAIL             |
| data   | array  |        | 数据包     |                          |
| date   | String |        | 日期时间戳 |                          |
| effect | String |        | 影响力     |                          |

- 返回格式(json)

```
{
    "code": 200,
    "msg": "操作成功",
    "status": "OK",
    "data": [
        {
            "rid": "SH600000.stk",
            "date": 1526486400000,
            "effect": 0.037935785752288266
        },
        {
            "rid": "SH600000.stk",
            "date": 1526572800000,
            "effect": 0.095903643069998268
        },
        {
            "rid": "SH600000.stk",
            "date": 1526659200000,
            "effect": 1.4999999999999999e-8
        },
        {
            "rid": "SH600000.stk",
            "date": 1526745600000,
            "effect": 0.000059244894008225669
        },
        {
            "rid": "SH600000.stk",
            "date": 1526832000000,
            "effect": 0.000019456399495254807
        }
    ]
}
```

#### 3.4 用户信息更新接口

- author : `Angz`

- 地址：`https://zqrb.stockalert.cn/user/update`

- HTTP请求方式：`POST`

- 请求参数

  | 参数    | 类型   | 是否必传 | 中文名   | 描述             |
  | ------- | ------ | ------ | -------- | ---------------- |
  | sign    | string |   Y     | 加密签名 |                  |
  | nickname | String |   N    | 用户昵称 |  |
  | avatar | string | N | 用户头像 | |
  注：如果用户登陆需在header中传入Authorization、uid。


- 返回数据

| 参数   | 类型   | 默认值 | 中文名     | 描述                     |
| ------ | ------ | ------ | ---------- | ------------------------ |
| code   | Int    |        | 状态码     | 200表示成功 -100代表失败 |
| msg    | String |        | 提示信息   |                          |
| status | String |        | 状态值     | OK 或者 FAIL             |

- 返回格式(json)

```
{
    "code": 200,
    "msg": "操作成功",
    "status": "OK"
}
```
#### 3.5意见反馈接口

- author : `Angz`

- 地址：`https://zqrb.stockalert.cn/feedback`

- HTTP请求方式：`POST`

- 请求参数

  | 参数    | 类型   | 是否必传 | 中文名   | 描述             |
  | ------- | ------ | ------ | -------- | ---------------- |
  | sign    | string |   Y     | 加密签名 |                  |
  | Feedback | String |   Y   | 反馈内容 |  |
  注：如果用户登陆需在header中传入Authorization、uid。


- 返回数据

| 参数   | 类型   | 默认值 | 中文名     | 描述                     |
| ------ | ------ | ------ | ---------- | ------------------------ |
| code   | Int    |        | 状态码     | 200表示成功 -100代表失败 |
| msg    | String |        | 提示信息   |                          |
| status | String |        | 状态值     | OK 或者 FAIL             |

- 返回格式(json)

```
{
    "code": 200,
    "msg": "操作成功",
    "status": "OK"
}
```
#### 3.6 关于我们接口

- author : `Angz`

- 地址：`https://zqrb.stockalert.cn/aboutUs`

- HTTP请求方式：`GET`

- 请求参数

  | 参数    | 类型   | 是否必传 | 中文名   | 描述             |
  | ------- | ------ | ------ | -------- | ---------------- |
  | sign    | string |   Y     | 加密签名 |                  |
  注：如果用户登陆需在header中传入Authorization、uid。


- 返回数据

| 参数     | 类型   | 默认值 | 中文名   | 描述                     |
| -------- | ------ | ------ | -------- | ------------------------ |
| code     | Int    |        | 状态码   | 200表示成功 -100代表失败 |
| msg      | String |        | 提示信息 |                          |
| status   | String |        | 状态值   | OK 或者 FAIL             |
| about_us | String |        | 关于我们 |                          |

- 返回格式(json)

```
{
    "code": 200,
    "msg": "操作成功",
    "status": "OK",
    "data": {
        "id": 1,
        "about_us": "《证券日报》是经济日报社主管主办的综合性证券专业报纸，是中国证监会指定披露上市公司信息报纸。《证券日报》秉持价值投资理念，以全新的运营模式和新闻传播理念，全心全意为资本市场参与者提供资讯服务。作为中国证监会指定披露上市公司信息报纸，承担着证券、保险、金融三大市场的政策发布、信息披露、舆论监督、投资者教育、市场文化建设等方面的使命和职责。"
    }
}
```
#### 3.7 用户信息接口

- author : `Angz`

- 地址：`https://zqrb.stockalert.cn/userInfo`

- HTTP请求方式：`GET`

- 请求参数

  | 参数    | 类型   | 是否必传 | 中文名   | 描述             |
  | ------- | ------ | ------ | -------- | ---------------- |
  | sign    | string |   Y     | 加密签名 |                  |
  注：如果用户登陆需在header中传入Authorization、uid。


- 返回数据

| 参数                      | 类型   | 默认值 | 中文名           | 描述                     |
| ------------------------- | ------ | ------ | ---------------- | ------------------------ |
| code                      | Int    |        | 状态码           | 200表示成功 -100代表失败 |
| msg                       | String |        | 提示信息         |                          |
| status                    | String |        | 状态值           | OK 或者 FAIL             |
| data                      | String |        | 数据包           |                          |
| user_id                   | int    |        | 用户id           |                          |
| nickname                  | string |        | 用户昵称         |                          |
| avatar                    | string |        | 用户头像         |                          |
| message                   | array  |        | 用户消息数据     |                          |
| message['num']            | Int    |        | 用户未读消息条数 |                          |
| message[' replyUserInfo'] | array  |        | 回复用户的信息   |                          |

- 返回格式(json)

```
{
    "code": 200,
    "msg": "操作成功",
    "status": "OK",
    "data": {
        "user_id": 3,
        "nickname": "bbbb",
        "avatar": "asdfasdfasdfasdf",
        "message": {
            "num": 1,
            "replyUserInfo": {
                "user_id": 5,
                "nickname": "凉19089977",
                "avatar": "https://zqrb.stockalert.cn/Group@3x.png"
            }
        }
    }
}
```

#### 3.8 收藏列表接口

- author : `wangfh`

- 地址：`https://zqrb.stockalert.cn/collectionList`

- HTTP请求方式：`GET`

- 请求参数

  | 参数    | 类型   | 是否必传 | 中文名   | 描述             |
  | ------- | ------ | ------ | -------- | ---------------- |
  | sign    | string |   Y     | 加密签名 |                  |
  | page  | int    |   N     | 第几页  | 不传默认第一页 |
  | pageSize    | int    |   N     | 一页显示多少条   |  不传默认10条               |
  注：需在header中传入Authorization、uid。


- 返回数据

| 参数                      | 类型   | 默认值 | 中文名           | 描述                     |
| ------------------------- | ------ | ------ | ---------------- | ------------------------ |
| code                      | Int    |        | 状态码           | 200表示成功 -100代表失败 |
| msg                       | String |        | 提示信息         |                          |
| status                    | String |        | 状态值           | OK 或者 FAIL             |
| data                      | String |        | 数据包           |                          |
| new_fla                 | int    |        | 类型           |  0:新闻；1：快讯  |
| 新闻：                 |     |        |            |    |
| news_id                   | Int    |        | 新闻id       |                                                              |
| title             | String |        | 新闻标题     |                                                              |
| summary           | Text   |        | 新闻摘要     |                                                              |
| comments_num      | int    |        | 评论数目     |                                                              |
| publish_time      | string |        | 发布时间     |                                                              |
| source_type       | int    |        | 资源类型     | 0:栏目列表,1:纯文本,2:单图(长方形),3多图,4:视频,5:单图(正方形) |
| detail_type       | Int    |        | 详情显示方式 | 1 正常文本流 2 横向滚动文本流                                |
| columns           | Array  |        | 栏目数组集合 |                                                              |
| columns[ "title"] | string |        | 栏目标题     |                                                              |
| listSource        | Array  |        | 新闻资源     | 列表图片 或 视屏,或栏目单图                                  |
| 快讯：                 |     |        |            |   |
| id                 | int    |        | 快讯id           |   |
| title                     | string |        | 标题         |                          |
| summary                    | string  |        | 内容     |                          |
| date                      | string    |        | 日期 |                          |
| news_type[ "title"]                      | int    |        | 栏目标题   |                         |
| source_type               | int    |        | 新闻资源类型   |（当是新闻的时候） 1:纯文本,2:单图(长方形),3多图,4:视频,5:单图(正方形)                     |
| detail_type               | int    |        | 详情显示方式   | （当是新闻的时候）  1 正常文本流 2 横向滚动文本流                         |

- 返回格式(json)

```
{
    "code": 200,
    "msg": "操作成功",
    "status": "OK",
    "data": [
        {
            "news_id": 30,
            "title": "雷军兑现让利承诺 小米IPO定价每股17港元",
            "summary": "记者从知情人士处获悉，小米港股IPO最终定价为17港元",
            "comments_num": 30,
            "publish_time": "2018-06-29 17:01",
            "source_type": 1,
            "column_ids": [
                {
                    "id": 1,
                    "parent_id": 0,
                    "title": "新闻"
                }
            ],
            "detail_type": 2,
            "listSource": [],
            "new_fla": 0
        },
        {
            "news_id": 29,
            "title": "雷军兑现让利承诺 小米IPO定价每股17港元",
            "summary": "记者从知情人士处获悉，小米港股IPO最终定价为17港元",
            "comments_num": 24,
            "publish_time": "2018-06-29 17:01",
            "source_type": 1,
            "column_ids": [
                {
                    "id": 1,
                    "parent_id": 0,
                    "title": "新闻"
                }
            ],
            "detail_type": 2,
            "listSource": [],
            "new_fla": 0
        },
        {
            "news_id": 2,
            "title": "央行收短放长 短期置换式降准概率低1",
            "summary": "summary2",
            "comments_num": 0,
            "publish_time": "2018-06-27 08:25",
            "source_type": 1,
            "column_ids": [
                {
                    "id": 8,
                    "parent_id": 4,
                    "title": "研判"
                }
            ],
            "detail_type": 1,
            "listSource": [],
            "new_fla": 0
        },
        {
            "id": 34,
            "type": 102,
            "title": "中国人寿(601628)寿险龙头蓄势后发，产能增长投资上行",
            "summary": "■市场扩容，龙头受益。预计个税递延方案将在年底前落地，初步估计个税递延将带来千亿保\n费增量。截至 2017 年 8 月国寿、平安寿险、太保寿险、新华四家保险公司保费市场份额合计\n达到 41%，显示出寿险行业较高的市场集中度，公司作为市场份额第一的行业龙头，在市场规\n模扩容的过程中",
            "update_time": 1531208773,
            "new_fla": 1,
            "date": "2018-07-10 15:46",
            "news_type": [
                {
                    "title": "研报"
                }
            ]
        }
    ]
}
```

#### 3.9 电子报头列表接口

- author : `wangfh`

- 地址：`https://zqrb.stockalert.cn/eNewsPaper`

- HTTP请求方式：`GET`

- 请求参数

  | 参数    | 类型   | 是否必传 | 中文名   | 描述             |
  | ------- | ------ | ------ | -------- | ---------------- |
  | sign    | string |   Y     | 加密签名 |                  |
  | date  | string    |   Y     | 日期  | 例：2018-06-15 |


- 返回数据

| 参数                      | 类型   | 默认值 | 中文名           | 描述                     |
| ------------------------- | ------ | ------ | ---------------- | ------------------------ |
| code                      | Int    |        | 状态码           | 200表示成功 -100代表失败 |
| msg                       | String |        | 提示信息         |                          |
| status                    | String |        | 状态值           | OK 或者 FAIL             |
| data                      | String |        | 数据包           |                          |
| head                      | string    |        | 头信息   |                          |

- 返回格式(json)

```
{
    "code": 200,
    "msg": "操作成功",
    "status": "OK",
    "data": [
        {
            "head": "A1头版",
            "date": "2018-06-21"
        },
        {
            "head": "A2今日基本面",
            "date": "2018-06-21"
        },
        {
            "head": "A3市场观察",
            "date": "2018-06-21"
        },
        {
            "head": "A4信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "B1证券业",
            "date": "2018-06-21"
        },
        {
            "head": "B2金融机构",
            "date": "2018-06-21"
        },
        {
            "head": "B3金融机构",
            "date": "2018-06-21"
        },
        {
            "head": "B4信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "C1公司新闻",
            "date": "2018-06-21"
        },
        {
            "head": "C2食 品",
            "date": "2018-06-21"
        },
        {
            "head": "C3互联网",
            "date": "2018-06-21"
        },
        {
            "head": "C4公司新闻",
            "date": "2018-06-21"
        },
        {
            "head": "D1信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D2信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D3信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D4信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D5信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D6信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D7信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D8信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D9信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D10信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D11信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D12信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D13信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D14信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D15信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D16信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D17信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D18信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D19信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D20信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D21信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D22信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D23信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D24信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D25信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D26信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D27信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D28信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D29信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D30信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D31信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D32信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D33信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D34信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D35信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D36信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D37信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D38信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D39信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D40信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D41信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D42信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D43信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D44信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D45信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D46信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D47信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D48信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D49信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D50信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D51信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D52信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D53信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D54信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D55信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D56信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D57信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D58信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D59信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D60信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D61信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D62信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D63信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D64信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D65信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D66信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D67信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D68信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D69信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D70信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D71信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D72信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D73信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D74信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D75信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D76信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D77信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D78信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D79信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D80信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D81信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D82信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D83信息披露",
            "date": "2018-06-21"
        },
        {
            "head": "D84信息披露",
            "date": "2018-06-21"
        }
    ]
}
```

#### 3.9 电子报头标题接口

- author : `wangfh`

- 地址：`https://zqrb.stockalert.cn/ePaperDetail`

- HTTP请求方式：`GET`

- 请求参数

  | 参数    | 类型   | 是否必传 | 中文名   | 描述             |
  | ------- | ------ | ------ | -------- | ---------------- |
  | sign    | string |   Y     | 加密签名 |                  |
  | date  | string    |   Y     | 日期  | 例：2018-06-15 |
  | head  | string    |   Y     | 头信息  | 例：A4信息披露 |


- 返回数据

| 参数                      | 类型   | 默认值 | 中文名           | 描述                     |
| ------------------------- | ------ | ------ | ---------------- | ------------------------ |
| code                      | Int    |        | 状态码           | 200表示成功 -100代表失败 |
| msg                       | String |        | 提示信息         |                          |
| status                    | String |        | 状态值           | OK 或者 FAIL             |
| data                      | String |        | 数据包           |                          |
| head                      | string    |        | 头信息   |                          |
| title                      | string    |        | 标题   |                          |
| url                      | string    |        | 跳转地址   |                          |

- 返回格式(json)

```
{
    "code": 200,
    "msg": "操作成功",
    "status": "OK",
    "data": [
        {
            "id": 2907,
            "head": "A4信息披露",
            "title": "彤程新材料集团股份有限公司首次公开发行Ａ股网上中签率公告",
            "url": "http://m.epaper.zqrb.cn/html/2018-06/15/content_313749.htm?div=-1",
            "create_time": 1529052157,
            "is_show": 1,
            "date": "2018-06-15"
        },
        {
            "id": 2908,
            "head": "A4信息披露",
            "title": "中国冶金科工股份有限公司2018年1-5月份新签合同情况简报",
            "url": "http://m.epaper.zqrb.cn/html/2018-06/15/content_313750.htm?div=-1",
            "create_time": 1529052157,
            "is_show": 1,
            "date": "2018-06-15"
        },
        {
            "id": 2909,
            "head": "A4信息披露",
            "title": "湖南百利工程科技股份有限公司关于签署股权收购意向书的补充说明公告",
            "url": "http://m.epaper.zqrb.cn/html/2018-06/15/content_313752.htm?div=-1",
            "create_time": 1529052158,
            "is_show": 1,
            "date": "2018-06-15"
        },
        {
            "id": 2910,
            "head": "A4信息披露",
            "title": "中国神华能源股份有限公司2018年5月份主要运营数据公告",
            "url": "http://m.epaper.zqrb.cn/html/2018-06/15/content_313777.htm?div=-1",
            "create_time": 1529052158,
            "is_show": 1,
            "date": "2018-06-15"
        },
        {
            "id": 2911,
            "head": "A4信息披露",
            "title": "贵人鸟股份有限公司2017年年度权益分派实施公告",
            "url": "http://m.epaper.zqrb.cn/html/2018-06/15/content_313778.htm?div=-1",
            "create_time": 1529052158,
            "is_show": 1,
            "date": "2018-06-15"
        },
        {
            "id": 2912,
            "head": "A4信息披露",
            "title": "中国东方航空股份有限公司关于2018年第五期超短期融资券发行的公告",
            "url": "http://m.epaper.zqrb.cn/html/2018-06/15/content_313779.htm?div=-1",
            "create_time": 1529052158,
            "is_show": 1,
            "date": "2018-06-15"
        },
        {
            "id": 2913,
            "head": "A4信息披露",
            "title": "创金合信基金管理有限公司关于旗下证券投资基金估值调整情况的公告",
            "url": "http://m.epaper.zqrb.cn/html/2018-06/15/content_313780.htm?div=-1",
            "create_time": 1529052158,
            "is_show": 1,
            "date": "2018-06-15"
        },
        {
            "id": 2914,
            "head": "A4信息披露",
            "title": "广州汽车集团股份有限公司关于控股股东增持计划的进展公告",
            "url": "http://m.epaper.zqrb.cn/html/2018-06/15/content_313781.htm?div=-1",
            "create_time": 1529052158,
            "is_show": 1,
            "date": "2018-06-15"
        },
        {
            "id": 2915,
            "head": "A4信息披露",
            "title": "芜湖伯特利汽车安全系统股份有限公司股票交易异常波动公告",
            "url": "http://m.epaper.zqrb.cn/html/2018-06/15/content_313782.htm?div=-1",
            "create_time": 1529052158,
            "is_show": 1,
            "date": "2018-06-15"
        },
        {
            "id": 2916,
            "head": "A4信息披露",
            "title": "上海飞乐音响股份有限公司关于股东股权解质及质押的公告",
            "url": "http://m.epaper.zqrb.cn/html/2018-06/15/content_313783.htm?div=-1",
            "create_time": 1529052158,
            "is_show": 1,
            "date": "2018-06-15"
        }
    ]
}
```
#### 4.0 用户消息列表接口

- author : `Angz`

- 地址：`https://zqrb.stockalert.cn/message`

- HTTP请求方式：`GET`

- 请求参数

  | 参数    | 类型   | 是否必传 | 中文名   | 描述             |
  | ------- | ------ | ------ | -------- | ---------------- |
  | sign    | string |   Y     | 加密签名 |                  |
  | min_reply_id | Int | N | 当前页最小reply_id | 第一页可不传 |
  | offset | Int | N | 每页显示的条数 默认20 |  |

  注：需在header中传入Authorization、uid。


- 返回数据

| 参数                      | 类型   | 默认值 | 中文名           | 描述                     |
| ------------------------- | ------ | ------ | ---------------- | ------------------------ |
| code                      | Int    |        | 状态码           | 200表示成功 -100代表失败 |
| msg                       | String |        | 提示信息         |                          |
| status                    | String |        | 状态值           | OK 或者 FAIL             |
| data                      | String |        | 数据包           |                          |
| reply_id             | Int   |        | 回复id |                          |
| reply                | string    |        | 回复内容 |                          |
| create_time          | string    |        | 恢复时间   |                          |
| user_info | array | | 用户信息数据 | |
| user_info['id'] | int | | 回复用户ID | |
| user_info['nickname'] | string | | 用户昵称 | |
| user_info['avatar'] | String | | 用户头像 | |
| news_info | Array | | 新闻信息数据 | |
| news_info['id'] | String | | 新闻id | |
| news_info['title'] | string | | 新闻标题 | |
| news_info['column_ids'] | array | | 新闻栏目信息 | |
| column_ids【0】['title'] | string | | 栏目标题 | |
| news_info[' source_type'] | Int | | 新闻资源类型 | 0:栏目列表,1:纯文本,2:单图(长方形),3多图,4:视频,5:单图(正方形) |
| news_info[' source'] | array | | 新闻资源数据 | |
| news_info[' source']【‘ source_path’】 | string | | 新闻资源路径 | |

- 返回格式(json)

```
{
    "code": 200,
    "msg": "操作成功",
    "status": "OK",
    "data": [
        {
            "reply_id": 8,
            "reply": "世界杯即将开赛3",
            "create_time": "2018-06-19 11:45:28",
            "user_info": {
                "id": 1,
                "nickname": "aaaa",
                "avatar": ""
            },
            "news_info": {
                "id": 2,
                "title": "央行收短放长 短期置换式降准概率低",
                "column_ids": [
                    {
                        "id": 4,
                        "parent_id": 0,
                        "title": "深度"
                    },
                    {
                        "id": 5,
                        "parent_id": 0,
                        "title": "电子报"
                    },
                    {
                        "id": 6,
                        "parent_id": 0,
                        "title": "财经"
                    }
                ],
                "source_type": 2,
                "source": {
                    "id": 4,
                    "news_id": 2,
                    "source_path": "dfsadsfadsaf"
                }
            }
        },
        {
            "reply_id": 7,
            "reply": "世界杯即将开赛2",
            "create_time": "2018-06-19 11:23:26",
            "user_info": {
                "id": 1,
                "nickname": "aaaa",
                "avatar": ""
            },
            "news_info": {
                "id": 5,
                "title": "title5",
                "column_ids": [
                    {
                        "id": 1,
                        "parent_id": 0,
                        "title": "新闻"
                    },
                    {
                        "id": 5,
                        "parent_id": 0,
                        "title": "电子报"
                    },
                    {
                        "id": 6,
                        "parent_id": 0,
                        "title": "财经"
                    }
                ],
                "source_type": 2,
                "source": {
                    "id": 6,
                    "news_id": 5,
                    "source_path": "aaaaaaa"
                }
            }
        },
        {
            "reply_id": 6,
            "reply": "世界杯即将开赛1",
            "create_time": "2018-06-14 15:10:51",
            "user_info": {
                "id": 1,
                "nickname": "aaaa",
                "avatar": ""
            },
            "news_info": {
                "id": 2,
                "title": "央行收短放长 短期置换式降准概率低",
                "column_ids": [
                    {
                        "id": 4,
                        "parent_id": 0,
                        "title": "深度"
                    },
                    {
                        "id": 5,
                        "parent_id": 0,
                        "title": "电子报"
                    },
                    {
                        "id": 6,
                        "parent_id": 0,
                        "title": "财经"
                    }
                ],
                "source_type": 2,
                "source": {
                    "id": 4,
                    "news_id": 2,
                    "source_path": "dfsadsfadsaf"
                }
            }
        }
    ]
}
```
#### 4.1 用户消息删除接口

- author : `Angz`

- 地址：`https://zqrb.stockalert.cn/message/:id`

- HTTP请求方式：`DELETE`

- 请求参数

  | 参数    | 类型   | 是否必传 | 中文名   | 描述             |
  | ------- | ------ | ------ | -------- | ---------------- |
  | sign    | string |   Y     | 加密签名 |                  |

  注：需在header中传入Authorization、uid。


- 返回数据

| 参数                      | 类型   | 默认值 | 中文名           | 描述                     |
| ------------------------- | ------ | ------ | ---------------- | ------------------------ |
| code                      | Int    |        | 状态码           | 200表示成功 -100代表失败 |
| msg                       | String |        | 提示信息         |                          |
| status                    | String |        | 状态值           | OK 或者 FAIL             |

- 返回格式(json)

```
{
    "code": 200,
    "msg": "操作成功",
    "status": "OK"
}
```
#### 4.2 新闻转发接口

- author : `Angz`

- 地址：`https://zqrb.stockalert.cn/transmit`

- HTTP请求方式：`GET`

- 请求参数

  | 参数    | 类型   | 是否必传 | 中文名   | 描述             |
  | ------- | ------ | ------ | -------- | ---------------- |
  | sign    | string |   Y     | 加密签名 |                  |
  | news_id | Int | Y | 新闻id | |

  


- 返回数据

| 参数   | 类型   | 默认值 | 中文名     | 描述                     |
| ------ | ------ | ------ | ---------- | ------------------------ |
| code   | Int    |        | 状态码     | 200表示成功 -100代表失败 |
| msg    | String |        | 提示信息   |                          |
| status | String |        | 状态值     | OK 或者 FAIL             |
| data   | Array  |        | 数据包     |                          |
| url    | String |        | 详情页地址 |                          |

- 返回格式(json)

```
{
    "code": 200,
    "msg": "操作成功",
    "status": "OK",
    "data": {
        "url": "https://zqrb.stockalert.cn/newsDetail/1"
    }
}
```
#### 4.3 app初始化接口

- author : `Angz`

- 地址：`https://zqrb.stockalert.cn/init`

- HTTP请求方式：`GET`

- 请求参数

  | 参数    | 类型   | 是否必传 | 中文名   | 描述             |
  | ------- | ------ | ------ | -------- | ---------------- |
  | sign    | string |   Y     | 加密签名 |                  |
  | app_id | Int | Y | 客户端设备类型 1:安卓手机, 2:安卓pad,3:iphone,4ipad | |
  | version_id | Int | Y | 大版本号（eg:1或2) | |
  | version_mini | float | Y | 小版本号（eg:0.0或1.0） | |

  注：APP版本=大版本号.小版本号 (eg:1.0.0)


- 返回数据

| 参数         | 类型   | 默认值 | 中文名       | 描述                             |
| ------------ | ------ | ------ | ------------ | -------------------------------- |
| code         | Int    |        | 状态码       | 200表示成功 -100代表失败         |
| msg          | String |        | 提示信息     |                                  |
| status       | String |        | 状态值       | OK 或者 FAIL                     |
| data         | Array  |        | 数据包       |                                  |
| is_update    | Int    |        | 是否需要更新 | 0 不需更新,1可选择更新,2强制更新 |
| version_id   | Int    |        | 大版本号     |                                  |
| version_mini | float  |        | 小版本号     |                                  |
| publish_time | int    |        | 发布时间     |                                  |

- 返回格式(json)

```
{
    "code": 200,
    "msg": "操作成功",
    "status": "OK",
    "data": {
        "is_update": 0,
        "version_id": 1,
        "version_mini": 0,
        "description": "证券日报测试版",
        "publish_time": 1530520313
    }
}
```

#### 4.4 新闻列广告接口

- 地址：`https://zqrb.stockalert.cn/adverNewsList`

- HTTP请求方式：`POST`

- 请求参数 

  | 参数        | 类型   | 默认值 | 中文名                     | 描述               |
  | ----------- | ------ | ------ | -------------------------- | ------------------ |
  | sign        | string |        | 加密签名                   |                    |
  | column_id   | int    |        | 栏目id                   |             |



- 返回数据

| 参数                 | 类型   | 默认值 | 中文名       | 描述                         |
| -------------------- | ------ | ------ | ------------ | ---------------------------- |
| code                 | Int    |        | 状态码       | 200表示成功 -100代表失败     |
| msg                  | String |        | 提示信息     |                              |
| status               | String |        | 状态值       | OK 或者 FAIL                 |
| data                 | Array  |        | 数据包       |                              |
| id                   | Int    |        | 广告id       |                              |
| title                | String |        | 标题        |                              |
| position             | Int    |        | 广告定位     |                              |
| out_url              | Str    |        | 广告外链     |                               |
| source_type          | Str    |        | 资源类型     | 1:纯文本,2:单图(长方形),3多图,4:视频,5:单图(正方形)   |
| state                | int    |        | 跳转方式     | 0链接,1详情                 |
| pic                  | array  |        | 图片地址     |                              |
| pic_path             | String |        | 图片地址    |                             |
| time                 | String |        | 时间     |（直接显示）                    |



- 返回格式(json)

```
{
    "code": 200,
    "msg": "操作成功",
    "status": "OK",
    "data": [
        {
            "id": 27,
            "title": "标题",
            "out_url": null,
            "source_type": 2,
            "update_time": 1530644123,
            "state": 0,
            "pic": [
                {
                    "pic_path": "http://oss-cn-hangzhou.aliyuncs.com/zhengquanrb/serverData/advertising/2018070517382339333.jpg"
                }
            ],
            "time": "2018-07-04",
            "column_id": 1,
            "position": 6,
            "adver_id": 0
        },
        {
            "id": 21,
            "title": "标题",
            "out_url": "https://www.baidu.com",
            "source_type": 3,
            "update_time": 1530784930,
            "state": 0,
            "pic": [
                {
                    "pic_path": "http://oss-cn-hangzhou.aliyuncs.com/zhengquanrb/serverData/advertising/2018070413435626071.jpg"
                },
                {
                    "pic_path": "http://oss-cn-hangzhou.aliyuncs.com/zhengquanrb/serverData/advertising/2018070413435626071.jpg"
                }
            ],
            "time": "23小时前",
            "column_id": 1,
            "position": 8,
            "adver_id": 21
        }
    ]
}
```

#### 4.4 banner列广告接口

- 地址：`https://zqrb.stockalert.cn/bannerAdver`

- HTTP请求方式：`GET`

- 请求参数 

  | 参数        | 类型   | 默认值 | 中文名                     | 描述               |
  | ----------- | ------ | ------ | -------------------------- | ------------------ |
  | sign        | string |        | 加密签名                   |                    |



- 返回数据

| 参数                 | 类型   | 默认值 | 中文名       | 描述                         |
| -------------------- | ------ | ------ | ------------ | ---------------------------- |
| code                 | Int    |        | 状态码       | 200表示成功 -100代表失败     |
| msg                  | String |        | 提示信息     |                              |
| status               | String |        | 状态值       | OK 或者 FAIL                 |
| data                 | Array  |        | 数据包       |                              |
| id                   | Int    |        | 广告id       |                              |
| title                | String |        | 标题        |                              |
| position             | Int    |        | 广告定位     |                              |
| out_url              | Str    |        | 广告外链     |                               |
| source_type          | Str    |        | 资源类型     | 1:纯文本,2:单图(长方形),3多图,4:视频,5:单图(正方形)   |
| state                | int    |        | 跳转方式     | 0链接,1详情                 |
| pic                  | array  |        | 图片地址     |                              |
| pic_path             | String |        | 图片地址    |                             |



- 返回格式(json)

```
{
    "code": 200,
    "msg": "操作成功",
    "status": "OK",
    "data": [
        {
            "id": 29,
            "title": "标题",
            "out_url": "https://www.jianshu.com/p/135a2ecc3256",
            "source_type": 2,
            "update_time": 1531103790,
            "state": 0,
            "pic": [
                {
                    "pic_path": "http://oss-cn-hangzhou.aliyuncs.com/zhengquanrb/serverData/advertising/2018070615585154845.png"
                }
            ],
            "position": 2,
            "adver_id": 0
        },
        {
            "id": 29,
            "title": "标题",
            "out_url": "https://www.jianshu.com/p/135a2ecc3256",
            "source_type": 2,
            "update_time": 1531103790,
            "state": 0,
            "pic": [
                {
                    "pic_path": "http://oss-cn-hangzhou.aliyuncs.com/zhengquanrb/serverData/advertising/2018070615585154845.png"
                }
            ],
            "position": 4,
            "adver_id": 29
        }
    ]
}
```

