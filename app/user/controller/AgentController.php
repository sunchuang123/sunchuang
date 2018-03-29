<?php
namespace app\user\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class AgentController extends AdminBaseController
{

    /**
     * 代理人管理列表
     */
    public function index()
    {
        $agent=Db::table('agent')->order(["agentID" => "DESC"])->select();
        $this->assign('agent', $agent);
        return $this->fetch();
    }

    /**
     *添加代理人
     * */
    public function agentAdd()
    {
        return $this->fetch();
    }

    /**
     *添加代理人提交
     * */
    public function agentAddPost()
    {
        if ($this->request->isPost()) {
            $data   = $this->request->param();
                $result = Db::table('agent')->insert($data);
                if ($result) {
                    $this->success("添加代理人成功", url("agent/index"));
                } else {
                    $this->error("添加代理人失败");
                }

        }
    }

    /**
     *编辑代理人
     * */
    public function agentedit()
    {
        $id = $this->request->param("id", 0, 'intval');
        $data = Db::table('agent')->where(["agentID" => $id])->find();
        if (!$data) {
            $this->error("该代理人不存在！");
        }
        $this->assign("data", $data);
        return $this->fetch();
    }

    /**
     *编辑代理人提交
     * */
    public function agenteditpost()
    {

        if ($this->request->isPost()) {
            $data   = $this->request->param();
                if (Db::table('agent')->update($data) !== false) {
                    $this->success("保存成功！", url('agent/index'));
                } else {
                    $this->error("保存失败！");
                }
        }
    }

    /**
     * 删除代理人
     */
    public function agentDelete()
    {
        $id = $this->request->param("id");
        $agent = Db::table('agent')->where('agentID',$id)->delete($id);
        if (!empty($agent)) {
            $this->success("删除成功！", url('agent/index'));
        } else {
            $this->error("删除失败！");
        }
    }
}

