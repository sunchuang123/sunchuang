<?php
namespace app\user\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class CustomerController extends AdminBaseController
{

    /**
     * 客户用户列表
     */
    public function index()
    {
        $user=Db::table('user')->order(["userD" => "DESC"])->select();
        $this->assign('user', $user);
        return $this->fetch();
    }

    /**
     *添加用户
     * */
    public function customerAdd()
    {
        $country=Db::table('country')->order(["countryID" => "DESC"])->select();
        $agent=Db::table('agent')->order(["agentID" => "DESC"])->select();

        $this->assign('country', $country);
        $this->assign('agent', $agent);
        return $this->fetch();
    }

    /**
     *添加用户提交
     * */
    public function customerAddPost()
    {
        if ($this->request->isPost()) {
            $data   = $this->request->param();
            $result = Db::table('user')->insert($data);
            if ($result) {
                $this->success("添加客户成功", url("customer/index"));
            } else {
                $this->error("添加客户失败");
            }

        }
    }

    /**
     *编辑用户
     * */
    public function customeredit()
    {
        $id = $this->request->param("id", 0, 'intval');
        $data = Db::table('user')->where(["userD" => $id])->find();
        if (!$data) {
            $this->error("该角色不存在！");
        }
        $country=Db::table('country')->order(["countryID" => "DESC"])->select();

        $this->assign('country', $country);
        $this->assign("data", $data);
        return $this->fetch();
    }

    /**
     *编辑用户提交
     * */
    public function customereditpost()
    {
        if ($this->request->isPost()) {
        $data   = $this->request->param();
            if (Db::table('user')->update($data) !== false) {
                $this->success("保存成功！", url('customer/index'));
            } else {
                $this->error("保存失败！");
            }
        }
    }

    /**
     * 删除用户
     */
    public function customerDelete()
    {
        $id = $this->request->param("id");
        $user = Db::table('user')->where('userD',$id)->delete($id);
        if (!empty($user)) {
            $this->success("删除成功！", url('customer/index'));
        } else {
            $this->error("删除失败！");
        }
    }
}

