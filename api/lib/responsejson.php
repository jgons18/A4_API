<?php
/**
 * Created by PhpStorm.
 * User: linux
 * Date: 27/02/19
 * Time: 18:48
 */

namespace Api\Lib;


class ResponseJson
{
    protected $data;
    public function __construct($data)
    {
        $this->data=$data;
        return $this;
    }

    /**
     * Render the response as JSON.
     *
     * @return string
     */
    public function render(){
        //Allow CORS
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: *');
        header('Content-Type:application/json');
        return json_encode($this->data);
    }
}