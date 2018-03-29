<?php
namespace app\product\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class StatusController extends AdminBaseController
{

    /**
     * 产品系列管理列表
     */
    public function index()
    {
        $param = $this->request->param();
        $keyword=isset($param["keyword"])?$param["keyword"]:"";

        $status=Db::table('productStatus')
            ->alias('status')
            ->join(['productItem'=>'item', 'cmf_'],'item.productitemID = status.productItemID','left')
            ->where(function($query) use ($keyword){
                    $query->where("item.productItemOEM_SN","like","%".$keyword."%");
            })
            ->order(["status.productStatusID" => "DESC"])->paginate(10);
        $page = $status->render();

        $this->assign('keyword',$keyword);
        $this->assign('status', $status);
        $this->assign('page', $page);
        return $this->fetch();
    }
}

