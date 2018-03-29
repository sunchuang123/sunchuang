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

class ModelValidate extends Validate
{
    protected $rule = [
        'producModeltName' => 'require',
        'productFamilyID' => 'require',
        'quantityInStock' => 'number',
        'productModelCost' => 'number',
    ];
    protected $message = [
        'producModeltName.require' => '型号名称不能为空',
        'productFamilyID.require' => '请选择产品系列',
        'quantityInStock.number' => '库存数量必须是数字',
        'productModelCost.number' => '产品成本模型必须是数字',
    ];

    protected $scene = [
        'add' => ['producModeltName','productFamilyID','quantityInStock','productModelCost'],
    ];
}