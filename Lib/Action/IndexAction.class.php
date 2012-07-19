<?php
// 本类由系统自动生成，仅供测试用途
class IndexAction extends Action {
	//首页
	public function _before_index() {
		checkLogin();
	}

	public function _before_vote() {
		checkLogin();
	}

	public function _before_introduction() {
		checkLogin();
	}

	public function _before_edit() {
		checkLogin();
		if (!isAdmin()) {
			$this -> error('没有权限!', U('Index/index'));
		}
	}

	public function _before_takeedit() {
		checkLogin();
		if (!isAdmin()) {
			$this -> error('没有权限!', U('Index/index'));
		}
	}

	public function _before_globaloptions() {
		checkLogin();
		if (!isAdmin()) {
			$this -> error('没有权限!', U('Index/index'));
		}
	}

	public function index() {
		/*
		 变量赋值
		 */
		//配置
		$config = readConfig();
		$this -> assign('config', $config);
		//控制参数
		//候选列表
		$voteListDB = D('Votelist');
		$voteItem = $voteListDB -> where(array('enable' => 1)) -> order($config['sortType']) -> select();
		$this -> assign('voteItem', $voteItem);
		if (isAdmin()) {
			$voteItemNotEnable = $voteListDB -> where('enable != 1') -> select();
			$this -> assign('voteItemNotEnable', $voteItemNotEnable);
		}
		//候选列表
		$this -> display();
	}

	//介绍页面
	public function introduction() {
		$id = $this -> _get('id');
		if (!$id) {
			logErr('introduction企图输入错误ID');
			$this -> error('无法获取请求页面!', U('Index/index'));
		}
		$voteListDB = D('Votelist');
		$item = $voteListDB -> where(array(
			'id' => $id,
			'enable' => 1
		)) -> find();
		if (!$item) {
			logErr($voteListDB -> getError());
			$this -> error('无法获取请求页面!', U('Index/index'));
		}

		//配置
		$config = readConfig();
		$this -> assign('config', $config);
		//控制参数
		$this -> assign('item', $item);
		//候选列表
		$this -> display();
	}

	//投票页面
	public function vote() {
		$config = readConfig();
		$id = $this -> _get('voteid');
		if (!$id) {
			$this -> error("voteid不存在");
		}
		//检查时间要素
		if (time() > $config['endTime']) {
			$this -> error("投票已结束");
		}
		if (time() < $config['startTime']) {
			$this -> error("投票尚未开始");
		}
		//DB
		$ticket = new TicketModel();
		//检查是否满足票数
		$condition = array();
		$condition['time'] = array(
			'EGT',
			time() - $config['introductionInterval']
		);
		switch ($config[introductionActor]) {
			case 'ip' :
				$condition['ip'] = get_client_ip();
				break;
			case 'user' :
				$condition['user'] = getUser();
				break;
			default :
				break;
		}
		$count = $ticket -> where($condition) -> count($id);
		if ($count >= $config['introductionIntervalCount']) {
			$this -> error("不满足投票条件!" . $config['introductionMethodStr']);
		}
		$data = array(
			'user' => getUser(),
			'voteid' => $id
		);
		$ticket -> startTrans();
		if (!$ticket -> create($data)) {
			$ticket -> rollback();
			$msg = $ticket -> getError();
			logErr($msg);
			$this -> error('投票失败!' . $msg);
		}
		if (!$ticket -> add()) {
			$ticket -> rollback();
			$msg = $ticket -> getError();
			logErr($msg);
			$this -> error('投票失败!' . $msg);
		} else {
			$voteListDb = D('Votelist');
			if (!$voteListDb -> where('id=' . $id) -> setInc('count', 1)) {
				$ticket -> rollback();
				$msg = $voteListDb -> getError();
				logErr($msg);
				$this -> error('投票失败!' . $msg);
			}
			$ticket -> commit();
			$this -> success('投票成功！');
		}
	}

