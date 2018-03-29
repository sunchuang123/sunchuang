<?php
namespace app\message\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class DeviceController extends AdminBaseController
{

    /**
     * 设备消息列表
     */
    public function index()
    {
        $device=Db::table('messagefromdevice')->union('SELECT * FROM messagetodevice order by messageFromDeviceDateTIme desc')->select();
        $this->assign('device', $device);
        return $this->fetch();
    }

}

