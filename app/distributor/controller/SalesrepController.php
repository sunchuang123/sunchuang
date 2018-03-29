<?php
namespace app\distributor\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class SalesrepController extends AdminBaseController
{

    /**
     * 推销员管理列表
     */
    public function index()
    {
        $salesrep=Db::table('salesrep')->order(["salesrepID" => "DESC"])->select();
        $this->assign('salesrep', $salesrep);
        return $this->fetch();
    }

    /**
     *添加推销员
     * */
    public function salesrepAdd()
    {
        return $this->fetch();
    }

    /**
     *添加推销员提交
     * */
    public function salesrepAddPost()
    {
        if ($this->request->isPost()) {
            $data   = $this->request->param();
                $result = Db::table('salesrep')->insert($data);
                if ($result) {
                    $this->success("添加推销员成功", url("salesrep/index"));
                } else {
                    $this->error("添加推销员失败");
                }

        }
    }

    /**
     *编辑推销员
     * */
    public function salesrepedit()
    {
        $id = $this->request->param("id", 0, 'intval');
        $data = Db::table('salesrep')->where(["salesrepID" => $id])->find();
        if (!$data) {
            $this->error("该推销员不存在！");
        }
        $this->assign("data", $data);
        return $this->fetch();
    }

    /**
     *编辑推销员提交
     * */
    public function salesrepeditpost()
    {
        if ($this->request->isPost()) {
            $data   = $this->request->param();
            if (Db::table('salesrep')->update($data) !== false) {
                $this->success("保存成功！", url('salesrep/index'));
            } else {
                $this->error("保存失败！");
            }
        }
    }

    /**
     * 删除推销员
     */
    public function salesrepDelete()
    {
        $id = $this->request->param("id");
        $salesrep = Db::table('salesrep')->where('salesrepID',$id)->delete($id);
        if (!empty($salesrep)) {
            $this->success("删除成功！", url('salesrep/index'));
        } else {
            $this->error("删除失败！");
        }
    }
}

