<?php
namespace app\product\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class ItemController extends AdminBaseController
{

    /**
     * 产品项目管理列表
     */
    public function index()
    {
        $param = $this->request->param();
        $modelId=isset($param["modelId"])?$param["modelId"]:"0";
        $keyword=isset($param["keyword"])?$param["keyword"]:"";

        //左查询
        $item=Db::table('productitem')
            ->alias('item')
            //左联合
            ->join(['otpgenerator'=>'otp', 'cmf_'],'item.productitemID = otp.otpGeneratorID','left')
            ->join(['productbatch'=>'batch', 'cmf_'],'item.productBatchID = batch.productBatchID','left')
            ->join(['otpsystem'=>'otpsystem', 'cmf_'],'otp.otpsystemID = otpsystem.otpsystemID','left')
            //条件
            ->where(function($query) use ($modelId){
                //判断是否选择型号筛选
                if($modelId!="0"){
                    $query->where(["item.productModelID" => $modelId]);
                }
            })
            ->where(function($query) use ($keyword){
                //模糊搜索
                $query->where('batch.productBatchNumber', 'like', "%".$keyword."%")->whereOr('item.productItemOEM_SN', 'like', "%".$keyword."%");
            })
            ->order(["item.productItemID" => "DESC"])->paginate(10);

        $page = $item->render();
        $model=Db::table('productmodel')->select();

        $this->assign('keyword',$keyword);
        $this->assign('modelId',$modelId);
        $this->assign('model',$model);
        $this->assign('item', $item);
        $this->assign('page', $page);
        return $this->fetch();
    }

    /**
     *添加产品项目
     * */
    public function itemAdd()
    {
        $model=Db::table('productmodel')->order(["productModelID" => "DESC"])->select();
        $otpSystem=Db::table('otpSystem')->order(["otpSystemID" => "DESC"])->select();

        $this->assign('model', $model);
        $this->assign('otpSystem', $otpSystem);
        return $this->fetch();
    }

    /**
     *添加产品项目提交
     * */
    public function itemAddPost()
    {
        if ($this->request->isPost()) {
            $data   = $this->request->param();
            $result = $this->validate($data, 'Item');
            if($result !==true){
                $this->error($result);
            }else{
                //生成产品编号
//                $model = Db::table('productmodel')->where(["productModelID" => $data["productModelID"]])->find();
//                $family = Db::table('productFamily')->where(["productFamilyID" => $model["productFamilyID"]])->find();
//                $data["productItemOEM_SN"]=$model["producModeltName"].date("ym",time()).$family["productFamilyName"];

                $otpSystemID=$data["otpsystem"];
                unset($data["otpsystem"]);

                //重复性查询
                $find=Db::table('productitem')->where(["productItemOEM_SN" => $data["productItemOEM_SN"]])->find();
                //判断id是否存在
                if(!empty($find)){
                    $this->error("该产品项目已存在");
                }else {
                    $item["productModelID"]=$data["productModelID"];
                    $item["productItemOEM_SN"]=$data["productItemOEM_SN"];
                    $item["productItemPAYG_SN"]=$data["productItemOEM_SN"];
                    $item["lifeCycleStatus"]=$data["lifeCycleStatus"];

                    $batch["productBatchNumber"]=$data["productBatchNumber"];
                    $batch["number"]=1;
                    $batch["productBatchDate"]=date("Y-m-d H:i:s",time());

                    Db::table('productBatch')->insert($batch);
                    $Batchid = Db::table('productBatch')->getLastInsID();

                    $item["productBatchID"]=$Batchid;
                    Db::table('productitem')->insert($item);
                    $itemid = Db::table('productitem')->getLastInsID();

                    $status["productItemID"]=$itemid;
                    $status["updateTimestamp"]=date("Y-m-d H:i:s");
                    $result = Db::table('productStatus')->insert($status);

                    if ($result) {
                        //生成密码发生器
                        $this->Initialization_Ietm_OTP($itemid,$otpSystemID);
                        $this->success("添加产品项目成功", url("item/index"));
                    } else {
                        $this->error("添加产品项目失败");
                    }
                }
            }
        }
    }

