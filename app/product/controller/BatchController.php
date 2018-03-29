<?php
namespace app\product\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class BatchController extends AdminBaseController
{

    /**
     * 添加记录管理列表
     */
    public function index()
    {
        $param = $this->request->param();
        $date=isset($param["date"])?$param["date"]:"";
        $keyword=isset($param["keyword"])?$param["keyword"]:"";

        $batch=Db::table('productbatch')
            ->where('productBatchNumber','like',"%".$keyword."%")
            ->where(function($query) use ($date){
                if($date!=""){
                    $query->where('productBatchDate', '>=', $date)->where('productBatchDate', '<', date("Y-m-d",strtotime($date)+86400));
                }
            })
            ->order(["productBatchID" => "DESC"])->paginate(10);
        $page = $batch->render();

        $this->assign('keyword',$keyword);
        $this->assign('date',$date);
        $this->assign('batch', $batch);
        $this->assign('page', $page);
        return $this->fetch();
    }
    /**
     *编辑添加记录
     * */
    public function itembatch()
    {
        $id = $this->request->param("id");
        $data = Db::table('productBatch')->where(["productBatchID" => $id])->find();
        if (!$data) {
            $this->error("添加记录不存在！");
        }

        $this->assign("data", $data);
        return $this->fetch();
    }

