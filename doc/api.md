[TOC]

# 1. pe管理系统API定义

## 1.1. 修订说明
| 版本号        | 修订人   | 修订时间       | 说明     |
| ---------- | ----- | ---------- | ------ |
| Version1.0 | magic | 2017-07-20 | 新增登录功能 |

1.2. 说明

* 基础URL：`http://192.168.1.64:8081`正式环境会修改

* 当message="ok"时，表示调用接口成功，其他表示失败返回说明。

* 企业appkey 由后端提供 一段随机字符串 海通appkey为d5f17cafef0829b5 

* sign安全加密认证的参数

   1.将接口需要提交的参数（不含sign）按名字排序（字典序）

   2.将所有参数用下面方法拼接起来：

  ​      `参数1=value1&参数2=value2&…&参数n=valuen`

​       3.将步骤2得到的字符串后面再拼上pe2017Signkey

​       4.将步骤3得到的字符串进行MD5运算，最后结果作为sign参数的值。

  **计算sign的java代码举例：**

  ```
  public static String getSign(Map<String, String> params){
    String sign = "";
    Map<String, String> treeMap = new java.util.TreeMap<String, String>();
    if(params!=null){
      if(params!=null && params.size()>0){
        for(Map.Entry<String, String> entry: params.entrySet()){
          treeMap.put(entry.getKey(), entry.getValue());
        }
      }
    }

    //获取MD5加密信息
    StringBuffer buffer = new StringBuffer();
    for(Map.Entry<String, String> entry : treeMap.entrySet()){
               buffer.append(entry.getKey()).append("=").append(entry.getValue()).append("&");
    }
   
    buffer.deleteCharAt(buffer.length()-1);
    buffer.append("pe2017Signkey");
    sign = MD5.encoding(buffer.toString(), null);

      return sign;
  }
  ```

* 每个接口都必须传appkey和sign参数

# 2. 用户接口 
### 2.1. 发送验证码 

+ 地址：`/api/index/send_code`


+ 请求格式  
    - HTTP请求方式： `POST` 
    - 支持格式： `JSON` 
+ 请求参数

    | 参数    | 类型   | 默认值  | 中文名  | 描述   |
    | :---- | :--- | :--- | :--- | :--- |
    | phone | int  |      | 手机号码 |      |
+ 返回格式

    | 参数        | 类型     | 默认值  | 中文名      | 描述            |
    | :-------- | :----- | :--- | :------- | :------------ |
    | message   | string |      | 状态信息     | ok表示成功，其他均为失败 |
    | timestamp | long   |      | 服务器毫秒时间戳 |               |
    | payload   | object |      | 附加数据     | []            |

示例

- 请求：

```
http://192.168.1.64:8081/api/index/send_code
```

- 返回：

```
{
  "message": "ok",
  "timestamp": 1490679496293,
  "payload": []
}
```

### 2.2. 登录（验证短信接口）

- 地址：`/api/index/verify_code`


- 请求格式  

  - HTTP请求方式： `POST` 
  - 支持格式： `JSON` 

- 请求参数

  | 参数    | 类型   | 默认值  | 中文名   | 描述         |
  | :---- | :--- | :--- | :---- | :--------- |
  | phone | Int  |      | 手机号码  | 登录手机号      |
  | code  | Int  |      | 手机验证码 | 用户手机收到的验证码 |

- 返回格式

  | 参数        | 类型     | 默认值  | 中文名      | 描述                                  |
  | :-------- | :----- | :--- | :------- | :---------------------------------- |
  | message   | string |      | 状态信息     | ok表示成功，其他均为失败，失败信息可能是手机号不正确，验证码失效等等 |
  | timestamp | long   |      | 服务器毫秒时间戳 |                                     |
  | payload   | object |      | 附加数据     | []                                  |


- 返回参数

  | 序号   | 参数          | 类型      | 字段说明   | 描述   |
  | ---- | ----------- | ------- | ------ | ---- |
  | 1    | uid         | Int     | 用户id   |      |
  | 2    | name        | Varchar | 用户姓名   |      |
  | 3    | phone       | Varchar | 手机号    |      |
  | 4    | depart_name | Int     | 部门id   |      |
  | 5    | position    | Varchar | 职位     |      |
  | 6    | email       | Varchar | 邮箱     |      |
  | 7    | project     | Varchar | 项目     |      |
  | 8    | memo        | Text    | 备注     |      |
  | 9    | url         | Varchar | 图像链接地址 |      |

- 示例

  - 请求：

  ```
  http://192.168.1.64:8081/api/index/verify_code
  ```

  - 返回：

  ```
  {
    "message": "ok",
    "timestamp": 1500544643087,
    "payload": {
      "uid": 19,
      "name": null,
      "phone": "18701780251",
      "depart_name": "",
      "position": null,
      "email": null,
      "project": null,
      "memo": null,
      "url": null
    }
  }
  ```


### 2.3. 获取个人信息接口

- 地址：`/api/userFont/getFrontUserInfo`


- 请求格式  

  - HTTP请求方式： `POST` 
  - 支持格式： `JSON` 

- 请求参数

  | 参数   | 类型   | 默认值  | 中文名         |
  | ---- | ---- | ---- | ----------- |
  | uid  | Int  |      | 用户ID（非空必传 ） |

