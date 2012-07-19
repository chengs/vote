<?php
//model of table ticket
class TicketModel extends Model {
	//auto complete
	protected $_auto = array( array('ip', 'get_client_ip', 1, 'function'), array('time', 'time', 1, 'function'), array('date', 'getDate', 1, 'callback'));
	//必须完成
	protected $_validate = array(  
	array('user', 'require', 'user 字段必须存在', Model::MUST_VALIDATE), 
	array('voteid', 'checkVoteid', 'voteid 字段错误!', Model::MUST_VALIDATE, 'callback'), 
	);
	// protected $patchValidate = true;
	//获得日期
	function getDate() {
		return date('Y-m-d');
	}

	function checkVoteid($id) {
		if (!is_numeric($id))
			return FALSE;
		$votelist = D('Votelist');
		if ($votelist -> find(array('id' => $id, 'enable' => 1)))
			return TRUE;
		else
			return FALSE;

	}

}
?>