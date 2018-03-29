<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\restapi\validate;

use think\Validate;

class DevicesValidate extends Validate
{
    protected $rule = [
        'hardwareId' => 'require',
    ];
    protected $message = [
        'hardwareId.require' => '设备ID不能为空',
    ];

    protected $scene = [
        'add' => ['hardwareId'],
    ];
}