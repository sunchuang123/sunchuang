<?php
    use think\Db;


    /**
     *国家ID转换名称
     **/
    function country_name($id){
        $nameNative=Db::table('country')->where('countryID',$id)->value('nameNative');
        return $nameNative;
    }
    /**
     *代理人ID转换名称
     **/
    function agent_name($id){
        $name=Db::table('agent')->where('agentID',$id)->field('lastName,firstName')->find();
        return $name["lastName"]."-".$name["firstName"];
    }
