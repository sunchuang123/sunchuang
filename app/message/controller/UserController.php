<?php
namespace app\message\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class UserController extends AdminBaseController
{

    /**
     * 用户消息列表
     */
    public function index()
    {
        $user=Db::table('messagefromuser')->union('SELECT * FROM messagetouser order by messageFromUserDateTIme desc')->select();
        $this->assign('user', $user);
        return $this->fetch();
    }

}

