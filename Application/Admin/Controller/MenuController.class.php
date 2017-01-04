<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Exception;

/*菜单管理*/
class MenuController extends CommonController {
    
    public function add(){
        if($_POST) {
            if(!isset($_POST['name']) || !$_POST['name']) {
                return res(0,'菜单名不能为空');
            }
            if(!isset($_POST['m']) || !$_POST['m']) {
                return res(0,'模块名不能为空');
            }
            if(!isset($_POST['c']) || !$_POST['c']) {
                return res(0,'控制器不能为空');
            }
            if(!isset($_POST['f']) || !$_POST['f']) {
                return res(0,'方法名不能为空');
            }
            if($_POST['menu_id']) {
                return $this->save($_POST);
            }
            $menuId = D("Menu")->insert($_POST);
            if($menuId) {
                return res(1,'新增成功',$menuId);
            }
            return res(0,'新增失败',$menuId);

        }else {
            $this->display();
        }
        //echo "welcome to singcms";
    }

    public function index() {
        $data = array();
        if(isset($_REQUEST['type']) && in_array($_REQUEST['type'], array(0,1))) {
            $data['type'] = intval($_REQUEST['type']);
            $this->assign('type',$data['type']);
        }else{
            $this->assign('type',-100);
        }
        /**
         * 分页操作逻辑
         */
        $page = $_REQUEST['p'] ? $_REQUEST['p'] : 1;
        $pageSize = $_REQUEST['pageSize'] ? $_REQUEST['pageSize'] : 3;
        $menus = D("Menu")->getMenus($data,$page,$pageSize);
        $menusCount = D("Menu")->getMenusCount($data);

        $res = new \Think\Page($menusCount, $pageSize);
        $pageRes = $res->res();
        $this->assign('pageRes', $pageRes);
        $this->assign('menus',$menus);
    	$this->display();
    }

    public function edit() {
        $menuId = $_GET['id'];

        $menu = D("Menu")->find($menuId);
        $this->assign('menu', $menu);
        $this->display();
    }
    public function save($data) {
        $menuId = $data['menu_id'];
        unset($data['menu_id']);

        try {
            $id = D("Menu")->updateMenuById($menuId, $data);
            if($id === false) {
                return res(0,'更新失败');
            }
            return res(1,'更新成功');
        }catch(Exception $e) {
            return res(0,$e->getMessage());
        }

    }

    public function setStatus() {
        try {
            if ($_POST) {
                $id = $_POST['id'];
                $status = $_POST['status'];
                // 执行数据更新操作
                $res = D("Menu")->updateStatusById($id, $status);
                if ($res) {
                    return res(1, '操作成功');
                } else {
                    return res(0, '操作失败');
                }

            }
        }catch(Exception $e) {
            return res(0,$e->getMessage());
        }

        return res(0,'没有提交的数据');
    }
    public function listorder() {
        $listorder = $_POST['listorder'];
        $jumpUrl = $_SERVER['HTTP_REFERER'];
        $errors = array();
        if($listorder) {
            try {
                foreach ($listorder as $menuId => $v) {
                    // 执行更新
                    $id = D("Menu")->updateMenuListorderById($menuId, $v);
                    if ($id === false) {
                        $errors[] = $menuId;
                    }

                }
            }catch(Exception $e) {
                return res(0,$e->getMessage(),array('jump_url'=>$jumpUrl));
            }
            if($errors) {
                return res(0,'排序失败-'.implode(',',$errors),array('jump_url'=>$jumpUrl));
            }
            return res(1,'排序成功',array('jump_url'=>$jumpUrl));
        }

        return res(0,'排序数据失败',array('jump_url'=>$jumpUrl));
    }




}