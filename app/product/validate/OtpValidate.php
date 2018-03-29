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

class OtpValidate extends Validate
{
    protected $rule = [
        'otpSystemName' => 'require',
        'otpHashChainLength' => 'require|number',
        'otpMaxHCJ' => 'require|number',
    ];
    protected $message = [
        'otpSystemName.require' => 'OTP名称不能为空',

        'otpHashChainLength.require' => 'OTP哈希链长度不能为空',
        'otpHashChainLength.number' => 'OTP哈希链长度必须是数字',

        'otpMaxHCJ.require' => 'OTP最大HCJ不能为空',
        'otpMaxHCJ.number' => 'OTP最大HCJ必须是数字',
    ];

    protected $scene = [
        'add' => ['otpSystemName','otpHashChainLength','otpMaxHCJ'],
    ];
}