- 返回格式

    | 参数        | 类型     | 默认值  | 中文名      | 描述            |
    | :-------- | :----- | :--- | :------- | :------------ |
    | message   | string |      | 状态信息     | ok表示成功，其他均为失败 |
    | timestamp | long   |      | 服务器毫秒时间戳 |               |
    | payload   | object |      | 附加数据     | data[]        |

- data[]信息如下

   | 序号   | 参数          | 类型      | 字段说明 | 描述     |
   | ---- | ----------- | ------- | ---- | ------ |
   | 1    | url         | Varchar | 链接   | 头像图片链接 |
   | 2    | name        | Varchar | 姓名   |        |
   | 3    | position    | Varchar | 职位   |        |
   | 4    | phone       | Varchar | 手机号码 |        |
   | 5    | email       | Varchar | 邮件   |        |
   | 6    | depart_id   | Int     | 部门ID |        |
   | 7    | project     | Varchar | 项目   |        |
   | 8    | memo        | Text    | 备注   |        |
   | 9    | uid         | Int     | 用户ID |        |
   | 10   | depart_name | Varchar | 部门名称 |        |

    ​


- 请求：

  ```
  http://192.168.1.64:8081/api/userFont/getFrontUserInfo
  ```

- 返回：

  ```
  {
  	"message":"ok",
  	"timestamp":1501568924060,
  	"payload":
  		{
  		"uid":1,
  		"name":"ewr",
  		"phone":"18895634289",
  		"depart_id":2,
  		"position":"经理",
  		"email":"1401128960@qq.com",
  		"project":"dsf",
  		"memo":"sdasad",
          "depart_name":"拓展部",
          "url":""http:\/\/pe.location.com\/uploads\/20170802\/c2a918b118cc25f0c2783aa66f4e055e.jpg"，
  	
  		}
  }
  ```

### 2.4. 个人信息-获取部门接口

- 地址：`/api/UserFont/getDepartmentInfo`


- 请求格式  

  - HTTP请求方式： `POST` 
  - 支持格式： `JSON` 

- 请求参数

  | 参数     | 类型      | 默认值  | 中文名     | 描述       |
  | ------ | ------- | ---- | ------- | -------- |
  | appkey | varchar |      | 企业唯一标识符 | 必传参数（非空） |

  ​

- 返回格式

  | 参数        | 类型     | 默认值  | 中文名      | 描述                         |
  | :-------- | :----- | :--- | :------- | :------------------------- |
  | message   | string |      | 状态信息     | ok表示成功，其他均为失败              |
  | timestamp | long   |      | 服务器毫秒时间戳 |                            |
  | payload   | object |      | 附加数据     | [db_name,depart_id,data[]] |


- de_name对应每个部门名称

  | 序号   | 参数        | 类型     | 字段说明       | 描述         |
  | ---- | --------- | ------ | ---------- | ---------- |
  | 1    | db_name   | String | 部门名称       |            |
  | 2    | depart_id | Int    | 部门id       | 部门ID对应部门名称 |
  | 3    | data      | Array  | 对应部门下所有人信息 |            |

- data[]信息如下

  | 序号   | 参数       | 类型      | 字段说明 | 描述     |
  | ---- | -------- | ------- | ---- | ------ |
  | 1    | url      | Varchar | 链接   | 头像图片链接 |
  | 2    | name     | Varchar | 姓名   |        |
  | 3    | position | Varchar | 职位   |        |
  | 4    | phone    | Varchar | 手机号码 |        |
  | 5    | email    | Varchar | 邮件   |        |

示例

- 请求：

```
http://192.168.1.64:8081/api/UserFont/getDepartmentInfo
```

- 返回：

```
{
	"message":"ok",
	"timestamp":1501310532570,
	"payload":
		[
		{"de_name":"开发部",
		'depart_id':1,
		"data":
			[{"url":null,
			"name":"小二",
			"position":"ppp",
			"phone":"15800421946",
			"email":"14011289@qq.com"}
			]},
			
		{"de_name":"拓展部",
		'depart_id':2,
		"data":
			[{"url":null,
			"name":"小三",
			"position":"ioo",
			"phone":"18701780251",
			"email":"1455555@qq.com"},
			
			{"url":null,
			"name":"小四",
			"position":"yyy",
			"phone":"18895625589",
			"email":"18865555@qq.com"}
			]},
			
		{"de_name":"外联部",
		'depart_id':3,
		"data":
			[] }]
}
```

###  2.5. 修改个人信息接口

- 地址：`/api/userFont/changeMyInfo`


- 请求格式  

  - HTTP请求方式： `POST` 
  - 支持格式： `JSON` 

- 请求参数

  | 参数        | 类型      | 默认值  | 中文名                                  |
  | --------- | ------- | ---- | ------------------------------------ |
  | uid       | Int     |      | 用户ID（非空必传）                           |
  | name      | Varchar |      | 用户姓名（可空）                             |
  | depart_id | Int     |      | 部门ID（可空depart_id来源于获取部门接口的depart_id） |
  | position  | Varchar |      | 职位（可空）                               |
  | project   | Varchar |      | 项目（可空）                               |
  | memo      | Text    |      | 备注（可空）                               |
  | email     | Varchar |      | 邮箱（可空）                               |

  ​

