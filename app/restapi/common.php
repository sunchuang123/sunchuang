<?php
    /**
     *CURL_POST
     * */
    function curl_post($url,$data){
        $headers = array();
        $headers[] = 'Authorization:Basic b3ZlczpvdmVzMTIz';
        $headers[] = 'X-UbiLink-Tenant:oves1234567890';
        $headers[] = 'User-Agent:Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9) Gecko/2008052906 Firefox/3.0';
        $headers[] = 'ACCEPT: */*';
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Content-Length:'.strlen($data);
        // 创建一个新cURL资源
        $ch = curl_init();

        // 设置URL和相应的选项
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        // post数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // post的变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        //设置文件头
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // 抓取URL并把它传递给浏览器
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $res=curl_exec($ch);
        // 关闭cURL资源，并且释放系统资源
        curl_close($ch);
        return json_decode($res);
    }
