<?php
    use think\Db;


    /**
     *国家
     **/
    function country_name($id){
        $name=Db::table('country')->where('countryID',$id)->value('nameNative');
        return $name;
    }
    /**
     *国家
     **/
    function distributor_name($id){
        $name=Db::table('distributor')->where('distributorID',$id)->value('customerName');
        return $name;
    }
