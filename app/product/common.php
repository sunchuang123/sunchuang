<?php
    use think\Db;


    /**
     *产品型号ID转换名称
     **/
    function model_name($id){
        $name=Db::table('productmodel')->where('productModelID',$id)->value('producModeltName');
        return $name;
    }
    /**
     *PG状态
     **/
    function pg_State($id){
        $pgState=Db::table('productstatus')->where('productItemID',$id)->order(["updateTimestamp" => "DESC"])->limit(1)->value('pgState');
        return $pgState;
    }
    /**
     *系列名称
     **/
    function Family_name($id){
        $productFamilyName=Db::table('productfamily')->where('productFamilyID',$id)->value('productFamilyName');
        return $productFamilyName;
    }
    /**
    *获取最新的密码
    */
    function code($id){
        $code=Db::table('codeHistory')->where('otpGeneratorID',$id)->order("codeDate","desc")->select();
        if(count($code)>=1){
            echo $code[0]["codeValue"];
        }
    }
    /**
     *获取原始设备制造商编号
     */
    function OEM($id){
        $oem=Db::table('productitem')->where('productItemID',$id)->value('productItemOEM_SN');
        echo $oem;
    }
    /*---------------------------------------------------------------计算密码方法-------------------------------------------------------------*/
    define("START_CHR", '*');
    define("END_CHR", '#');
    define("GAP_CHR", ' ');
    define("STRL21", 21);
    /**
     * 密码初始化
     * */
    function refresh_table_OPT_binding_callback($seed,$hash_chain_length)
    {
        $hash_root = OTP_Hash_64($seed);
        $hash_top = $hash_root;
        $hash_chain_length = (int) $hash_chain_length;
        for ($i = 1; $i <= $hash_chain_length; $i++) {$hash_top = OTP_Hash_64 ($hash_top); };

        $response = array(
            'seed' => $seed,
            'hash_root' => $hash_root,
            'hash_length' => $hash_chain_length,
            'hash_top' => $hash_top
        );
        return $response;
    }

    /**
     *OTP哈希64位加密
     */
    function OTP_Hash_64($msg){
        $otp_hash=sha1($msg);
        for($i=0;$i<=4;$i++){
            $hash_hex[$i]=intval(substr($otp_hash,$i*8,8),16);
        };
        $hash_hex[0]^=$hash_hex[2];
        $hash_hex[0]^=$hash_hex[4];
        $hash_hex[1]^=$hash_hex[3];
        $otp_hash=sprintf("%'08X%'08X",$hash_hex[0],$hash_hex[1]);
        return $otp_hash;
    }
    /**
     *生成实际的密码和root
     */
    function refresh_table_OPT_generator_callback($hash_root,$current_has_index,$hash_jump){
        $otp_hash = OTP_hex($hash_root, $current_has_index, $hash_jump);
        $otp_hash_formatted = dec_str_padding_low_high($otp_hash);
        $response = array(
            'otp_hash' => $otp_hash,
            'otp_hash_formatted' => $otp_hash_formatted
        );
        return $response;
    }
    function OTP_hex($hash_root, $current_has_index, $hash_jump){
        $otp_hash_hex = $hash_root;
        $K = $current_has_index - $hash_jump;
        for ($i = 1; $i <= $K; $i++) {$otp_hash_hex = OTP_Hash_64 ($otp_hash_hex);	};
        return $otp_hash_hex;
    }
    function dec_str_padding_low_high($hash_str){
        $hash_low_high = hash_str_to_Low_High_Bytes($hash_str);
        $low_10 = sprintf("%'010d",$hash_low_high[0]);
        $high_10 = sprintf("%'010d",$hash_low_high[1]);
        $hash_dec_str = '0' . $low_10 . $high_10;
        $strl= strlen($hash_dec_str);
        $hash_form = END_CHR;
        for ($j =1 ; $j <=6 ; $j++){
            $hash_form = GAP_CHR . substr($hash_dec_str, $strl-3*$j, 3) .  $hash_form;
        };
        $hash_form = START_CHR . substr($hash_dec_str, 0, 3) .  $hash_form;
        return $hash_form;
    };
    function hash_str_to_Low_High_Bytes($hash_str){
        $otp_hash_low_high[0] = intval ( substr($hash_str, 0, 8), 16 );
        $otp_hash_low_high[1] = intval ( substr($hash_str, 8, 8), 16 );
        return $otp_hash_low_high;
    };
    /**
     *验证密码天数
     * */
    function refresh_table_Parse_OTP_callback(){
//
        $otp_received = $_GET["otp_received"];
        $target_hash = $_GET["device_current_hash"];
        $payg_state = $_GET["device_payg_state"];
        $output_state = $_GET["device_output_state"];
        $CDT = $_GET["device_CDT"];
        $maxN = $_GET["device_maxN"];

        $otp_hash = extract_hash_str_low_high($otp_received);
        $test_hash = $otp_hash;
        $hcj_calc = 0;
        do {
            $test_hash = OTP_Hash_64 ($test_hash);
            $hash_hit = ($test_hash == $target_hash);
            $hcj_calc++;
        } while ( ($hcj_calc <= $maxN+$maxN) and (!$hash_hit) );
        if ($hcj_calc < $maxN)
        {
            $hash_Test_result = "OTP_ACCEPTED";
            $payg_state = "PAYG";
            $output_state = "ENABLED";
            $CDT = $_POST["device_CDT"] + $hcj_calc;
            $target_hash = $otp_hash; // update the hash to newly accepted input
        }
        elseif ( ($hcj_calc >= $maxN) and ($hcj_calc < $maxN+$maxN) )
        {
            $hash_Test_result = "OTP_ACCEPTED";
            $payg_state = "FREE"; // FREE event detected
            $output_state = "ENABLED";
            $CDT = 0;
            $target_hash = $otp_hash; // update the hash to newly accepted input
        }
        else
        {
            $hash_Test_result = "OTP_REJECTED";
        };
        $response = array(
            'hash_test' => $hash_Test_result,
            'accepted_hash' => $target_hash,
            'device_output_state' => $output_state,
            'device_payg_state' => $payg_state,
            'hcj_calc' => $hcj_calc,
            'test_hash' => $test_hash,
            'hash_hit' => $hash_hit,
            'device_CDT' => $CDT
        );

        echo json_encode($response);
    }
    function extract_hash_str_low_high($formatted_hash_str){
        $hash_low_high = extract_hash_low_high($formatted_hash_str);
        $hash_str = sprintf("%'08X%'08X",$hash_low_high[0],$hash_low_high[1]);
        return $hash_str;
    };
    function extract_hash_low_high($formatted_hash_str){

        // strip out spaces
        $formatted_hash_str = str_replace(GAP_CHR, '', $formatted_hash_str);
        $formatted_hash_str = str_replace(START_CHR, '', $formatted_hash_str);
        $formatted_hash_str = str_replace(END_CHR, '', $formatted_hash_str);

    //  break out the low and high 10-digit segments, ignoring the leading zero
        $hash_low_high[0] = intval(substr($formatted_hash_str, 1, 10));
        $hash_low_high[1] = intval(substr($formatted_hash_str, 11, 10));

        return $hash_low_high;
    };