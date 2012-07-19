<?php
//jaccount class file
load('@.clsJAccount');

function readConfig() {
	$DB = D('Config');
	$arr = $DB -> select();
	$config = array();
	foreach ($arr as $item) {
		$config[$item['name']] = $item['value'];
	}
	return $config;
}

//获得用户，与jaccount配合
function getUser() {
	return jacGetUser() ? jacGetUser() : 'unknown';
}

function logErr($msg) {
	$logDB = D('Log');
	$logDB -> create(array('msg' => $msg));
	$logDB -> add();
}

function checkLogin() {
	jacLogin();
}

function isAdmin() {
	$config = readConfig();
	$list=$config['adminList'];
	if (strpos($list, getUser())) {
		return TRUE;
	} else {
		return FALSE;
	}
}

/*
 jaccount 系列函数
 */
function jacObj() {
	$jam = new JAccountManager('jaourhome05303', 'E:\JAccount');
	return $jam;
}

//登出
function jacLogout() {
	$obj = jacObj();
	if ($obj -> logout(U('Index/logout'))) {
		session('jaUsername', null);
		session('jaLogin', null);
	} else {
		logErr('jacLogout Error');
	}
}

//登录
function jacLogin() {
	if (!jacGetUser()) {
		$obj = jacObj();
		$ht = $obj -> checkLogin(U('Index/login'));
		if (($ht != NULL) && ($obj -> hasTicketInURL)) {
			$obj -> redirectWithoutTicket();
		}

		if ($ht['ja3rdpartySessionID'] == session_id()) {
			session('jaUsername', $ht['uid'] . '_' . $ht['id']);
			session('jaLogin', 1);
		} else {
			logErr('jalogin error');
		}
	}
}

function jacGetUser() {
	if (session('?jaLogin') && session('?jaUsername') && session('jaLogin') == 1) {
		return session('jaUsername');
	} else {
		return FALSE;
	}
}
?>