	//设置全局参数
	public function globaloptions() {
		//需要填充的字段
		$options = array(
			array(
				'key' => 'sitename',
				'name' => '站点名称',
				'type' => 'text'
			),
			array(
				'key' => 'copyright',
				'name' => '版权信息',
				'type' => 'text'
			),
			array(
				'key' => 'introductionTitle',
				'name' => '投票名称',
				'type' => 'text'
			),
			array(
				'key' => 'introduction',
				'name' => '投票内容',
				'type' => 'textarea',
				'dataType' => 'html'
			),
			array(
				'key' => 'introductionActor',
				'name' => '投票角色',
				'type' => 'text',
				'addition' => '投票角色:只能写user或ip'
			),
			array(
				'key' => 'introductionInterval',
				'name' => '投票间隔',
				'type' => 'text',
				'addition' => '单位是秒'
			),
			array(
				'key' => 'introductionIntervalCount',
				'name' => '投票间隔内票数',
				'type' => 'text'
			),
			array(
				'key' => 'introductionMethodStr',
				'name' => '投票方式说明',
				'type' => 'text'
			),
			array(
				'key' => 'itemName',
				'name' => '候选要素名称',
				'type' => 'text'
			),
			array(
				'key' => 'startTime',
				'name' => '投票开始时间',
				'type' => 'text',
				'addition' => '格式:2012-2-1 12:00:00',
				'dataType' => 'time'
			),
			array(
				'key' => 'endTime',
				'name' => '投票结束时间',
				'type' => 'text',
				'addition' => '格式:2012-2-1 12:00:00',
				'dataType' => 'time'
			),
			array(
				'key' => 'sortType',
				'name' => '排序SQL字段',
				'type' => 'text'
			),
			array(
				'key' => 'sortTypeStr',
				'name' => '排序方式说明',
				'type' => 'text'
			),
			array(
				'key' => 'adminList',
				'name' => '管理员',
				'type' => 'text',
				'addition' => '格式:ID_学号,用/分割'
			)
		);
		$configDb = D('Config');
		$errorMsg = array();
		//接受参数
		foreach ($options as $o) {
			if ($this -> _post($o['key'])) {
				$key = $o['key'];
				switch($o['dataType']) {
					case 'time' :
						$value = strtotime($this -> _post($o['key']));
						break;
					case 'html' :
						$value = htmlspecialchars_decode($this -> _post($o['key']));
						break;
					default :
						$value = $this -> _post($o['key']);
						break;
				}

				if (!$configDb -> save(array(
					'name' => $key,
					'value' => $value
				))) {
					$errorMsg[] = $configDb -> getError();
				}
			}
		}
		/*
		 变量赋值
		 */
		//配置
		$config = readConfig();
		foreach ($options as $i => $o) {
			switch ($o['dataType']) {
				case 'time' :
					$options[$i]['value'] = date('Y-m-d H:i:s', $config[$o['key']]);
					break;
				default :
					$options[$i]['value'] = $config[$o['key']];
					break;
			}
		}
		// var_dump($options);
		$this -> assign('config', $config);
		$this -> assign('options', $options);
		$this -> assign('errorMsg', $errorMsg);
		$this -> display();
	}

	//返回幻灯片数据
	public function slide($value = '') {
		//读取目录
		$slide = array();
		$voteListDb = D('Votelist');
		$id = $this -> _get('id');
		if ($id && $voteListDb -> where(array(
			'id' => $id,
			'enable' => 1
		)) -> find()) {
			$fileList = scandir(APP_PATH . "Data/item$id/slide/");
			if ($fileList) {
				foreach ($fileList as $file) {
					if (!is_dir($file)) {
						$slide[] = "Data/item$id/slide/" . $file;
					}
				}
			}
		} else {
			$msg = $voteListDb -> getError();
			if ($msg)
				logErr($msg);
		}
		echo json_encode($slide);
	}

	//编辑界面
	public function edit($value = '') {
		/*
		 变量赋值
		 */
		//配置
		$config = readConfig();
		$this -> assign('config', $config);
		$item = array(
			'title' => '',
			'subtitle' => '',
			'introduction' => '',
			'enable' => 1
		);
		$id = $this -> _get('id');
		$voteListDb = D('Votelist');
		if ($id && ($arr = $voteListDb -> where(array('id' => $id, )) -> find())) {
			$item = $arr;
		}
		$this -> assign('item', $item);
		$this -> display();
	}

	//处理编辑
	public function takeedit($value = '') {
		$voteListDB = D('Votelist');
		$data = $voteListDB -> create();
		if ($data) {
			if (isset($data['id'])) {
				$res = $voteListDB -> save();
				if ($res) {
					//获得id 用来跳转
					$res = $data['id'];
				}
			} else {
				$res = $voteListDB -> add();
			}
		}
		if ($data && $res) {
			$this -> success('编辑/修改成功', U('Index/edit?id=' . $res));
		} else {
			logErr($voteListDB -> getError());
			$this -> error('编辑修改失败!', U('Index/index'));
		}

	}

	//logout
	public function logout() {
		jacLogout();
		$this -> success('成功登出!稍后返回首页', U('Index/index'));
	}

	//login
	public function login() {
		jacLogin();
		$this -> success('成功登录!稍后返回首页', U('Index/index'));
	}

}
