<?php
namespace Admin\Controller;
use Think\Controller;
/**
 * 公共类
 */
class CommonController extends Controller {


	public function __construct() {
		
		parent::__construct();
		$this->_init();
	}
	/**
	 * 初始化
	 * @return
	 */
	private function _init() {
		// 如果已经登录
		$isLogin = $this->isLogin();
		if(!$isLogin) {
			// 跳转到登录页面
			$this->redirect('/index.php?m=admin&c=login');
		}
	}

	/**
	 * 获取登录用户信息
	 * @return array
	 */
	public function getLoginUser() {
		return session("admin");
	}

	/**
	 * 判定是否登录
	 * @return boolean 
	 */
	public function isLogin() {
		$user = $this->getLoginUser();
		if($user && is_array($user)) {
			return true;
		}

		return false;
	}
	/**
     * 设置状态
	 */
	public function setStatus($data, $models) {
		try {
			if ($_POST) {
				$id = $data['id'];
				$status = $data['status'];
				if (!$id) {
					return res(0, 'ID不存在');
				}
				$res = D($models)->updateStatusById($id, $status);
				if ($res) {
					return res(1, '操作成功');
				} else {
					return res(0, '操作失败');
				}
			}
			return res(0, '没有提交的内容');
		}catch(Exception $e) {
			return res(0, $e->getMessage());
		}
	}

	/**
	 * 排序
	 */
	public function listorder($model='') {
		$listorder = $_POST['listorder'];
		$jumpUrl = $_SERVER['HTTP_REFERER'];
		$errors = array();
		try {
			if ($listorder) {
				foreach ($listorder as $id => $v) {
					// 执行更新
					$id = D($model)->updateListorderById($id, $v);
					if ($id === false) {
						$errors[] = $id;
					}
				}
				if ($errors) {
					return res(0, '排序失败-' . implode(',', $errors), array('jump_url' => $jumpUrl));
				}
				return res(1, '排序成功', array('jump_url' => $jumpUrl));
			}
		}catch (Exception $e) {
			return res(0, $e->getMessage());
		}
		return res(0,'排序数据失败',array('jump_url' => $jumpUrl));
	}

}