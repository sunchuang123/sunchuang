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
namespace app\product\validate;

use think\Validate;

class ItembatchValidate extends Validate
{
    protected $rule = [
        'productModelID' => 'require',
        'productBatchNumber' => 'require',
        'startID' => 'require|number|notIn:0',
        'startIdNumber' => 'require|number|gt:4',
        'Number' => 'require|number|notIn:0',
        'otpsystem' => 'require',
    ];
    protected $message = [
        'productModelID.require' => '请选择型号',
        'productBatchNumber.require' => '批号不能为空',

        'startID.require' => '起始编号不能为空',
        'startID.number' => '起始编号必须是数字',
        'startID.notIn' => '起始编号不能为0',

        'startIdNumber.require' => '编号长度不能为空',
        'startIdNumber.gt' => '编号长度大于或等于5',
        'startIdNumber.number' => '编号长度必须是数字',

        'Number.require' => '数量不能为空',
        'Number.notIn' => '数量不能为0',
        'Number.number' => '数量必须是数字',

        'otpsystem.require' => '请选择OTP系统',
    ];

    protected $scene = [
        'add' => ['productModelID','productBatchNumber','startID','startIdNumber','Number','otpsystem'],
    ];
}