    /**
     *编辑产品项目
     * */
    public function itemedit()
    {
        $id = $this->request->param("id");
        $data = Db::table('productitem')->where(["productItemID" => $id])->find();
        if (!$data) {
            $this->error("该产品项目不存在！");
        }
        $model=Db::table('productmodel')->order(["productModelID" => "DESC"])->select();

        $this->assign("data", $data);
        $this->assign('model', $model);
        return $this->fetch();
    }

    /**
     *编辑产品项目提交
     * */
    public function itemeditpost()
    {
        if ($this->request->isPost()) {
            $data   = $this->request->param();
            $payg=$data["payg"];
            unset($data["payg"]);
            if($payg==0){
                $data["productItemPAYG_SN"]=Db::table('productitem')->where('productItemID',$data["productItemID"])->value("productItemOEM_SN");
            }
            if (Db::table('productitem')->where('productItemID',$data["productItemID"])->update($data) !== false) {
                $this->success("保存成功！", url('item/index'));
            } else {
                $this->error("保存失败！");
            }
        }
    }

    /**
     * 删除产品项目
     */
    public function itemDelete()
    {
        $id = $this->request->param("id");
        $item=Db::table('productItem')->where('productItemID',$id)->find();
        $status = Db::table('productItem')->where('productItemID',$id)->delete($id);
        if (!empty($status)) {
//            Db::table('productbatch')->delete($item["productBatchID"]);
            Db::table('productStatus')->where('productItemID',$item["productItemID"])->delete();
            $this->success("删除成功！", url('item/index'));
        } else {
            $this->error("删除失败！");
        }
    }

    /**
     * 批量添加
     */
    public function itembatch()
    {
        $model=Db::table('productmodel')->order(["productModelID" => "DESC"])->select();
        $otpSystem=Db::table('otpSystem')->order(["otpSystemID" => "DESC"])->select();

        $this->assign('model', $model);
        $this->assign('otpSystem', $otpSystem);
        return $this->fetch();
    }

    /**
     * 批量提交
     */
    public function itemBatchpost()
    {
        if ($this->request->isPost()) {
            $data   = $this->request->param();
            $result = $this->validate($data, 'Itembatch');
            if($result !==true){
                $this->error($result);
            }else{
                //删除不被添加的数据
                $date=$data["date"];
                $number=$data["Number"];
                $startID=$data["startID"];
                $Suffix=$data["Suffix"];
                $prefix=$data["prefix"];
                $startIdNumber=$data["startIdNumber"]-4;
                $batch["productBatchNumber"]=$data["productBatchNumber"];
                $batch["number"]=$number;
                $batch["productBatchDate"]=date("Y-m-d H:i:s",time());
                $batch["prefix"]=$prefix;
                $batch["suffix"]=$Suffix;
                $otpSystemID=$data["otpsystem"];
                unset($data["otpsystem"]);
                unset($data["date"]);
                unset($data["Number"]);
                unset($data["startID"]);
                unset($data["Suffix"]);
                unset($data["prefix"]);
                unset($data["productBatchNumber"]);
                unset($data["startIdNumber"]);

                $data["lifeCycleStatus"]="New";

                //添加批次
                Db::table('productbatch')->insert($batch);
                $batchid = Db::table('productbatch')->getLastInsID();
                $data["productBatchID"]=$batchid;

                //重复性查询
                for($i=0;$i<$number;$i++){
                    //计算OEM_SN
                    $addstartID=$startID+$i;
                    $length=$startIdNumber-strlen($addstartID);
                    $OEM_SN="";
                    if($Suffix!=""){
                        $OEM_SN=$prefix;
                    }
                    $OEM_SN.=str_replace('-','',$date);
                    for($l=0;$l<$length;$l++){
                        $OEM_SN.="0";
                    }
                    $OEM_SN.=$addstartID;
                    if($prefix!=""){
                        $OEM_SN.=$Suffix;
                    }
                    $data["productItemOEM_SN"]=$OEM_SN;

                    //重复性查询
                    $find=Db::table('productitem')->where(["productItemOEM_SN" => $data["productItemOEM_SN"]])->find();
                    //判断产品编号是否存在
                    if(!empty($find)){
                        $this->error("产品项目".$data["productItemOEM_SN"]."已存在");
                        exit;
                    }
                }
                //开始循环添加
                for($i=0;$i<$number;$i++){
                    //计算OEM_SN
                    $addstartID=$startID+$i;
                    $length=$startIdNumber-strlen($addstartID);
                    $OEM_SN="";
                    if($prefix!=""){
                        $OEM_SN=$prefix;
                    }
                    $OEM_SN.=str_replace('-','',$date);
                    for($l=0;$l<$length;$l++){
                        $OEM_SN.="0";
                    }
                    $OEM_SN.=$addstartID;
                    if($Suffix!=""){
                        $OEM_SN.=$Suffix;
                    }
                    $data["productItemOEM_SN"]=$OEM_SN;
                    $data["productItemPAYG_SN"]=$OEM_SN;


                    //重复性查询
//                    $find=Db::table('productitem')->where(["productItemOEM_SN" => $data["productItemOEM_SN"]])->find();
//                    //判断产品编号是否存在
//                    if(!empty($find)){
//                        $this->error("产品项目".$data["productItemOEM_SN"]."已存在");
//                    }else {

                        $result = Db::table('productitem')->insert($data);
                        $itemid = Db::table('productitem')->getLastInsID();

                        $status["productItemID"]=$itemid;
                        $status["updateTimestamp"]=date("Y-m-d H:i:s",time());
                        Db::table('productStatus')->insert($status);

                        $this->Initialization_Ietm_OTP($itemid,$otpSystemID);
                        if($i==($number-1)){
                            if ($result) {
                                $this->success("批量添加产品项目成功", url("item/index"));
                            } else {
                                $this->error("批量添加产品项目失败");
                            }
                        }
//                    }
                }
            }
        }
    }

