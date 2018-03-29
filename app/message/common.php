<?php
    use think\Db;


    /**
     *代理人ID转换名称
     **/
    function agent_name($id){
        $name=Db::table('agent')->where('agentID',$id)->find();
        return $name["lastName"].$name["firstName"];
    }
    /**
     *代理人ID转换名称
     **/
    function user_name($id){
        $name=Db::table('user')->where('userD',$id)->find();
        return $name["lastName"].$name["firstName"];
    }
