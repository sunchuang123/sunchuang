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
namespace app\country\validate;

use think\Validate;

class AdminValidate extends Validate
{
    protected $rule = [
        'countryID' => 'require',
    ];
    protected $message = [
        'countryID.require' => 'ID不能为空',
    ];

    protected $scene = [
        'add' => ['countryID'],
    ];
}