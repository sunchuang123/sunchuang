<?php
namespace app\distributor\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class DistributorController extends AdminBaseController
{

    /**
     * 经销商管理列表
     */
    public function index()
    {
        $distributor=Db::table('distributor')->order(["distributorID" => "DESC"])->select();
        $this->assign('distributor', $distributor);
        return $this->fetch();
    }

    /**
     *添加经销商
     * */
    public function distributorAdd()
    {
        $country=Db::table('country')->order(["countryID" => "DESC"])->select();

        $this->assign('country', $country);
        return $this->fetch();
    }

    /**
     *添加经销商提交
     * */
    public function distributorAddPost()
    {
        if ($this->request->isPost()) {
            $data   = $this->request->param();
            $result = Db::table('distributor')->insert($data);
            if ($result) {
                $this->success("添加经销商成功", url("distributor/index"));
            } else {
                $this->error("添加经销商失败");
            }

        }
    }

    /**
     *编辑经销商
     * */
    public function distributoredit()
    {
        $id = $this->request->param("id", 0, 'intval');
        $data = Db::table('distributor')->where(["distributorID" => $id])->find();
        $country=Db::table('country')->order(["countryID" => "DESC"])->select();
        if (!$data) {
            $this->error("该经销商不存在！");
        }

        $this->assign('country', $country);
        $this->assign("data", $data);
        return $this->fetch();
    }

    /**
     *编辑经销商提交
     * */
    public function distributoreditpost()
    {
        if ($this->request->isPost()) {
            $data   = $this->request->param();
                if (Db::table('distributor')->update($data) !== false) {
                    $this->success("保存成功！", url('distributor/index'));
                } else {
                    $this->error("保存失败！");
                }
            }
    }

    /**
     * 删除经销商
     */
    public function distributorDelete()
    {
        $id = $this->request->param("id");
        $distributor = Db::table('distributor')->where('distributorID',$id)->delete($id);
        if (!empty($distributor)) {
            $this->success("删除成功！", url('distributor/index'));
        } else {
            $this->error("删除失败！");
        }
    }
}

