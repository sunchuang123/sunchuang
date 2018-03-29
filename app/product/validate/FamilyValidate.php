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

class FamilyValidate extends Validate
{
    protected $rule = [
        'productFamilyName' => 'require',
    ];
    protected $message = [
        'productFamilyName.require' => '系列名称不能为空',
    ];

    protected $scene = [
        'add' => ['productFamilyName'],
    ];
}