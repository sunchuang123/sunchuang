<?php
namespace app\pay\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class OrderController extends AdminBaseController
{

    /**
     * 订购产品管理列表
     */
    public function index()
    {
        $order=Db::table('order_product')->order(["orderProductcID" => "DESC"])->select();
        $this->assign('order', $order);
        return $this->fetch();
    }

}

