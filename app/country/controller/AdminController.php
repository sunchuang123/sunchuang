<?php
namespace app\country\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class AdminController extends AdminBaseController
{

    /**
     * 国家管理列表
     */
    public function index()
    {
        $country=Db::table('country')->order(["countryID" => "DESC"])->select();
        $this->assign('country', $country);
        return $this->fetch();
    }

    /**
     *添加国家
     * */
    public function countryAdd()
    {
        return $this->fetch();
    }

    /**
     *添加国家提交
     * */
    public function countryAddPost()
    {
        if ($this->request->isPost()) {
            $data   = $this->request->param();
            $result = Db::table('country')->insert($data);
            if ($result) {
                $this->success("添加国家成功", url("admin/index"));
            } else {
                $this->error("添加国家失败");
            }

        }
    }

    /**
     *编辑国家
     * */
    public function countryedit()
    {
        $id = $this->request->param("id", 0, 'intval');
        $data = Db::table('country')->where(["countryID" => $id])->find();
        if (!$data) {
            $this->error("该国家不存在！");
        }
        $this->assign("data", $data);
        return $this->fetch();
    }

    /**
     *编辑国家提交
     * */
    public function countryeditpost()
    {
        if ($this->request->isPost()) {
        $data   = $this->request->param();
            if (Db::table('country')->update($data) !== false) {
                $this->success("保存成功！", url('admin/index'));
            } else {
                $this->error("保存失败！");
            }
        }
    }

    /**
     * 删除国家
     */
    public function countryDelete()
    {
        $id = $this->request->param("id");
        $item = Db::table('country')->where('countryID',$id)->delete($id);
        if (!empty($item)) {
            $this->success("删除成功！", url('admin/index'));
        } else {
            $this->error("删除失败！");
        }
    }
}

