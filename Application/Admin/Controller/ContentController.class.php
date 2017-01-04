<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Exception;

/**
 * 文章内容管理
 */
class ContentController extends CommonController {
    //文章列表
    public function index() {
        $conds = array();
        $title = $_GET['title'];
        if($title) {
            $conds['title'] = $title;
        }
        if($_GET['catid']) {
            $conds['catid'] = intval($_GET['catid']);
        }

        $page = $_REQUEST['p'] ? $_REQUEST['p'] : 1;
        $pageSize = 10;

        $news = D("News")->getNews($conds,$page,$pageSize);
        $count = D("News")->getNewsCount($conds);

        $res  =  new \Think\Page($count,$pageSize);
        $pageres = $res->res();
        $positions = D("Position")->getNormalPositions();
        $this->assign('pageres',$pageres);
        $this->assign('news',$news);
        $this->assign('positions', $positions);

        $this->assign('webSiteMenu',D("Menu")->getBarMenus());
        $this->display();
    }
    //添加文章
    public function add(){
        if($_POST) {
            if(!isset($_POST['title']) || !$_POST['title']) {
                return res(0,'标题不存在');
            }
            if(!isset($_POST['small_title']) || !$_POST['small_title']) {
                return res(0,'短标题不存在');
            }
            if(!isset($_POST['catid']) || !$_POST['catid']) {
                return res(0,'文章栏目不存在');
            }
            if(!isset($_POST['keywords']) || !$_POST['keywords']) {
                return res(0,'关键字不存在');
            }
            if(!isset($_POST['content']) || !$_POST['content']) {
                return res(0,'content不存在');
            }
            if($_POST['news_id']) {
                return $this->save($_POST);
            }
            $newsId = D("News")->insert($_POST);
            if($newsId) {
                $newsContentData['content'] = $_POST['content'];
                $newsContentData['news_id'] = $newsId;
                $cId = D("NewsContent")->insert($newsContentData);
                if($cId){
                    return res(1,'新增成功');
                }else{
                    return res(1,'主表插入成功，副表插入失败');
                }


            }else{
                return res(0,'新增失败');
            }

        }else {

            $webSiteMenu = D("Menu")->getBarMenus();

            $titleFontColor = C("TITLE_FONT_COLOR");
            $copyFrom = C("COPY_FROM");
            $this->assign('webSiteMenu', $webSiteMenu);
            $this->assign('titleFontColor', $titleFontColor);
            $this->assign('copyfrom', $copyFrom);
            $this->display();
        }
    }
    //修改文章页面
    public function edit() {
        $newsId = $_GET['id'];
        if(!$newsId) {
            // 执行跳转
            $this->redirect('/index.php?m=admin&c=content');
        }
        $news = D("News")->find($newsId);
        if(!$news) {
            $this->redirect('/index.php?m=admin&c=content');
        }
        $newsContent = D("NewsContent")->find($newsId);
        if($newsContent) {
            $news['content'] = $newsContent['content'];
        }

        $webSiteMenu = D("Menu")->getBarMenus();
        $this->assign('webSiteMenu', $webSiteMenu);
        $this->assign('titleFontColor', C("TITLE_FONT_COLOR"));
        $this->assign('copyfrom', C("COPY_FROM"));

        $this->assign('news',$news);
        $this->display();
    }
    //保存修改文章
    public function save($data) {
        $newsId = $data['news_id'];
        unset($data['news_id']);

        try {
            $id = D("News")->updateById($newsId, $data);
            $newsContentData['content'] = $data['content'];
            $condId = D("NewsContent")->updateNewsById($newsId, $newsContentData);
            if($id === false || $condId === false) {
                return res(0, '更新失败');
            }
            return res(1, '更新成功');
        }catch(Exception $e) {
            return res(0, $e->getMessage());
        }

    }
    //设置文章状态
    public function setStatus() {
        try {
            if ($_POST) {
                $id = $_POST['id'];
                $status = $_POST['status'];
                if (!$id) {
                    return res(0, 'ID不存在');
                }
                $res = D("News")->updateStatusById($id, $status);
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

    //文章排序
    public function listorder() {
        $listorder = $_POST['listorder'];
        $jumpUrl = $_SERVER['HTTP_REFERER'];
        $errors = array();
        try {
            if ($listorder) {
                foreach ($listorder as $newsId => $v) {
                    // 执行更新
                    $id = D("News")->updateNewsListorderById($newsId, $v);
                    if ($id === false) {
                        $errors[] = $newsId;
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

    //文章推送
    public function push() {
        $jumpUrl = $_SERVER['HTTP_REFERER'];
        $positonId = intval($_POST['position_id']);
        $newsId = $_POST['push'];

        if(!$newsId || !is_array($newsId)) {
            return res(0, '请选择推荐的文章ID进行推荐');

        }
        if(!$positonId) {
            return res(0, '没有选择推荐位');
        }
        try {
            $news = D("News")->getNewsByNewsIdIn($newsId);
            if (!$news) {
                return res(0, '没有相关内容');
            }

            foreach ($news as $new) {
                $data = array(
                    'position_id' => $positonId,
                    'title' => $new['title'],
                    'thumb' => $new['thumb'],
                    'news_id' => $new['news_id'],
                    'status' => 1,
                    'create_time' => $new['create_time'],
                );
                $position = D("PositionContent")->insert($data);
            }
        }catch(Exception $e) {
            return res(0, $e->getMessage());
        }

        return res(1, '推荐成功',array('jump_url'=>$jumpUrl));


    }
}