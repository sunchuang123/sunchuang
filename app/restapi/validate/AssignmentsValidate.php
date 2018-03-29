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

class AssignmentsValidate extends Validate
{
    protected $rule = [
        'deviceHardwareId' => 'require',
        'assignmentType' => 'require',
    ];
    protected $message = [
        'deviceHardwareId.require' => '设备ID不能为空',
        'assignmentType.require' => '分配类型不能为空',
    ];

    protected $scene = [
        'add' => ['deviceHardwareId','assignmentType'],
    ];
}