- 返回格式

  | 参数        | 类型     | 默认值  | 中文名      | 描述            |
  | :-------- | :----- | :--- | :------- | :------------ |
  | message   | string |      | 状态信息     | ok表示成功，其他均为失败 |
  | timestamp | long   |      | 服务器毫秒时间戳 |               |
  | payload   | object |      | 附加数据     | Null          |

  示例


- 请求：

```
http://192.168.1.64:8081/api/userFont/changeMyInfo
```

- 返回：

```
{
	"message":"ok",
	"timestamp":1501226954687,
	"payload":null	
}
```

### 2.6. 用户头像上传接口

- 地址：`/api/Userfont/uploadImage`


- 请求格式  

  - HTTP请求方式： `POST` 
  - 支持格式： `JSON` 

- 请求参数

  | 参数    | 类型   | 默认值  | 中文名                                      |
  | ----- | ---- | ---- | ---------------------------------------- |
  | uid   | Int  |      | 用户ID（非空必传）                               |
  | image | File |      | 图片文件(type='file',  name='image')form_data格式 |

  ​

- 返回格式

  | 参数        | 类型     | 默认值  | 中文名      | 描述            |
  | :-------- | :----- | :--- | :------- | :------------ |
  | message   | string |      | 状态信息     | ok表示成功，其他均为失败 |
  | timestamp | long   |      | 服务器毫秒时间戳 |               |
  | payload   | object |      | 附加数据     | String        |

  示例
  ​
-  返回格式

   | 参数   | 类型     | 默认值  | 中文名   | 描述   |
   | :--- | :----- | :--- | :---- | :--- |
   | Url  | String |      | 图片url | 完全路径 |


- 请求：

```
http://192.168.1.64:8081/api/userFont/uploadImage
```

- 返回：

```
{
	"message":"ok",
	"timestamp":1501226954687,	"payload":"http:\/\/pe.ll.com\/uploads\/20170803\/2aa744c830c0a2e5b014af1457556161.jpg"	
}
```

# 3. 资讯接口
### 3.1. 资讯列表

- 地址：`/api/news/newsLists`


-   请求格式

    - HTTP请求方式： `POST`
    - 支持格式： `JSON`

- 请求参数

    | 参数         | 类型   | 默认值  | 中文名    | 描述                     |
    | ---------- | ---- | ---- | ------ | ---------------------- |
    | page       | Int  | 1    | 页数     | 第几页（可空）                |
    | pageSize   | Int  | 20   | 每页数量   | 可空                     |
    | uid        | Int  |      | 登录用户id | 可空（如果传递了user_id,则表示关注） |
    | type       | Int  |      | 类型     | 可空（1是负面，2是舆情）          |
    | company_id | Int  |      | 公司id   | 可空（公司详情非空）             |

    ​

- 返回格式

    | 参数        | 类型     | 默认值  | 中文名      | 描述            |
    | :-------- | :----- | :--- | :------- | :------------ |
    | message   | string |      | 状态信息     | ok表示成功，其他均为失败 |
    | timestamp | long   |      | 服务器毫秒时间戳 |               |
    | payload   | object |      | 附加数据     | [data]        |

- payload.data是一个数组，结构如下：下面是全部勾选情况下的数据，实际以具体勾选的选项为准

    | 序号   | 参数         | 类型       | 字段说明     | 描述                                      |
    | ---- | ---------- | -------- | -------- | --------------------------------------- |
    | 1    | title      | Varchar  | 新闻标题     |                                         |
    | 2    | time       | Datetime | 新闻发表时间   |                                         |
    | 3    | source     | Varchar  | 新闻来源     |                                         |
    | 4    | tag        | Varchar  | 新闻标签     | 多个标签以#分割且已在末尾加上强制词（如果table_id=13则忽略强制词） |
    | 5    | company_id | Int      | 公司编号     |                                         |
    | 6    | table_id   | int      | 资讯对应的表id | 对应新闻类型表                                 |
    | 7    | data_id    | Int      | 数据id     | 对应某个新闻类型的某条新闻                           |


示例

- 请求：

```
http://192.168.1.64:8081/api/news/newsLists
```

- 返回：

```
{
  "message": "ok",
  "timestamp": 1500544643087,
  "payload": [
        {
              "title": "百度百科下架百科词条代编代写违规商品",
              "time": "2017-07-28 09:50:00",
              "source": "新浪科技",
              "tag": "",
              "company_id": 1,
              "table_id": 13,
              "data_id": 59
          },
          {
              "title": "PayPal第二季度净营收31.36亿美元 增长27%",
              "time": "2017-07-28 09:43:32",
              "source": "ebrun亿邦动力",
              "tag": "#百度",
              "company_id": 1,
              "table_id": 13,
              "data_id": 56
          }
    ]

}
```

### 3.2. 资讯详情

- 地址：`/api/news/newsInfo`


- 请求格式

  - HTTP请求方式： `POST`
  - 支持格式： `JSON`

