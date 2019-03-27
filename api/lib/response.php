<?php
/**
 * Created by PhpStorm.
 * User: linux
 * Date: 27/02/19
 * Time: 18:42
 */

namespace Api\Lib;

use Api\Lib\ResponseJson;


class Response
{
    public static function create($data,$format){
        switch ($format){
            case 'application/json':
            default:
                $obj=new ResponseJson($data);
                break;
        }
        return $obj;
    }
}