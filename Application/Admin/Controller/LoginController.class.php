<?php
namespace Admin\Controller;
use Think\Controller;


class LoginController extends Controller {

    public function index(){
        if(session('adminUser')) {
           $this->redirect('/index.php?m=admin&c=index');
        }
        $this->display();
    }

    public function check() {
        $username = $_POST['username'];
        $password = $_POST['password'];
        if(!trim($username)) {
            return res(0,'用户名不能为空');
        }
        if(!trim($password)) {
            return res(0,'密码不能为空');
        }

        $ret = D('Admin')->getAdminByUsername($username);
        if(!$ret || $ret['status'] !=1) {
            return res(0,'该用户不存在');
        }

        if($ret['password'] != getMd5Password($password)) {
            return res(0,'密码错误');
        }

        D("Admin")->updateByAdminId($ret['admin_id'],array('lastlogintime'=>time()));

        session('admin', $ret);
        return res(1,'登录成功');
    }

    public function logout() {
        session('adminUser', null);
        $this->redirect('/index.php?m=admin&c=login');
    }

}