- 请求参数

  | 参数       | 类型   | 默认值  | 中文名  | 描述                                       |
  | :------- | :--- | :--- | :--- | :--------------------------------------- |
  | table_id | Int  |      | 数据表名 | table_id(非空)的值不同，对应的新闻类型也不同，具体情况如下：                                                                                1=>法律诉讼      2=>法院公告   3=>失信人                               4=>被执行人      5=>行政处罚   6=>严重违法                          7=>股权出质      8=>动产抵押   9=>欠税公告                       10=>经营异常   11=>开庭公告 12=>司法拍卖                 13=>新闻舆情 |
  | data_id  | Int  |      | 新闻id | data_id(非空)新闻类型的某条新闻 例如：table_id=1,data_id=2即新闻类型是法律诉讼的第二条新闻 |

- 返回格式

  | 参数        | 类型     | 默认值  | 字段说明     | 描述            |
  | :-------- | :----- | :--- | :------- | :------------ |
  | message   | string |      | 状态信息     | ok表示成功，其他均为失败 |
  | timestamp | long   |      | 服务器毫秒时间戳 |               |
  | payload   | object |      | 附加数据     | [data]        |

- s payload.data是一个数组，table_id值不同，返回的字段也不同

  ​

- 当table_id=1时，返回字段如下

  | 序号   | 参数         | 类型       | 字段说明   | 描述   |
  | ---- | ---------- | -------- | ------ | ---- |
  | 1    | submittime | Datatime | 提交时间   |      |
  | 2    | title      | Varchar  | 标题     |      |
  | 3    | casetype   | Varchar  | 案件类型   |      |
  | 4    | caseno     | Varchar  | 案件号    |      |
  | 5    | court      | Varchar  | 法院     |      |
  | 6    | doctype    | Varchar  | 文书类型   |      |
  | 7    | url        | Varchar  | 原文链接地址 |      |
  | 8    | uuid       | Varchar  | 唯一标识符  |      |

示例

- 请求：

```
http://192.168.1.64:8081/api/news/newsInfo
```

- 返回：

```
{
	"message":"ok",
	"timestamp":1501138492151,
	"payload":
		{
		"submittime":"2017-07-26 19:34:22",
		"title":"111",
		"casetype":"民事案件",
		"caseno":"（2016）晋0106民初1174号",
		"court":"太原市迎泽区人民法院",
		"doctype":"民事裁定书",
		"url":"http:\/\/wenshu.com",
		"uuid":"e1bf8bb9f39446809c6c839669ad4a84"
		}
}
```



- 当table_id=2时，返回字段如下

  | 序号   | 参数           | 类型       | 字段说明   | 描述   |
  | ---- | ------------ | -------- | ------ | ---- |
  | 1    | publishdate  | Datatime | 刊登日期   |      |
  | 2    | party1       | Varchar  | 原告     |      |
  | 3    | party2       | Varchar  | 当事人    |      |
  | 4    | bltntypename | Varchar  | 公告类型名称 |      |
  | 5    | courtcode    | Varchar  | 法院名    |      |
  | 6    | content      | Text     | 案件内容   |      |

示例

- 请求：

```
http://192.168.1.64:8081/api/news/newsInfo
```

- 返回：

```
{
	"message":"ok",
	"timestamp":1501139313047,
	"payload":
		{
		"publishdate":"2017-07-27 15:05:09",
		"party1":"李盛彬",
		"party2":"杨伟",
		"bltntypename":"起诉状副本及开庭传票",
		"courtcode":"古丈县人民法院",
		"content":"杨伟：本院受理原告李盛彬诉你及北京百度网讯科技有限公司买卖合同纠纷一案，现依法向你公			告送达起诉"
		}
	}
```



- 当table_id=3时，返回字段如下

  | 序号   | 参数          | 类型       | 字段说明        | 描述   |
  | ---- | ----------- | -------- | ----------- | ---- |
  | 1    | iname       | Varchar  | 失信人名或公司名称   |      |
  | 2    | casecode    | Varchar  | 执行依据文号      |      |
  | 3    | cardnum     | Varchar  | 身份证号／组织机构代码 |      |
  | 4    | areaname    | Varchar  | 省份          |      |
  | 5    | courtname   | Varchar  | 执行法院        |      |
  | 6    | gistid      | Varchar  | 案号          |      |
  | 7    | regdate     | Datetime | 立案时间        |      |
  | 8    | gistunit    | Varchar  | 做出执行依据单位    |      |
  | 9    | duty        | Text     | 法律生效文书确定的义务 |      |
  | 10   | performance | Varchar  | 被执行人的履行情况   |      |
  | 11   | publishdate | Datetime | 发布时间        |      |

示例

- 请求：

```
http://192.168.1.64:8081/api/news/newsInfo
```

- 返回：

```
{
	"message":"ok",
	"timestamp":1501140536547,
	"payload":
		{
		"iname":"天津信裕房地产发展有限公司",
		"casecode":"(2016)津0103执4676号",
		"cardnum":"60056316-5",
		"areaname":"天津",
		"courtname":"天津市河西区人民法院",
		"regdate":"(2014)西民一初字第751号",
		"gistunit":"天津市河西区人民法院",
		"duty":"一、被告于2014年12月31日前返还原告借款200000元，于2015年12月31日前返还原告剩余,
		"performance":"全部未履行",
		"publishdate":"2017-07-27 15:28:20"
		}
}
```



