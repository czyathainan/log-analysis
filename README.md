# log-analysis

#### 介绍
WEB日志分析查找工具，支持任何文本日志文件的分析(Apache, Nginx)，多关键词查找、关键词排除，轻松处理百万行日志内容，可以很好的分析出CC攻击、嗅探/注入、恶意采集等网络攻击来源


#### 软件架构
PHP语言编写的日志查找工具，需要安装php运行环境


#### 安装使用教程
1. 将文本日志文件复制到 ./log-analysis/log/ 目录下，然后通过浏览器访问项目: http://127.0.0.1/log-analysis
2. 在浏览器输出界面中分别设置好你要查找和排除的关键词集合，点击【保存规则】
3. 在浏览器输出界面左上角点击你要查询日志文件名即可开始查找，等几秒就能看到匹配结果了


#### 查找规则设置
设置【查找词关系】：为and则必须同时匹配所有关键词才能输出该行，为or则匹配至少一个关键词即可输出该行；<br />
设置【匹配范围】：选择“整行”则会匹配整行内容，选择“受访URL”则将行内容以"HTTP/1."截断匹配左侧字符串；<br />
设置【优先级】：当行内容同时存在查找词和排除词时，选择“查找优先”则会输出该行；选择“排除优先”则会忽略该行；<br />




[<img src="https://api.gitsponsors.com/api/badge/img?id=179625452" height="20">](https://api.gitsponsors.com/api/badge/link?p=kIWlJLB8yDXphtwVgCMa8bkaJSqk+217sVIvz0PLz0sswiLwjopO+Kn7EhQPSWV0QhhfZkaIjByHXg4gbVGktFqn4ofazoOPY2VX6mT2kkpZSsm0bNppGXX0CumOQTFBaxGq8v4xYvHq/wEExNDDMg==)
