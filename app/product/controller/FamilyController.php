<?php
namespace app\product\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class FamilyController extends AdminBaseController
{

    /**
     * 产品系列管理列表
     */
    public function index()
    {
        $param = $this->request->param();
        $keyword=isset($param["keyword"])?$param["keyword"]:"";

        $family=Db::table('productfamily')
            ->where('productFamilyName', 'like', "%".$keyword."%")
            ->order(["productFamilyID" => "DESC"])->paginate(10);
        $page = $family->render();

        $this->assign('keyword',$keyword);
        $this->assign('family', $family);
        $this->assign('page', $page);
        return $this->fetch();
    }

    /**
     *添加产品系列
     * */
    public function familyAdd()
    {
        return $this->fetch();
    }

    /**
     *添加产品系列提交
     * */
    public function familyAddPost()
    {
        if ($this->request->isPost()) {
            $data   = $this->request->param();
            $result = $this->validate($data, 'Family');
            if($result !==true){
                $this->error($result);
            }else{
                $result = Db::table('productfamily')->insert($data);
                if ($result) {
                    $this->success("添加产品系列成功", url("family/index"));
                } else {
                    $this->error("添加产品系列失败");
                }
            }
        }
    }

    /**
     *编辑产品系列
     * */
    public function familyedit()
    {
        $id = $this->request->param("id");
        $data = Db::table('productfamily')->where(["productFamilyID" => $id])->find();
        if (!$data) {
            $this->error("该产品系列不存在！");
        }
        //查询产品系列下是否有型号
        $count=Db::table('productmodel')->where(["productFamilyID" => $id])->count();

        $this->assign("data", $data);
        $this->assign("count", $count);
        return $this->fetch();
    }

    /**
     *编辑产品系列提交
     * */
    public function familyeditpost()
    {
        if ($this->request->isPost()) {
            $data   = $this->request->param();
            $result = $this->validate($data, 'Family');
            if($result !==true){
                $this->error($result);
            }else{
                //把原id赋给变量
                $id = $data["id"];
                unset($data["id"]);
                if (Db::table('productfamily')->where(["productFamilyID" => $id])->update($data) !== false) {
                    $this->success("保存成功！", url('family/index'));
                } else {
                    $this->error("保存失败！");
                }
            }
        }
    }

    /**
     * 删除产品系列
     */
    public function familyDelete()
    {
        $id = $this->request->param("id");

        //查询产品系列下是否有型号
        $count=Db::table('productmodel')->where(["productFamilyID" => $id])->count();
        if($count>0){
            $this->error("该产品系列下已有型号 无法删除！");
        }else {
            $status = Db::table('productfamily')->delete($id);
            if (!empty($status)) {
                $this->success("删除成功！", url('family/index'));
            } else {
                $this->error("删除失败！");
            }
        }
    }
}

