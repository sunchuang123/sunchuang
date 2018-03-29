<?php
namespace app\product\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class CodehistoryController extends AdminBaseController
{

    /**
     * 产品指数管理列表
     */
    public function index()
    {
        $id   = $this->request->param("id");
//        $code=Db::table('codehistory')->where(["otpGeneratorID" =>$id])->order(["codeHistoryID" => "DESC"])->paginate(10);
        $code=Db::table('codehistory')->where(["otpGeneratorID" =>$id])->order(["codeHistoryID" => "DESC"])->select();
//        $page = $code->render();
        $this->assign('code', $code);
//        $this->assign('page', $page);
        return $this->fetch();
    }

}

