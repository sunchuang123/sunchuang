<?php
namespace app\message\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class AgentController extends AdminBaseController
{

    /**
     * 代理人消息列表
     */
    public function index()
    {
        $agent=Db::table('messagefromagent')->union('SELECT * FROM messagetoagent order by messageFromAgentDateTIme desc')->select();
        $this->assign('agent', $agent);
        return $this->fetch();
    }

}

