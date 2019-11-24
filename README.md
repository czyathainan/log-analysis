# log-analysis
WEB环境下文本日志分析查找工具，支持Apache, Nginx日志

请将网站日志文件放到 /log-analysis/log/ 目录下，然后在本地访问 http://127.0.0.1/log-analysis/index.php 即可开始使用。

测试结果：在百万行的日志中查找指定的访问日志，本工具可以几十秒内轻松完成，如果您想追求更高的分析/查找效率，请在配置更好的电脑上运行该程序。

在index.php程序中，每分析20000行日志会休眠1秒，这是为了防止数据量过大造成卡死现象，您可以根据您的电脑配置适当调整该参数，代码如下：
if($all_line % 20000 == 0)
{
	flush();
	ob_flush();
	sleep(1);
}

程序支持运行环境LAMP/LNMP/WAMP/WNMP