- 当table_id=4时，返回字段如下

  | 序号   | 参数             | 类型       | 字段说明 | 描述   |
  | ---- | -------------- | -------- | ---- | ---- |
  | 1    | caseCreateTime | Datetime | 立案时间 |      |
  | 2    | execMoney      | Varchar  | 执行标的 |      |
  | 3    | caseCode       | Varchar  | 案号   |      |
  | 4    | execCourtName  | Varchar  | 执行法院 |      |

示例

- 请求：

```
http://192.168.1.64:8081/api/news/newsInfo
```

- 返回：

```
{
	"message":"ok",
	"timestamp":1501141580304,
	"payload":
		{
		"caseCreateTime":"2017-07-27 15:38:37",
		"execMoney":"北京市民法院",
		"caseCode":"(2016)京1066号",
		"execCourtName":"北京市海淀区"
		}
}
```



- 当table_id=5时，返回字段如下

  | 序号   | 参数             | 类型       | 字段说明         | 描述   |
  | ---- | -------------- | -------- | ------------ | ---- |
  | 1    | decisionDate   | Datetime | 行政处罚日期       |      |
  | 2    | punishNumber   | Varchar  | 行政处罚决定书文号    |      |
  | 3    | type           | Varchar  | 违法行为类型       |      |
  | 4    | departmentName | Varchar  | 作出行政处罚决定机关名称 |      |
  | 5    | content        | Text     | 行政处罚内容       |      |

示例

- 请求：

```
http://192.168.1.64:8081/api/news/newsInfo
```

- 返回：

```
{
	"message":"ok",
	"timestamp":1501142066422,
	"payload":
		{
		"decisionDate":"2017-07-27 13:49:21",
		"punishNumber":"德工商处字(2015)303 号",
		"type":"违反,对擅自销售卫星地面接收设施的行政处罚",
		"departmentName":"福建省德化县工商行政管理局",
		"content":"罚款0.1万元，没收违法所得和非法财物"
		}
}
```



