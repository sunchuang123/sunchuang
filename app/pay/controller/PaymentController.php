<?php
namespace app\pay\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class PaymentController extends AdminBaseController
{

    /**
     * 付款管理列表
     */
    public function index()
    {
        $payment=Db::table('payment')->order(["paymentID" => "DESC"])->select();
        $this->assign('payment', $payment);
        return $this->fetch();
    }

}

