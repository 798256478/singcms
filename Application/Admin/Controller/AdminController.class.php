<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Exception;

/*
 * 管理员相关操作
 */
class AdminController extends CommonController {

    //管理员列表
    public function index() {
        $admins = D('Admin')->getAdmins();
        $this->assign('admins', $admins);
        $this->display();
    }
    //添加管理员
    public function add() {

        // 保存数据
        if(IS_POST) {

            if(!isset($_POST['username']) || !$_POST['username']) {
                return res(0, '用户名不能为空');
            }
            if(!isset($_POST['password']) || !$_POST['password']) {
                return res(0, '密码不能为空');
            }
            $_POST['password'] = getMd5Password($_POST['password']);
            // 判定用户名是否存在
            $admin = D("Admin")->getAdminByUsername($_POST['username']);
            if($admin && $admin['status']!=-1) {
                return res(0,'该用户存在');
            }

            // 新增
            $id = D("Admin")->insert($_POST);
            if(!$id) {
                return res(0, '新增失败');
            }
            return res(1, '新增成功');
        }
        $this->display();
    }

    //修改管理员状态
    public function setStatus() {
        $data = array(
            'admin_id'=>intval($_POST['id']),
            'status' => intval($_POST['status']),
        );
        return parent::setStatus($_POST,'Admin');
    }
    //管理员个人信息
    public function personal() {
        $res = $this->getLoginUser();
        $user = D("Admin")->getAdminByAdminId($res['admin_id']);
        $this->assign('vo',$user);
        $this->display();
    }
    //修改个人信息
    public function save() {
        $user = $this->getLoginUser();
        if(!$user) {
            return res(0,'用户不存在');
        }

        $data['realname'] = $_POST['realname'];
        $data['email'] = $_POST['email'];

        try {
            $id = D("Admin")->updateByAdminId($user['admin_id'], $data);
            if($id === false) {
                return res(0, '配置失败');
            }
            return res(1, '配置成功');
        }catch(Exception $e) {
            return res(0, $e->getMessage());
        }
    }

}