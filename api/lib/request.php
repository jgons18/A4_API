<?php
/**
 * Created by PhpStorm.
 * User: linux
 * Date: 27/02/19
 * Time: 17:25
 */

namespace Api\Lib;


class Request
{
    /**
     * @var array
     */
    public $url_element=array();
    /**
     * @var string
     */
    public $method;
    /**
     * @var array
     */
    public $parameters;
    //?id=1 si lo pusieramos como un array
}