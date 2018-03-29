<?php
namespace app\pay\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class PlanController extends AdminBaseController
{

    /**
     * 支付计划管理列表
     */
    public function index()
    {
        $plan=Db::table('payplan')->order(["payPlanID" => "DESC"])->select();
        $this->assign('plan', $plan);
        return $this->fetch();
    }
    
}

