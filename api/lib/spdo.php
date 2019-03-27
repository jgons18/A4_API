<?php
/**
 * Created by PhpStorm.
 * User: linux
 * Date: 27/02/19
 * Time: 19:16
 */

namespace Api\Lib;


class SPDO extends \PDO
{
    static $intance;

    function __construct()
    {
        $dsn='mysql:dbname=A3;host=172.17.0.2';
        $usr='root';
        $pwd='linuxlinux';
        try{
            parent::__construct($dsn, $usr, $pwd);
        }catch (\PDOException $e){
            echo $e->getMessage();
        }

    }

    static function singleton(){
        if(!(self::$intance) instanceof self){
            self::$intance=new self();
        }
        return self::$intance;
    }

    function oper($sql,$request,$params,$msg){
        $stmt=self::$intance->prepare($sql);
        foreach ($params as $param){
            $stmt->bindValue(':'.$param,$request->parameters[$param]);
        }
        if($stmt->execute()){
            return ['msg'=>$msg];
        }else{
            return ['msg'=>'Failded operation'];
        }

    }
}