    /**
     *编辑产品项目提交
     * */
    public function batcheditpost()
    {
        if ($this->request->isPost()) {
            $data   = $this->request->param();
            if (Db::table('productBatch')->update($data) !== false) {
                $this->success("保存成功！", url('batch/index'));
            } else {
                $this->error("保存失败！");
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

        //开始查询数据
        $item=Db::table('productitem')
            ->alias('item')
            //左联合
            ->join(['otpgenerator'=>'otp', 'cmf_'],'item.productitemID = otp.otpGeneratorID','left')
            ->join(['productbatch'=>'batch', 'cmf_'],'item.productBatchID = batch.productBatchID','left')
            ->join(['otpsystem'=>'otpsystem', 'cmf_'],'otp.otpsystemID = otpsystem.otpsystemID','left')
            //条件
            ->where("item.productBatchID",$param["id"])
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
     *出厂锁定
     * */
    public function locking(){
        $id = $this->request->param("id");
        $data=Db::table('productitem')->where('productBatchID',$id)->select();
        for($i=0;$i<count($data);$i++){
            $this->rechargePost($data[$i]["productItemID"]);
        }
        if (Db::table('productBatch')->update(["productBatchID"=>$id,"locking"=>1]) !== false) {
            $this->success("锁定成功！", url('batch/index'));
        } else {
            $this->error("锁定失败！");
        }
    }
    /**
     *产品充值提交
     * */
    public function rechargePost($id){
        $productitem=Db::table('otpgenerator')->where('otpGeneratorID',$id)->find();
        $response=refresh_table_OPT_generator_callback($productitem["otpGeneratorHash_Root"],$productitem["otpGeneratorCurrent_Hash_Index"],1);
        $Recharge["otpGeneratorID"]=$id;
        $Recharge["otpGeneratorCoce_Count"]=$productitem["otpGeneratorCoce_Count"]+1;
        $Recharge["otpGeneratorCurrent_Hash_Index"]=$productitem["otpGeneratorCurrent_Hash_Index"]-1;
        Db::table('otpgenerator')->where("otpGeneratorID",$Recharge["otpGeneratorID"])->update($Recharge);

        $codeHistory=Db::table('codeHistory')->where("otpGeneratorID",$id)->order("codeIndex","desc")->select();
        $code["otpGeneratorID"]=$id;
        if(count($codeHistory)>0){
            $code["codeIndex"]=$codeHistory[0]["codeIndex"]+1;
        }else{
            $code["codeIndex"]=1;
        }
        $code["codeValue"]=$response["otp_hash_formatted"];
        $code["codeDate"]=date("Y-m-d H-i-h");
        $result=Db::table('codehistory')->insert($code);
    }
    /**
    *追加产品
    **/
    public function additionalProduct(){
        $id = $this->request->param("id");
        $this->assign("id", $id);
        return $this->fetch();
    }
    /**
     *追加产品提交
     **/
    public function additionalProductPost(){
        $data = $this->request->param();
        $batchid=$data["productBatchID"];
        $number=$data["number"];

        //查询前缀与后缀和最后一条产品的原始设备制造商编号
        $batch=Db::table('productbatch')->where('productBatchID',$batchid)->find();
        $item=Db::table('productitem')->where('productBatchID',$batchid)->order(["productItemID" => "DESC"])->limit(0,1)->find();

        //开始计算出原始设备制造编号的指针
        $prefix_len=strlen($batch["prefix"]);
        $suffix_len=strlen($batch["suffix"]);
        //减去前缀
        $str=substr($item["productItemOEM_SN"],$prefix_len);
        //减去后缀
        if($suffix_len!=0){
            $str=substr($str,0,-$suffix_len);
        }
        //获取年月
        $ym=substr($str,0,4);
        //获取编号长度
        $str_len=(strlen($str)-4);
        //减去年月
        $str=substr($str,4);
        //利用正则表达式去除前面的0
        preg_match("/0*/",$str,$m);
        //获取0的长度
        $m_len=strlen($m[0]);
        if($m_len>=1){

            $str=substr($str,$m_len);
        }
        //重复性查询
        for($i=0;$i<$number;$i++){
            //拼接原始设备制造商编号
            $oem_sn="";
            $oem_sn=$batch["prefix"].$ym;
            for($l=0;$l<$m_len;$l++){
                $oem_sn.="0";
            }
            $oem_sn.=$str+$i+1;
            $oem_sn.=$batch["suffix"];

            $rs=Db::table('productitem')->where('productItemOEM_SN',$oem_sn)->select();
            if(count($rs)>0){
                $this->error("产品项目".$oem_sn."已存在");
            }
        }
        //循环添加
        for($i=0;$i<$number;$i++){
            //拼接原始设备制造商编号
            $oem_sn="";
            $oem="";
            //拼接前缀
            $oem_sn=$batch["prefix"].$ym;
            //拼接编号长度部分
            for($l=0;$l<$m_len;$l++){
                $oem.="0";
            }
            $oem.=$str+$i+1;
            $oem_sn.=substr($oem,-$str_len);
            //拼接后缀
            $oem_sn.=$batch["suffix"];
            //设置即将添加的产品数据
            $dataitem["productModelID"]=$item["productModelID"];
            $dataitem["productBatchID"]=$item["productBatchID"];
            $dataitem["lifeCycleStatus"]=$item["lifeCycleStatus"];
            $dataitem["productItemOEM_SN"]=$oem_sn;
            $dataitem["productItemPAYG_SN"]=$oem_sn;
            $result=Db::table('productitem')->insert($dataitem);
            $itemid = Db::table('productitem')->getLastInsID();
            //查询最后一条产品的OTP系统
            $Lastotp=Db::table('otpsystem')
                ->where('otpSystemID','in',function($query)use($item){
                    $query->table('otpgenerator')->where('otpGeneratorID',$item["productItemID"])->field('otpSystemID');
                })
                ->find();
            //设置即将添加的OTP设备
            $otp["otpGeneratorID"]=$itemid;
            $otp["otpSystemID"]=$Lastotp["otpSystemID"];
            $otp["otpGeneratorCurrent_Hash_Index"]=$Lastotp["otpHashChainLength"];
            $otp["otpGeneratorCoce_Count"]=0;
//            //获得otp哈希值
            $otp_hash=refresh_table_OPT_binding_callback(rand(10000,99999).time().rand(10000,99999),$Lastotp["otpHashChainLength"]);
            $otp["otpGeneratorHash_Top"]=$otp_hash["hash_top"];
            $otp["otpGeneratorHash_Root"]=$otp_hash["hash_root"];
            Db::table('otpgenerator')->insert($otp);
            //设置即将添加的产品状态数据
            $status["productItemID"]=$itemid;
            $status["updateTimestamp"]=date("Y-m-d H:i:s",time());
            Db::table('productStatus')->insert($status);
            if($result && $i==($number-1)){
                //更新批次的数量
                $databatch["number"]=$number+$batch["number"];
                Db::table('productbatch')->where('productBatchID',$batch["productBatchID"])->update($databatch);
                $this->success("添加成功！", url('batch/index'));
            }
        }
    }
}

