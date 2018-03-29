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

class ItemValidate extends Validate
{
    protected $rule = [
        'productModelID' => 'require',
        'productBatchNumber' => 'require',
        'otpsystem' => 'require',
    ];
    protected $message = [
        'productModelID.require' => '请选择型号',
        'productBatchNumber.require' => '批号不能为空',
        'otpsystem.require' => '请选OTP系统',
    ];

    protected $scene = [
        'add' => ['productModelID','productBatchNumber','otpsystem'],
    ];
}