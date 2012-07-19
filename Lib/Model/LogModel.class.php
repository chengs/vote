<?php
//model of table log
class LogModel extends Model {
	//auto complete
	protected $_auto = array(
		array(
			'ip',
			'get_client_ip',
			1,
			'function'
		),
		array(
			'time',
			'time',
			1,
			'function'
		),
		array(
			'user',
			'getUser',
			1,
			'function'
		)
	);

}
?>