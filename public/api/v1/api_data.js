define({ "api": [  {    "type": "GET",    "url": "/api/v1/access/list",    "title": "9-获取预约申请—门禁权限列表",    "group": "CMS",    "version": "1.0.1",    "description": "<p>获取预约申请—门禁权限列表</p>",    "examples": [      {        "title": "请求样例:",        "content": "http://maintain.mengant.cn/api/v1/access/list??department=全部&username=朱明良&time_begin=2018-10-01&time_end=2018-12-31&status=0&access=全部&page=1&size=20",        "type": "get"      }    ],    "parameter": {      "fields": {        "请求参数说明": [          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "department",            "description": "<p>默认传入全部</p>"          },          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "username",            "description": "<p>申请人</p>"          },          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "time_begin",            "description": "<p>开始时间</p>"          },          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "time_end",            "description": "<p>截止时间</p>"          },          {            "group": "请求参数说明",            "type": "int",            "optional": false,            "field": "status",            "description": "<p>流程状态：-1 | 不通过；0 | 保存中；1 | 流程中； 2 | 通过；3 | 获取全部</p>"          },          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "access",            "description": "<p>开通功能</p>"          },          {            "group": "请求参数说明",            "type": "int",            "optional": false,            "field": "page",            "description": "<p>当前页码</p>"          },          {            "group": "请求参数说明",            "type": "int",            "optional": false,            "field": "size",            "description": "<p>每页多少条数据</p>"          }        ]      }    },    "success": {      "examples": [        {          "title": "返回样例:",          "content": "{\"total\":1,\"per_page\":20,\"current_page\":1,\"last_page\":1,\"data\":[{\"id\":1,\"create_time\":\"2018-12-03 10:26:55\",\"username\":\"朱明良\",\"department\":\"办公室\",\"role_name\":\"管理员\",\"user_type\":\"干部职工\",\"access\":\"资料室,会议室\",\"deadline\":\"2018-12-30 00:00:00\",\"status\":0,\"admin_id\":1}]}",          "type": "json"        }      ],      "fields": {        "返回参数说明": [          {            "group": "返回参数说明",            "type": "int",            "optional": false,            "field": "total",            "description": "<p>数据总数</p>"          },          {            "group": "返回参数说明",            "type": "int",            "optional": false,            "field": "per_page",            "description": "<p>每页多少条数据</p>"          },          {            "group": "返回参数说明",            "type": "int",            "optional": false,            "field": "current_page",            "description": "<p>当前页码</p>"          },          {            "group": "返回参数说明",            "type": "int",            "optional": false,            "field": "last_page",            "description": "<p>最后页码</p>"          },          {            "group": "返回参数说明",            "type": "int",            "optional": false,            "field": "id",            "description": "<p>申请id</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "create_time",            "description": "<p>日期</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "username",            "description": "<p>申请人</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "department",            "description": "<p>部门</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "role_name",            "description": "<p>职务</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "user_type",            "description": "<p>人员类型</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "access",            "description": "<p>开通功能</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "deadline",            "description": "<p>工作截止时间</p>"          },          {            "group": "返回参数说明",            "type": "int",            "optional": false,            "field": "status",            "description": "<p>流程状态：-1 | 不通过；0 | 保存中；1 | 流程中； 2 | 通过</p>"          },          {            "group": "返回参数说明",            "type": "int",            "optional": false,            "field": "admin_id",            "description": "<p>发起人id</p>"          }        ]      }    },    "filename": "application/api/controller/v1/AccessControl.php",    "groupTitle": "CMS",    "name": "GetApiV1AccessList"  },  {    "type": "GET",    "url": "/api/v1/token/admin",    "title": "1-CMS获取登陆token",    "group": "CMS",    "version": "1.0.1",    "description": "<p>后台用户登录</p>",    "examples": [      {        "title": "请求样例:",        "content": "{\n   \"account\": \"18956225230\",\n   \"pwd\": \"a123456\"\n }",        "type": "post"      }    ],    "parameter": {      "fields": {        "请求参数说明": [          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "account",            "description": "<p>账号</p>"          },          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "pwd",            "description": "<p>用户密码</p>"          }        ]      }    },    "success": {      "examples": [        {          "title": "返回样例:",          "content": "{\"u_id\":1,\"username\":\"朱明良\",\"account\":\"admin\",\"role\":1,\"token\":\"7488c7a7b1f79ed99b319f141637519c\"}",          "type": "json"        }      ],      "fields": {        "返回参数说明": [          {            "group": "返回参数说明",            "type": "int",            "optional": false,            "field": "u_id",            "description": "<p>用户id</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "username",            "description": "<p>用户名称</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "account",            "description": "<p>账号</p>"          },          {            "group": "返回参数说明",            "type": "int",            "optional": false,            "field": "role",            "description": "<p>用户角色：暂定</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "token",            "description": "<p>口令令牌，每次请求接口需要放在header里传入，有效期 2 hours</p>"          }        ]      }    },    "filename": "application/api/controller/v1/Token.php",    "groupTitle": "CMS",    "name": "GetApiV1TokenAdmin"  },  {    "type": "GET",    "url": "/api/v1/token/login/out",    "title": "2-CMS退出登陆",    "group": "CMS",    "version": "1.0.1",    "description": "<p>CMS退出当前账号登陆。</p>",    "examples": [      {        "title": "请求样例:",        "content": "http://maintain.mengant.cn/api/v1/token/loginOut",        "type": "get"      }    ],    "success": {      "examples": [        {          "title": "返回样例:",          "content": "{\"msg\":\"ok\",\"errorCode\":0}",          "type": "json"        }      ],      "fields": {        "返回参数说明": [          {            "group": "返回参数说明",            "type": "int",            "optional": false,            "field": "error_code",            "description": "<p>错误码： 0表示操作成功无错误</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "msg",            "description": "<p>信息描述</p>"          }        ]      }    },    "filename": "application/api/controller/v1/Token.php",    "groupTitle": "CMS",    "name": "GetApiV1TokenLoginOut"  },  {    "type": "POST",    "url": "/api/v1/access/save",    "title": "5-CMS-新增预约申请—门禁权限",    "group": "CMS",    "version": "1.0.1",    "description": "<p>预约申请—门禁权限</p>",    "examples": [      {        "title": "请求样例:",        "content": "{\n   \"access\": \"资料室\",\n   \"deadline\": \"2018-12-30\"\n   \"user_type\": \"干部职工\"\n }",        "type": "post"      }    ],    "parameter": {      "fields": {        "请求参数说明": [          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "access",            "description": "<p>申请功能</p>"          },          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "deadline",            "description": "<p>截止时间</p>"          },          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "user_type",            "description": "<p>人员类型</p>"          }        ]      }    },    "success": {      "examples": [        {          "title": "返回样例:",          "content": "{\"msg\":\"ok\",\"errorCode\":0}",          "type": "json"        }      ],      "fields": {        "返回参数说明": [          {            "group": "返回参数说明",            "type": "int",            "optional": false,            "field": "error_code",            "description": "<p>错误代码 0 表示没有错误</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "msg",            "description": "<p>操作结果描述</p>"          }        ]      }    },    "filename": "application/api/controller/v1/AccessControl.php",    "groupTitle": "CMS",    "name": "PostApiV1AccessSave"  },  {    "type": "POST",    "url": "/api/v1/admin/pwd/update",    "title": "4-CMS-用户-修改密码",    "group": "CMS",    "version": "1.0.1",    "description": "<p>后台用户修改账号密码</p>",    "examples": [      {        "title": "请求样例:",        "content": "{\n   \"new_pwd\": \"aaaaaa\",\n   \"old_pwd\": \"a123456\"\n }",        "type": "post"      }    ],    "parameter": {      "fields": {        "请求参数说明": [          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "new_pwd",            "description": "<p>新密码</p>"          },          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "old_pwd",            "description": "<p>旧密码</p>"          }        ]      }    },    "success": {      "examples": [        {          "title": "返回样例:",          "content": "{\"msg\":\"ok\",\"errorCode\":0}",          "type": "json"        }      ],      "fields": {        "返回参数说明": [          {            "group": "返回参数说明",            "type": "int",            "optional": false,            "field": "error_code",            "description": "<p>错误代码 0 表示没有错误</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "msg",            "description": "<p>操作结果描述</p>"          }        ]      }    },    "filename": "application/api/controller/v1/Admin.php",    "groupTitle": "CMS",    "name": "PostApiV1AdminPwdUpdate"  },  {    "type": "POST",    "url": "/api/v1/admin/username/update",    "title": "3-用户-修改登录账号",    "group": "CMS",    "version": "1.0.1",    "description": "<p>后台用户修改用户名</p>",    "examples": [      {        "title": "请求样例:",        "content": "{\n   \"pwd\": \"a123456\",\n   \"account\": \"修改名字\"\n }",        "type": "post"      }    ],    "parameter": {      "fields": {        "请求参数说明": [          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "pwd",            "description": "<p>密码</p>"          },          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "account",            "description": "<p>登录账号</p>"          }        ]      }    },    "success": {      "examples": [        {          "title": "返回样例:",          "content": "{\"msg\":\"ok\",\"errorCode\":0}",          "type": "json"        }      ],      "fields": {        "返回参数说明": [          {            "group": "返回参数说明",            "type": "int",            "optional": false,            "field": "error_code",            "description": "<p>错误代码 0 表示没有错误</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "msg",            "description": "<p>操作结果描述</p>"          }        ]      }    },    "filename": "application/api/controller/v1/Admin.php",    "groupTitle": "CMS",    "name": "PostApiV1AdminUsernameUpdate"  },  {    "type": "POST",    "url": "/api/v1/meeting/delete",    "title": "8-CMS-删除会议",    "group": "CMS",    "version": "1.0.1",    "description": "<p>删除会议</p>",    "examples": [      {        "title": "请求样例:",        "content": "{\n   \"id\": \"1\"\n }",        "type": "post"      }    ],    "parameter": {      "fields": {        "请求参数说明": [          {            "group": "请求参数说明",            "type": "int",            "optional": false,            "field": "id",            "description": "<p>会议id</p>"          }        ]      }    },    "success": {      "examples": [        {          "title": "返回样例:",          "content": "{\"msg\":\"ok\",\"errorCode\":0}",          "type": "json"        }      ],      "fields": {        "返回参数说明": [          {            "group": "返回参数说明",            "type": "int",            "optional": false,            "field": "error_code",            "description": "<p>错误代码 0 表示没有错误</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "msg",            "description": "<p>操作结果描述</p>"          }        ]      }    },    "filename": "application/api/controller/v1/Meeting.php",    "groupTitle": "CMS",    "name": "PostApiV1MeetingDelete"  },  {    "type": "POST",    "url": "/api/v1/meeting/save",    "title": "6-CMS-新增会议",    "group": "CMS",    "version": "1.0.1",    "description": "<p>新增会议</p>",    "examples": [      {        "title": "请求样例:",        "content": "{\n   \"meeting_date\": \"2018-12-30\",\n   \"address\": \"101会议室\",\n   \"time_begin\": \"09：00\",\n   \"time_end\": \"09：30\",\n   \"meeting_begin\": \"10：00\",\n   \"theme\": \"全体职工大会\",\n   \"outline\": \"年终总结\",\n   \"remark\": \"必须参加\"\n }",        "type": "post"      }    ],    "parameter": {      "fields": {        "请求参数说明": [          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "meeting_date",            "description": "<p>日期</p>"          },          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "address",            "description": "<p>签到地点</p>"          },          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "time_begin",            "description": "<p>签到开始时间</p>"          },          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "time_end",            "description": "<p>签到截止时间</p>"          },          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "meeting_begin",            "description": "<p>会议开始时间</p>"          },          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "theme",            "description": "<p>会议主题</p>"          },          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "outline",            "description": "<p>内容概要</p>"          },          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "remark",            "description": "<p>备注</p>"          }        ]      }    },    "success": {      "examples": [        {          "title": "返回样例:",          "content": "{\"msg\":\"ok\",\"errorCode\":0}",          "type": "json"        }      ],      "fields": {        "返回参数说明": [          {            "group": "返回参数说明",            "type": "int",            "optional": false,            "field": "error_code",            "description": "<p>错误代码 0 表示没有错误</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "msg",            "description": "<p>操作结果描述</p>"          }        ]      }    },    "filename": "application/api/controller/v1/Meeting.php",    "groupTitle": "CMS",    "name": "PostApiV1MeetingSave"  },  {    "type": "POST",    "url": "/api/v1/meeting/update",    "title": "7-CMS-修改会议",    "group": "CMS",    "version": "1.0.1",    "description": "<p>修改会议</p>",    "examples": [      {        "title": "请求样例:",        "content": "{\n   \"id\": \"1\",\n   \"meeting_date\": \"2018-12-30\",\n   \"address\": \"101会议室\",\n   \"time_begin\": \"09：00\",\n   \"time_end\": \"09：30\",\n   \"meeting_begin\": \"10：00\",\n   \"theme\": \"全体职工大会\",\n   \"outline\": \"年终总结\",\n   \"remark\": \"必须参加\"\n }",        "type": "post"      }    ],    "parameter": {      "fields": {        "请求参数说明": [          {            "group": "请求参数说明",            "type": "int",            "optional": false,            "field": "id",            "description": "<p>会议id</p>"          },          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "meeting_date",            "description": "<p>日期</p>"          },          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "address",            "description": "<p>签到地点</p>"          },          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "time_begin",            "description": "<p>签到开始时间</p>"          },          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "time_end",            "description": "<p>签到截止时间</p>"          },          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "meeting_begin",            "description": "<p>会议开始时间</p>"          },          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "theme",            "description": "<p>会议主题</p>"          },          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "outline",            "description": "<p>内容概要</p>"          },          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "remark",            "description": "<p>备注</p>"          }        ]      }    },    "success": {      "examples": [        {          "title": "返回样例:",          "content": "{\"msg\":\"ok\",\"errorCode\":0}",          "type": "json"        }      ],      "fields": {        "返回参数说明": [          {            "group": "返回参数说明",            "type": "int",            "optional": false,            "field": "error_code",            "description": "<p>错误代码 0 表示没有错误</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "msg",            "description": "<p>操作结果描述</p>"          }        ]      }    },    "filename": "application/api/controller/v1/Meeting.php",    "groupTitle": "CMS",    "name": "PostApiV1MeetingUpdate"  }] });