    /**
    *导出Excel
    */
    public function exportExcle(){
        header('Content-Type: application/vnd.ms-excel');//告诉浏览器将要输出excel03文件
        header('Content-Disposition: attachment;filename="产品项目-'.date("Y-m-d",time()).'.xlsx');//告诉浏览器将输出文件的名称(文件下载)
        header('Cache-Control: max-age=0');//禁止缓存
        $objexcle = new \PHPExcel();

        $param = $this->request->param();
        $modelId=isset($param["modelId"])?$param["modelId"]:0;
        $keyword=isset($param["keyword"])?$param["keyword"]:"";

        //开始查询数据
        $item=Db::table('productitem')
            ->alias('item')
            //左联合
            ->join(['otpgenerator'=>'otp', 'cmf_'],'item.productitemID = otp.otpGeneratorID','left')
            ->join(['productbatch'=>'batch', 'cmf_'],'item.productBatchID = batch.productBatchID','left')
            ->join(['otpsystem'=>'otpsystem', 'cmf_'],'otp.otpsystemID = otpsystem.otpsystemID','left')
            //条件
            ->where(function($query) use ($modelId){
                //判断是否选择型号筛选
                if($modelId!="0"){
                    $query->where(["item.productModelID" => $modelId]);
                }
            })
            ->where(function($query) use ($keyword){
                //模糊搜索
                $query->where('batch.productBatchNumber', 'like', "%".$keyword."%")->whereOr('item.productItemOEM_SN', 'like', "%".$keyword."%");
            })
            ->order(["item.productItemID" => "DESC"])->select();
        //设置字段
        $objSheet = $objexcle->getActiveSheet();
        $objSheet ->setTitle();
        $objSheet->setCellValue("A1","原始设备制造商编号");
        $objSheet->setCellValue("B1","批号");
        $objSheet->setCellValue("C1","PG状态");
        $objSheet->setCellValue("D1","PAYG系列号");
        $objSheet->setCellValue("E1","生命周期状态");
        $objSheet->setCellValue("F1","PAYG安全哈希顶部");
        $objSheet->setCellValue("G1","PAYG安全哈希底部");
        $objSheet->setCellValue("H1","OTP计数");
        $objSheet->setCellValue("I1","目前哈希索引");
        $objSheet->setCellValue("J1","散列链得长度");
        $objSheet->setCellValue("K1","最大HCJ");

        //设置列宽
        $objSheet->getColumnDimension('A')->setWidth(30);
        $objSheet->getColumnDimension('B')->setWidth(30);
        $objSheet->getColumnDimension('C')->setWidth(30);
        $objSheet->getColumnDimension('D')->setWidth(30);
        $objSheet->getColumnDimension('E')->setWidth(30);
        $objSheet->getColumnDimension('F')->setWidth(30);
        $objSheet->getColumnDimension('G')->setWidth(30);
        $objSheet->getColumnDimension('H')->setWidth(30);
        $objSheet->getColumnDimension('I')->setWidth(30);
        $objSheet->getColumnDimension('J')->setWidth(30);
        $objSheet->getColumnDimension('K')->setWidth(30);

        //循环打印数据
        for($i=0;$i<count($item);$i++){
            $objSheet->setCellValueExplicit("A".($i+2),"".$item[$i]["productItemOEM_SN"]);
            $objSheet->setCellValueExplicit("B".($i+2),"".$item[$i]["productBatchNumber"]);
            $objSheet->setCellValueExplicit("C".($i+2),"".pg_State($item[$i]["productItemID"]));
            $objSheet->setCellValueExplicit("D".($i+2),"".$item[$i]["productItemPAYG_SN"]);
            $objSheet->setCellValueExplicit("E".($i+2),"".$item[$i]["lifeCycleStatus"]);
            $objSheet->setCellValueExplicit("F".($i+2),"".$item[$i]["otpGeneratorHash_Top"]);
            $objSheet->setCellValueExplicit("G".($i+2),"".$item[$i]["otpGeneratorHash_Root"]);
            $objSheet->setCellValueExplicit("H".($i+2),"".$item[$i]["otpGeneratorCoce_Count"]);
            $objSheet->setCellValueExplicit("I".($i+2),"".$item[$i]["otpGeneratorCurrent_Hash_Index"]);
            $objSheet->setCellValueExplicit("J".($i+2),"".$item[$i]["otpHashChainLength"]);
            $objSheet->setCellValueExplicit("K".($i+2),"".$item[$i]["otpMaxHCJ"]);
        }
        $objWriter = \PHPExcel_IOFactory::createWriter($objexcle, 'Excel2007');//生成一个Excel2007文件
        $objWriter->save("php://output");
        exit();
    }

