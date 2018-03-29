<?php
namespace app\distributor\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class SalesofficeController extends AdminBaseController
{

    /**
     * 销售办公室管理列表
     */
    public function index()
    {
        $salesoffice=Db::table('sales_office')->order(["sales_OfficeID" => "DESC"])->select();
        $this->assign('salesoffice', $salesoffice);
        return $this->fetch();
    }

    /**
     *添加销售办公室
     * */
    public function salesofficeAdd()
    {
        return $this->fetch();
    }

    /**
     *添加销售办公室提交
     * */
    public function salesofficeAddPost()
    {
        if ($this->request->isPost()) {
            $data   = $this->request->param();
            $result = $this->validate($data, 'salesoffice');
            if ($result !== true) {
                // 验证失败 输出错误信息
                $this->error($result);
            } else {
                $result = Db::table('sales_office')->insert($data);
                if ($result) {
                    $this->success("添加销售办公室成功", url("salesoffice/index"));
                } else {
                    $this->error("添加销售办公室失败");
                }

            }
        }
    }

    /**
     *编辑销售办公室
     * */
    public function salesofficeedit()
    {
        $id = $this->request->param("id", 0, 'intval');
        $data = Db::table('sales_office')->where(["sales_officeID" => $id])->find();
        if (!$data) {
            $this->error("该销售办公室不存在！");
        }
        $this->assign("data", $data);
        return $this->fetch();
    }

    /**
     *编辑销售办公室提交
     * */
    public function salesofficeeditpost()
    {

        $id = $this->request->param("id", 0, 'intval');
        if ($this->request->isPost()) {
            $data   = $this->request->param();
            $result = $this->validate($data, 'salesoffice');
            if ($result !== true) {
                // 验证失败 输出错误信息
                $this->error($result);

            } else {
                if (Db::table('sales_office')->update($data) !== false) {
                    $this->success("保存成功！", url('salesoffice/index'));
                } else {
                    $this->error("保存失败！");
                }
            }
        }
    }

    /**
     * 删除销售办公室
     */
    public function salesofficeDelete()
    {
        $id = $this->request->param("id");
        $salesoffice = Db::table('sales_office')->where('sales_officeID',$id)->delete($id);
        if (!empty($salesoffice)) {
            $this->success("删除成功！", url('salesoffice/index'));
        } else {
            $this->error("删除失败！");
        }
    }
}

