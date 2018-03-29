<?php
namespace app\restapi\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class ApiController extends AdminBaseController
{
    /*
     *绑定设备
     * */
    public function assignments(){
        return $this->fetch();
    }
    /*
     *绑定设备提交
     * */
    public function assignmentsPost(){
        $data   = $this->request->param();
        $result = $this->validate($data, 'assignments');
        if ($result !== true) {
            // 验证失败 输出错误信息
            $this->error($result);
        } else {
            $res=curl_post("http://p.iotwhere.com:9090/ubilink/api/assignments",json_encode($data));
            if($res==null){
                $this->error("该设备不存在或已被绑定");
            }else{
                $this->success("绑定成功", url('api/assignmentsdetails',$res));
            }
        }
    }
    /*
     *绑定设备提交
     * */
    public function assignmentsdetails(){
        $res   = $this->request->param();
        $this->assign("res",$res);
        return $this->fetch();
    }
    /*
     *新建设备
     * */
    public function devices(){
        return $this->fetch();
    }
    /*
     *新建设备提交
     * */
    public function devicesPost(){

        $data   = $this->request->param();
        $result = $this->validate($data, 'devices');
        if ($result !== true) {
            // 验证失败 输出错误信息
            $this->error($result);
        } else {
            $res=curl_post("http://p.iotwhere.com:9090/ubilink/api/devices",'{
                      "hardwareId": '.$data["hardwareId"].',
                       "siteToken":"bb105f8d-3150-41f5-b9d1-db04965668d3",
                      "specificationToken": "6ed5e928-1217-4e99-b454-2e70a8fb5676",
                      "parentHardwareId": "",
                      "comments": ""}');
            if($res==null){
                $this->error("服务器错误或该设备已存在");
            }else{
                $this->success("绑定成功", url('api/devicesdetails',$res));
            }
        }


    }
    /*
     *绑定设备提交
     * */
    public function devicesdetails(){
        $res   = $this->request->param();
        $this->assign("res",$res);
        return $this->fetch();
    }
}

