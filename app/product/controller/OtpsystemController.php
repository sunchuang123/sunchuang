<?php

namespace app\product\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class OtpsystemController extends AdminBaseController
{

    /**
     * OTP系统管理列表
     */
    public function index()
    {
        $otpSystem = Db::table('otpSystem')->order(["otpSystemID" => "DESC"])->paginate(10);
        $page = $otpSystem->render();
        $this->assign('otpSystem', $otpSystem);
        $this->assign('page', $page);
        return $this->fetch();
    }

    /**
     *添加OTP系统
     * */
    public function otpAdd()
    {
        return $this->fetch();
    }

    /**
     *添加OTP系统提交
     * */
    public function otpAddPost()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $result = $this->validate($data, 'otp');
            if ($result !== true) {
                // 验证失败 输出错误信息
                $this->error($result);
            } else {
                $result = Db::table('otpSystem')->insert($data);
                if ($result) {
                    $this->success("添加OTP系统成功", url("otpsystem/index"));
                } else {
                    $this->error("添加OTP系统失败");
                }
            }
        }
    }

    /**
     *编辑OTP系统
     * */
    public function otpedit()
    {
        $id = $this->request->param("id");
        $data = Db::table('otpSystem')->where(["otpSystemID" => $id])->find();
        if (!$data) {
            $this->error("该OTP系统不存在！");
        }

        $this->assign("data", $data);
        return $this->fetch();
    }

    /**
     *编辑OTP系统提交
     * */
    public function otpeditpost()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $result = $this->validate($data, 'otp');
            if ($result !== true) {
                // 验证失败 输出错误信息
                $this->error($result);

            } else {
                if (Db::table('otpSystem')->update($data) !== false) {
                    $this->success("保存成功！", url('otpsystem/index'));
                } else {
                    $this->error("保存失败！");
                }
            }
        }
    }


        /**
         * 删除OTP系统
         */
        public function otpDelete()
        {
            $id = $this->request->param("id", 0, 'intval');
            $status = Db::table('otpSystem')->delete($id);
            if (!empty($status)) {
                $this->success("删除成功！", url('otpsystem/index'));
            } else {
                $this->error("删除失败！");
            }
        }

    }

