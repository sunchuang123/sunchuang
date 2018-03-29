<?php
namespace app\distributor\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class OrderController extends AdminBaseController
{

    /**
     * 经销商订单列表
     */
    public function index()
    {
        $order=Db::table('distributor_order')->order(["orderID" => "DESC"])->select();
        $this->assign('order', $order);
        return $this->fetch();
    }

    /**
     * 经销商订单详情
     */
    public function orderdetails()
    {
        $id = $this->request->param("id");
        $order=Db::table('distributor_order')->where("orderID",$id)->find();
        $this->assign('order', $order);
        return $this->fetch();
    }

}

