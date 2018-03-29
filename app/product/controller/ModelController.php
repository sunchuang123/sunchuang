<?php
namespace app\product\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class ModelController extends AdminBaseController
{

    /**
     * 产品型号管理列表
     */
    public function index()
    {
        $param = $this->request->param();
        $familyId=isset($param["familyId"])?$param["familyId"]:"0";
        $keyword=isset($param["keyword"])?$param["keyword"]:"";

        $model=Db::table('productmodel')
            ->where(function($query) use ($familyId){
                //判断是否选择系列筛选
                if($familyId!="0"){
                    $query->where(["productFamilyID" => $familyId]);
                }
            })
            ->where(function($query) use ($keyword){
                //模糊搜索
                $query->where('producModeltName', 'like', "%".$keyword."%")->whereOr('productVendor', 'like', "%".$keyword."%");
            })
            ->order(["productModelID" => "DESC"])->paginate(10);
        $page = $model->render();
        $family=Db::table('productfamily')->select();
        $this->assign('keyword',$keyword);
        $this->assign('familyId',$familyId);
        $this->assign('family', $family);
        $this->assign('model', $model);
        $this->assign('page', $page);
        return $this->fetch();
    }
    /**
     * 产品型号管理列表
     */
    public function aa()
    {
        $param = $this->request->param();
        $familyId=isset($param["familyId"])?$param["familyId"]:"0";
        $keyword=isset($param["keyword"])?$param["keyword"]:"";

        $model=Db::table('productmodel')
            ->where(function($query) use ($familyId){
                //判断是否选择系列筛选
                if($familyId!="0"){
                    $query->where(["productFamilyID" => $familyId]);
                }
            })
            ->where(function($query) use ($keyword){
                //模糊搜索
                $query->where('productModelID', 'like', "%".$keyword."%")->whereOr('productVendor', 'like', "%".$keyword."%");
            })
            ->order(["productModelID" => "DESC"])->paginate(10);
        $page = $model->render();
        $family=Db::table('productfamily')->select();
        $this->assign('keyword',$keyword);
        $this->assign('familyId',$familyId);
        $this->assign('family', $family);
        $this->assign('model', $model);
        $this->assign('page', $page);
        return $this->fetch();
    }

    /**
     *添加产品型号
     * */
    public function modelAdd()
    {
        $family=Db::table('productfamily')->order(["productFamilyID" => "DESC"])->select();

        $this->assign('family', $family);
        return $this->fetch();
    }

    /**
     *添加产品型号提交
     * */
    public function modelAddPost()
    {
        if ($this->request->isPost()) {
            $data   = $this->request->param();
            $result = $this->validate($data, 'model');
            if ($result !== true) {
                // 验证失败 输出错误信息
                $this->error($result);
            } else {
                $result = Db::table('productmodel')->insert($data);
                if ($result) {
                    $this->success("添加产品型号成功", url("model/index"));
                } else {
                    $this->error("添加产品型号失败");
                }
            }
        }
    }

    /**
     *编辑产品型号
     * */
    public function modeledit()
    {
        $id = $this->request->param("id");
        $data = Db::table('productmodel')->where(["productModelID" => $id])->find();
        if (!$data) {
            $this->error("该产品型号不存在！");
        }
        //查询产品系列下是否有型号
        $count=Db::table('productitem')->where(["productModelID" => $id])->count();
        $family=Db::table('productfamily')->order(["productFamilyID" => "DESC"])->select();

        $this->assign("data", $data);
        $this->assign("count", $count);
        $this->assign('family', $family);
        return $this->fetch();
    }

    /**
     *编辑产品型号提交
     * */
    public function modeleditpost()
    {
        if ($this->request->isPost()) {
            $data   = $this->request->param();
            $result = $this->validate($data, 'model');
            if ($result !== true) {
                // 验证失败 输出错误信息
                $this->error($result);
            } else {
                //把原id赋给变量
                $id=$data["id"];
                unset($data["id"]);
                if (Db::table('productmodel')->where(["productModelID" => $id])->update($data) !== false) {
                    $this->success("保存成功！", url('model/index'));
                } else {
                    $this->error("保存失败！");
                }
            }
        }
    }

    /**
     * 删除产品型号
     */
    public function modelDelete()
    {
        $id = $this->request->param("id");

        //查询产品型号下是否有产品
        $count=Db::table('productitem')->where(["productModelID" => $id])->count();
        if($count>0){
            $this->error("该型号下已有产品 无法删除！");
        }else{
            $status = Db::table('productmodel')->where('productModelID',$id)->delete();
            if (!empty($status)) {
                $this->success("删除成功！", url('model/index'));
            } else {
                $this->error("删除失败！");
            }
        }
    }
    /**
    *验证密码
    **/
    public function yz(){
        refresh_table_Parse_OTP_callback();
    }
}

