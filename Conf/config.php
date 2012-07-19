<?php
return array(
	//'配置项'=>'配置值'
	'DB_TYPE'                => 'pdo',
    'DB_DSN'                 => 'sqlite:'.APP_PATH.'Db/database.db', 
    'DB_PREFIX'                => '',  // 数据库表前缀
    'DB_CHARSET'            => 'utf8',      // 数据库编码默认采用utf8
    'DEFAULT_TIMEZONE'=>'Asia/Shanghai', //时区
    'URL_MODEL'=>3, //url 使用普通模式,
   	'SESSION_AUTO_START' =>true, //启动session
);
?>