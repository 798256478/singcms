<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Exception;

/*
 * 基础配置相关操作
 */
class BasicController extends CommonController {
	//设置首页
	public function index() {
		$result = D("Basic")->select();
		$this->assign('vo', $result);
		$this->assign('type',1);
		$this->display();
	}
	//添加配置信息
	public function add() {
		if($_POST) {
			if(!$_POST['title']) {
				return res(0, '站点信息不能为空');
			}
			if(!$_POST['keywords']) {
				return res(0, '站点关键词');
			}
			if(!$_POST['description']) {
				return res(0, '站点描述');
			}

			D("Basic")->save($_POST);
			return res(1, '配置成功');
		}else {
			return res(0, '没有提交的数据');
		}
	}

	//缓存管理
	public function cache() {
		$this->assign('type',2);
		$this->display();
	}



}