- 当table_id=6时，返回字段如下

  | 序号   | 参数               | 类型       | 字段说明          | 描述   |
  | ---- | ---------------- | -------- | ------------- | ---- |
  | 1    | putDate          | Datetime | 列入日期          |      |
  | 2    | putReason        | Text     | 列入原因          |      |
  | 3    | putDepartment    | Varchar  | 决定列入部门(作出决定机关 |      |
  | 4    | removeReason     | Text     | 移除原因          |      |
  | 5    | removeDepartment | Varchar  | 决定移除部门        |      |

示例

- 请求：

```
http://192.168.1.64:8081/api/news/newsInfo
```

- 返回：

```
{
	"message":"ok",
	"timestamp":1501142982940,
	"payload":
		{
		"putDate":"2017-07-27 16:04:47",
		"putReason":"提交虚假材料或者采取其他欺诈手段隐瞒重要事实，取得公司变更或者注销登记，被撤销登记的",
		"putDepartment":"陕西省工商行政管理局",
		"removeReason":null,
		"removeDepartment":null
		}
}
```



- 当table_id=7时，返回字段如下

  | 序号   | 参数            | 类型       | 字段说明       | 描述   |
  | ---- | ------------- | -------- | ---------- | ---- |
  | 1    | regNumber     | Varchar  | 登记编号       |      |
  | 2    | pledgor       | Varchar  | 出质人        |      |
  | 3    | pledgee       | Varchar  | 质权人        |      |
  | 4    | state         | Varchar  | 状态         |      |
  | 5    | equityAmount  | Smallint | 出质股权数额     |      |
  | 6    | certifNumberR | Varchar  | 质权人证照/证件号码 |      |
  | 7    | regDate       | Datetime | 股权出质设立登记日期 |      |

示例

- 请求：

```
http://192.168.1.64:8081/api/news/newsInfo
```

- 返回：

```
{
	"message":"ok",
	"timestamp":1501144621304,
	"payload":
		{
		"regNumber":"0267",
		"pledgor":"夏立江",
		"pledgee":"兰州汇通投资担保有限公司",
		"state":"无效",
		"equityAmount":380,
		"certifNumberR":"4535",
		"regDate":"2017-07-27 16:24:55"
		}
}
```



- 当table_id=8时，返回字段如下

  | 序号   | 参数             | 类型      | 字段说明         | 描述   |
  | ---- | -------------- | ------- | ------------ | ---- |
  | 1    | regDate        | Datetme | 登记日期         |      |
  | 2    | regNum         | Varchar | 登记编号         |      |
  | 3    | type           | Varchar | 被担保债权种类      |      |
  | 4    | amount         | Varchar | 被担保债权数额      |      |
  | 5    | regDepartment  | Varchar | 登记机关         |      |
  | 6    | term           | Varchar | 债务人履行债务的期限   |      |
  | 8    | scope          | Varchar | 担保范围         |      |
  | 9    | remark         | Text    | 备注           |      |
  | 10   | overviewType   | Varchar | 概况种类         |      |
  | 11   | overviewAmount | Varchar | 概况数额         |      |
  | 12   | overviewScope  | Varchar | 概况担保的范围      |      |
  | 13   | overviewTerm   | Varchar | 概况债务人履行债务的期限 |      |
  | 14   | overviewRemark | Text    | 概况备注         |      |
  | 15   | pawnInfoList   | Text    | 抵押物信息json数据  |      |
  | 16   | peopleInfoList | Text    | 抵押人信息json数据  |      |

- pawnInfoList  (抵押物信息)  字段说明

  | 返回值字段     | 字段类型   | 字段说明            |
  | --------- | ------ | --------------- |
  | pawnName  | String | 名称              |
  | ownership | String | 所有权归属           |
  | detail    | String | 数量、质量、状况、所在地等情况 |
  | remark    | String | 备注              |

- peopleInfoList（抵押人信息） 字段说明

  | 返回值字段      | 字段类型   | 字段说明        |
  | ---------- | ------ | ----------- |
  | peopleName | String | 抵押权人名称      |
  | liceseType | String | 抵押权人证照/证件类型 |
  | licenseNum | String | 证照/证件号码     |

示例

- 请求：

```
http://192.168.1.64:8081/api/news/newsInfo
```

- 返回：

```
{
	"message":"ok",
	"timestamp":1501655074984,
	"payload":
		{"regDate":"2015-01-08 00:00:00",
		"regNum":"冶工商押登字[2014]646",
		"type":"借款合同",
		"amount":"300万元",
		"regDepartment":"大冶市工商行政管理局",
		"term":null,"scope":"主债权、利息等，详见合同",
		"remark":"",
		"overviewType":null,
		"overviewAmount":null,
		"overviewScope":null,
		"overviewRemark":null,
		"pawnInfoList":
			[{"detail":"共1台，价值40万元，其他同上",
			"ownership":"自有",
			"pawnName":"泥浆泵"
			}],
		"peopleInfoList":
			[{"licenseNum":"420281000007873",
			"peopleName":"黄石市中小企业信用担保有限责任公司大冶分公司",
			"liceseType":"营业执照"
			}]
		}
}
```



- 当table_id=9时，返回字段如下

  | 序号   | 参数               | 类型       | 字段说明      | 描述      |
  | ---- | ---------------- | -------- | --------- | ------- |
  | 1    | name             | Varchar  | 纳税人名称     |         |
  | 2    | taxCategory      | Varchar  | 欠税税种      |         |
  | 3    | personIdNumber   | Varchar  | 证件号码      |         |
  | 4    | legalpersonName  | Varchar  | 法人或负责人名称  |         |
  | 5    | location         | Varchar  | 经营地点      |         |
  | 6    | newOwnTaxBalance | Int      | 当前新发生欠税余额 |         |
  | 7    | ownTaxBalance    | Int      | 欠税余额      |         |
  | 8    | taxIdNumber      | Varchar  | 纳税人识别号    |         |
  | 9    | type             | Varchar  | 类型        | 0国税 1地税 |
  | 10   | publishDate      | Datetime | 发布时间      |         |
  | 11   | tax_authority    | Varchar  | 税务机关      |         |

示例

- 请求：

```
http://192.168.1.64:8081/api/news/newsInfo
```

- 返回：

```
{
	"message":"ok",
	"timestamp":1501147665723,
	"payload":
		{
		"name":"大爷",
		"taxCategory":"营业税、城市维护建设税等",
		"personIdNumber":"32423423",
		"location":"洪山区珞瑜路196号",
		"legalpersonName":"小华",
		"newOwnTaxBalance":23,
		"ownTaxBalance":42324,
		"taxIdNumber":"234",
		"type":1
		"publishDate":"2017-07-27 17:36:02",
		"tax_authority"："洪山国税局"
		}
}
```



- 当table_id=10时，返回字段如下

  | 序号   | 参数               | 类型       | 字段说明       | 描述   |
  | ---- | ---------------- | -------- | ---------- | ---- |
  | 1    | putDate          | Datetime | 列入日期       |      |
  | 2    | putReason        | Varchar  | 列入经营异常名录原因 |      |
  | 3    | putDepartment    | Varchar  | 列入部门       |      |
  | 4    | removeDate       | Datetime | 移出日期       |      |
  | 5    | removeReason     | Varchar  | 移出原因       |      |
  | 6    | removeDepartment | Varchar  | 移出部门       |      |


示例

- 请求：

```
http://192.168.1.64:8081/api/news/newsInfo
```

- 返回：

```
{
	"message":"ok",
	"timestamp":1501148338677,
	"payload":
		{
		"putDate":"2017-07-27 17:36:02",
		"putReason":"未按照规定报送2014年度年度报告",
		"putDepartment":"长沙市工商行政管理局天心分局",
		"removeDate":"2017-07-29 17:36:26",
		"removeReason":"依法补报了未报年份的年度报告并公示",
		"removeDepartment":"长沙市工商行政管理局天心分局"
		}
}
```



- 当table_id=11时，返回字段如下

  | 序号   | 参数                   | 类型       | 字段说明    | 描述   |
  | ---- | -------------------- | -------- | ------- | ---- |
  | 1    | case_name            | Varchar  | 案由      |      |
  | 2    | caseno               | Varchar  | 案号      |      |
  | 3    | court_date           | Datetime | 开庭日期    |      |
  | 4    | schedu_date          | Datetime | 排期日期    |      |
  | 5    | undertake_department | Varchar  | 承办部门    |      |
  | 6    | presiding_judge      | Varchar  | 审判长/主审人 |      |
  | 7    | appellant            | Varchar  | 上诉人     |      |
  | 8    | appellee             | Varchar  | 被上诉人    |      |
  | 9    | court                | Varchar  | 法院      |      |
  | 10   | courtroom            | Varchar  | 法庭      |      |
  | 11   | area                 | Varchar  | 地区      |      |

示例

- 请求：

```
http://192.168.1.64:8081/api/news/newsInfo
```

- 返回：

```
{
	"message":"ok",
	"timestamp":1501149285579,
	"payload":
		{
		"case_name":"赔偿案",
		"caseno":"213131",
		"court_date":"2017-07-27 17:49:33",
		"area":"上海",
		"schedu_date":"2017-07-27 17:49:39",
		"undertake_department":"上海人民法院",
		"presiding_judge":"小王",
		"appellant":"张三",
		"appellee":"李四",
		"court":"上海法院",
		"courtroom":"上海法庭"
		}
}
```



- 当table_id=12时，返回字段如下

  | 序号   | 参数              | 类型       | 字段说明     | 描述   |
  | ---- | --------------- | -------- | -------- | ---- |
  | 1    | title           | Varchar  | 标题       |      |
  | 2    | auction_time    | Datetime | 委托法院拍卖时间 |      |
  | 3    | entrusted_court | Varchar  | 委托法院内容   |      |
  | 4    | content         | Text     | 内容       |      |

示例

- 请求：

```
http://192.168.1.64:8081/api/news/newsInfo
```

- 返回：

```
{
	"message":"ok",
	"timestamp":1501150196248,
	"payload":
		{"
		title":"这是xxx标题",
		"auction_time":"2017-07-27 18:06:58",
		"entrusted_court":"委托法院干一些事",
		"content":"这是内容"
		}
}
```



- 当table_id=13时，返回字段如下

  | 序号   | 参数             | 类型   | 字段说明 | 描述   |
  | ---- | -------------- | ---- | ---- | ---- |
  | 1    | format_content | Text | 内容   |      |

示例

- 请求：

```
http://192.168.1.64:8081/api/news/newsInfo
```

- 返回：

```
{
	"message":"ok",
	"timestamp":1501150196248,
	"payload":
		{"
		"format_content":"这是内容"
		}
}
```

### 3.3. 新增公司关注接口

- 地址：`/api/news/ncFocus`


- 请求格式

  - HTTP请求方式： `POST`
  - 支持格式： `JSON`

- 请求参数

  | 参数         | 类型   | 默认值  | 中文名    | 描述   |
  | ---------- | ---- | ---- | ------ | ---- |
  | uid        | Int  |      | 登录用户id | 非空   |
  | company_id | Int  |      | 公司id   | 非空   |

  ​

- 返回格式

  | 参数        | 类型     | 默认值  | 中文名      | 描述            |
  | :-------- | :----- | :--- | :------- | :------------ |
  | message   | string |      | 状态信息     | ok表示成功，其他均为失败 |
  | timestamp | long   |      | 服务器毫秒时间戳 |               |
  | payload   | object |      | 附加数据     | 1             |

示例

- 请求：

```
http://192.168.1.64:8081/api/news/ncFocus
```

- 返回：

```
{
  "message": "ok",
  "timestamp": 1500544643087,
  "payload": 1
}
```

# 4. 项目接口

### 4.1. 项目列表

- 地址：`/api/news/focusCompanyLists`


- 请求格式

  - HTTP请求方式： `POST`
  - 支持格式： `JSON`

- 请求参数

  | 参数       | 类型   | 默认值  | 中文名    | 描述                      |
  | -------- | ---- | ---- | ------ | ----------------------- |
  | page     | Int  | 1    | 页数     | 第几页（可空）                 |
  | pageSize | Int  | 20   | 每页数量   | 可空                      |
  | user_id  | Int  |      | 登录用户id | 可空（如果传递了user_id,则表示关注）  |
  | type     | Int  |      | 类型     | 可空（1是拟投，2是已投，若空则取拟投+已投） |

  ​

- 返回格式

  | 参数        | 类型     | 默认值  | 中文名      | 描述            |
  | :-------- | :----- | :--- | :------- | :------------ |
  | message   | string |      | 状态信息     | ok表示成功，其他均为失败 |
  | timestamp | long   |      | 服务器毫秒时间戳 |               |
  | payload   | object |      | 附加数据     | [data]        |

- payload.data是一个数组，结构如下：下面是全部勾选情况下的数据，实际以具体勾选的选项为准

  | 序号   | 参数          | 类型       | 字段说明    | 描述                          |
  | ---- | ----------- | -------- | ------- | --------------------------- |
  | 1    | name        | Varchar  | 公司名称    |                             |
  | 2    | state       | Datetime | 现状      | A轮  B轮  类似这些（type=2时取此数据）   |
  | 3    | update_time | Varchar  | 最近更新时间  | （type=2时取此数据）               |
  | 4    | add_time    | Varchar  | 录入时间    | （type=1时取此数据）               |
  | 5    | status      | Int      | 状态（默认1） | 0禁用 1准备中  2已完成（type=1时取此数据） |

示例

- 请求：

```
http://192.168.1.64:8081/api/news/focusCompanyLists
```

- 返回：

```
{
	"message":"ok",
	"timestamp":1501226954687,
	"payload":
		[
		{"name":"北京百度网度科技有限公司",
		"state":null,
		"update_time":null,
		"add_time":null,
		"status":1
		},
		{"name":"北京聚能鼎力科技股份有限公司",
		"state":null,
		"update_time":null,
		"add_time":null,
		"status":1
		},
		{"name":"北京麦轮泰电子商务股份有限公司",
		"state":null,
		"update_time":null,
		"add_time":null,
		"status":1
		}]
}
```

### 4.2. 申请项目新增数据接口

- 地址：`/api/news/applyNewProject`


- 请求格式

  - HTTP请求方式： `POST`
  - 支持格式： `JSON`

- 请求参数

  | 参数              | 类型      | 默认值  | 中文名      | 描述       |
  | --------------- | ------- | ---- | -------- | -------- |
  | name            | varchar |      | 公司名称     | 必传参数（非空） |
  | legalPersonName | varchar |      | 法人       | 可空       |
  | regLocation     | varchar |      | 注册地址     | 可空       |
  | creditCode      | varchar |      | 统一社会信用代码 | 可空       |
  | info            | text    |      | 其他信息     | 可空       |

  ​

- 返回格式

  | 参数        | 类型     | 默认值  | 中文名      | 描述            |
  | :-------- | :----- | :--- | :------- | :------------ |
  | message   | string |      | 状态信息     | ok表示成功，其他均为失败 |
  | timestamp | long   |      | 服务器毫秒时间戳 |               |
  | payload   | object |      | 附加数据     |               |

示例

- 请求：

```
http://192.168.1.64:8081/api/news/applyNewProject
```

- 返回：

```
{
	"message":"ok",
	"timestamp":1501237308579,
	"payload":null
	}
```

# 5. 上市信息接口

### 5.1. 股票行情接口

- 地址：/api/Listinformation/volatility


- 请求格式

  - HTTP请求方式： `POST`
  - 支持格式： `JSON`

- 请求参数

  | 参数         | 类型   | 默认值  | 中文名  | 描述   |
  | ---------- | ---- | ---- | ---- | ---- |
  | company_id | Int  |      | 公司ID | 非空   |

  ​

- 返回格式

  | 参数        | 类型     | 默认值  | 中文名      | 描述            |
  | :-------- | :----- | :--- | :------- | :------------ |
  | message   | string |      | 状态信息     | ok表示成功，其他均为失败 |
  | timestamp | long   |      | 服务器毫秒时间戳 |               |
  | payload   | object |      | 附加数据     | [data]        |

- payload.data是一个数组，结构如下：下面是全部勾选情况下的数据，实际以具体勾选的选项为准

  | 序号   | 参数               | 类型       | 字段说明 | 描述   |
  | ---- | :--------------- | -------- | ---- | ---- |
  | 1    | stockcode        | Int      | 股票号  |      |
  | 2    | stockname        | Varchar  | 股票名  |      |
  | 3    | timeshow         | Datetime | 更新时间 |      |
  | 4    | fvaluep          | Varchar  | 市盈率  |      |
  | 5    | tvalue           | Varchar  | 总市值  |      |
  | 6    | flowvalue        | Varchar  | 流通市值 |      |
  | 7    | tvaluep          | Varchar  | 市净率  |      |
  | 8    | topenprice       | Varchar  | 今开   |      |
  | 9    | tamount          | Varchar  | 成交量  |      |
  | 10   | trange           | Varchar  | 振幅   |      |
  | 11   | thighprice       | Varchar  | 最高   |      |
  | 12   | tamounttotal     | Varchar  | 成交额  |      |
  | 13   | tchange          | Varchar  | 换手   |      |
  | 14   | tlowprice        | Varchar  | 最低   |      |
  | 15   | pprice           | Varchar  | 昨收   |      |
  | 16   | tmaxprice        | Varchar  | 涨停   |      |
  | 17   | tminprice        | Varchar  | 跌停   |      |
  | 18   | hexm_curPrice    | Varchar  | 当前价格 |      |
  | 19   | hexm_float_price | Varchar  | 涨跌   |      |
  | 20   | hexm_float_rate  | Varchar  | 涨跌幅  |      |

示例

- 请求：

```
http://192.168.1.64:8081/api/Listinformation/volatility
```

- 返回：

```
{
	"message":"ok",
	"timestamp":1501741579705,
	"payload":		    
		{
		"stockcode":1,
		"stockname":"\u5e73\u5b89\u94f6\u884c",
		"stockType":1,
		"timeshow":"2017-08-02 15:50:04",
		"fvaluep":"6.321",
		"tvalue":"0.84",
		"flowvalue":"1548.00\u4ebf",
		"tvaluep":"0.84",
		"topenprice":"9.25",
		"tamount":"4969.32\u4e07",
		"trange":"0.77%",
		"thighprice":"9.18",
		"tamounttotal":"4.54\u4ebf",
		"tchange":"0.29%",
		"tlowprice":"9.11",
		"pprice":"9.12",
		"tmaxprice":"10.03",
		"tminprice":"8.21",
		"hexm_curPrice":"9.15",
		"hexm_float_price":"0.03",
		"hexm_float_rate":"0.03%"
	}
}
```

### 