    /**
     *初始化产品OTP密码
     */
    public function Initialization_Ietm_OTP($productItemID,$sytem){
        $otpsytem=Db::table("otpsystem")->where(["otpSystemID" => $sytem])->find();
        $data["otpGeneratorID"]=$productItemID;
        $data["otpSystemID"]=$otpsytem["otpSystemID"];
        $data["otpGeneratorCoce_Count"]=0;
        $data["otpGeneratorCurrent_Hash_Index"]=$otpsytem["otpHashChainLength"];

        $otp_hash=refresh_table_OPT_binding_callback(rand(10000,99999).time().rand(10000,99999),$otpsytem["otpHashChainLength"]);
        $data["otpGeneratorHash_Top"]=$otp_hash["hash_top"];
        $data["otpGeneratorHash_Root"]=$otp_hash["hash_root"];
        Db::table('otpGenerator')->insert($data);
    }
    /**
     *产品充值
     * */
    public function recharge(){
        $id   = $this->request->param("id");
        $this->assign('id', $id);
        return $this->fetch();
    }
    /**
     *产品充值提交
     * */
    public function rechargePost(){
        $data   = $this->request->param();
        $productitem=Db::table('otpgenerator')->where('otpGeneratorID',$data["id"])->find();
        $response=refresh_table_OPT_generator_callback($productitem["otpGeneratorHash_Root"],$productitem["otpGeneratorCurrent_Hash_Index"],$data["day"]);
        $Recharge["otpGeneratorID"]=$data["id"];
        $Recharge["otpGeneratorCoce_Count"]=$productitem["otpGeneratorCoce_Count"]+1;
        $Recharge["otpGeneratorCurrent_Hash_Index"]=$productitem["otpGeneratorCurrent_Hash_Index"]-$data["day"];
        Db::table('otpgenerator')->where("otpGeneratorID",$Recharge["otpGeneratorID"])->update($Recharge);

        $codeHistory=Db::table('codeHistory')->where("otpGeneratorID",$data["id"])->order("codeIndex","desc")->select();
        $code["otpGeneratorID"]=$data["id"];
        if(count($codeHistory)>0){
            $code["codeIndex"]=$codeHistory[0]["codeIndex"]+1;
        }else{
            $code["codeIndex"]=1;
        }
        $code["codeValue"]=$response["otp_hash_formatted"];
        $code["codeDate"]=date("Y-m-d H-i-h");
        $result=Db::table('codehistory')->insert($code);

        if ($result) {
            $this->success("充值成功！", url('item/index'));
        } else {
            $this->error("充值失败！");
        }
    }
}

