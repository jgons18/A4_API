<?php
/**
 * Created by PhpStorm.
 * User: linux
 * Date: 27/02/19
 * Time: 17:23
 */

namespace Api\Lib;

use Api\Lib\Request;
use Api\Lib\Response;

class Rest
{
    function __construct()
    {
        $request = new Request();
        //si está definido...
        if(isset($_SERVER['PATH_INFO'])){
            //estamos extrayendo información sobre los diferentes elementos, convierte el array de elements. Extraeremos la información que nos dan a través del servidor
            $request->url_elements=explode('/',trim($_SERVER['PATH_INFO'],'/'));
        }
        //extraremos el método y lo pondremos en mayúscula
        $request->method=strtoupper($_SERVER['REQUEST_METHOD']);

        switch ($request->method){
            case 'GET':// api/user
                //$request->parameters=$_GET;//si es un get, los parámetros se encontrarían en el subarray $get
                //esta pregunta nos permite preguntar por ej. api/user/1 o api/user/?id=1
                $request->parameters=(count($request->url_elements)>1)?$request->url_elements[1]:$_GET;
                break;
            case 'POST':
                //$request->parameters=file_get_contents('php://input');
                $request->parameters=json_decode(file_get_contents('php://input'),true);
                //$request->parameters=$_POST;
                //var_dump($request->parameters);
                break;
            case 'PUT':
                //parse_str(file_get_contents('php://input'),$request->parameters);
                $request->parameters=json_decode(file_get_contents('php://input'),true);
                $request->parameters['id']=count($request->url_elements)>1?$request->url_elements[1]:$_GET;
                break;
            case 'DELETE':
                //parse_str(file_get_contents('php://input'),$request->parameters);
                $request->parameters=count($request->url_elements)>1?$request->url_elements[1]:$_GET;
                break;
            default:
                header('HTPP/1.1 405 Metho not allowed');
                header('Allow: GET, PUT,POST and DELETE');
                break;
        }
        //enrutamiento

        //si no está vacío el url elements
        if(!empty($request->url_elements)){
            $controller_name=ucfirst($request->url_elements[0]);
            $file=strtolower(LIB.'controllers'.DS.$controller_name.'.php');//estamos definiendo la ruta del controlador
            //si existe el fichero del controlador
            //if(is_readable($file)){
            try{
                // doble barra por que con la ' ignora la \
                $path_controller='\Api\Lib\Controllers\\'.$controller_name;
                //$controller=new $path_controller;
                $controller=new $path_controller;
                $action_name=strtolower($request->method);
                //guardamos la respuesta del controlador
                $response_str=call_user_func_array(array($controller,$action_name),array($request));
            }catch (\Exception $e){
                header('HTTP/1.1 404 Not found');
                $response_str='Unknown request: '.$request->url_elements[0];
            }

        }else{
           $response_str='Unknown request';
        }
        //enviar respuesta
        $resp=Response::create($response_str,$_SERVER['HTTP_ACCEPT']);
        echo $resp->render();
    }


}