<?php

namespace Admin\Controller;
use Think\Controller;
use Think\Upload;


class ImageController extends CommonController {
    private $_uploadObj;
    public function __construct() {

    }
    //上传文章内容的图片
    public function ajaxuploadimage() {
        $upload = D("UploadImage");
        $res = $upload->imageUpload();
        if($res===false) {
            return res(0,'上传失败','');
        }else{
            return res(1,'上传成功',$res);
        }

    }

    public function kindupload(){
        $upload = D("UploadImage");
        $res = $upload->upload();
        if($res === false) {
            return showKind(1,'上传失败');
        }
        return showKind(0,$res);
    }

}