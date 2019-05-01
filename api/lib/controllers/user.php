<?php
/**
 * Created by PhpStorm.
 * User: linux
 * Date: 27/02/19
 * Time: 19:05
 */

namespace Api\Lib\Controllers;

use Api\Lib\SPDO;

class User
{
    protected $gdb;

    function __construct()
    {
        $this->gdb=SPDO::singleton();
    }

    /**
     * En caso de estar en local -> curl -v -X GET http://localhost:8067/api/user
     * get users data: curl -X GET api/user
     * get user data: curl -X GET api/user/id
     * @param $request
     * @return array
     */
    function get($request=null){
        if($_SERVER['REQUEST_METHOD']!='GET'){
            return ['error'=>'Request not valid'];
        }
        //select * from users
        else{
            /*$sql="SELECT * from usuarios";
            $stmt=$this->gdb->prepare($sql);
            $stmt->execute();
            $rows=$stmt->fetchAll(\PDO::FETCH_ASSOC);*/

            if($request->parameters==null){
                $sql="SELECT * from usuarios";
                $stmt=$this->gdb->prepare($sql);
                $stmt->execute();
                $rows=$stmt->fetchAll(\PDO::FETCH_ASSOC);
            }else{
                //$sql="SELECT * from usuarios WHERE id_user=:id_user";
                $sql="SELECT * FROM usuarios WHERE id=:id";
                $id=$request->parameters;
                $stmt=$this->gdb->prepare($sql);
                //$stmt->bindValue(':id_user',$id,\PDO::PARAM_INT);
                $stmt->bindValue(':id',$id,\PDO::PARAM_INT);
                $stmt->execute();
                $rows=$stmt->fetchAll(\PDO::FETCH_ASSOC);
            }
            return $rows;
        }

    }
    /**
     * Add user data: curl -v -X POST api/user -d '{"field":"value","field2":"value2"}'
     * @param $request
     * @return array
     */
    function post($request=null){
        if($_SERVER['REQUEST_METHOD']!='POST'){
            return ['error'=>'Request not valid'];
        }
        else{
            /*var_dump($request->parameters);
            die;*/
            if(!empty($request->parameters['nombre'])&&
                !empty($request->parameters['apellidos'])&&
                !empty($request->parameters['email'])&&
                !empty($request->parameters['password'])) {

                //var_dump($request->parameters['nombre']);

                $pass_enc=password_hash($_POST['password'],PASSWORD_BCRYPT,['cost'=>4]);
                $datenow=date('Y-m-d H:i:s');
                $sql="INSERT INTO usuarios(email, password, nombre, apellidos, fecha_creado) VALUES (:email, :password, :nombre, :apellidos, :fecha_creado)";
                //return $this->gdb->oper($sql,$request,['login,nombre,apellidos,edad,passwd'],"User created");
                $stmt=$this->gdb->prepare($sql);
                $stmt->bindValue(':email',$request->parameters['email'],\PDO::PARAM_STR);
                $stmt->bindValue(':password',$pass_enc,\PDO::PARAM_STR);
                $stmt->bindValue(':nombre',$request->parameters['nombre'],\PDO::PARAM_STR);
                $stmt->bindValue(':apellidos',$request->parameters['apellidos'],\PDO::PARAM_STR);
                $stmt->bindValue(':fecha_creado',$datenow,\PDO::PARAM_STR);
                $stmt->execute();
                //$rows=$stmt->fetchAll(\PDO::FETCH_ASSOC);
                if($stmt->execute()){
                    return ['msg'=>'Usuario creado'];
                }else{
                    return ['msg'=>'No se ha podido crear el usuario'];
                }

            }else{
                return ['msg'=>'Bad request'];
            }

        }
    }
    /**
     * Update user data: curl -X PUT api/user -d '{"field":"value"}'
     * @param $request
     * @return array
     */
    function put($request){
        if($_SERVER['REQUEST_METHOD']!='PUT'){
            return ['error'=>'Request not valid'];
        }else{
            //$data=[$name,$login,$pass];
            //$id=$request->getId_user();

            //$id=$request->parameter['id_user'];
            $id=$request->parameters['id'];
            //if(!empty($request->parameter['passwd'])){
            if(!empty($request->parameters['password'])){
                $pass_enc=password_hash($_POST['password'],PASSWORD_BCRYPT,['cost'=>4]);

            }
            $datenow=date('Y-m-d H:i:s');
            $camposaactualizar=false;
            foreach ($request->parameter as $field=>$value){
                /*$name=$request->parameter['name'];
                $surname=$request->parameter['apellidos'];
                $login=$request->parameter['login'];
                $pass=$request->parameter['passwd'];*/

                //$sql="UPDATE usuarios SET $field=:$field WHERE id=:$id";
                $sql="UPDATE usuarios SET $field=:$field,fecha_act=:fecha_act WHERE id=:id";

                //$output= $this->gdb->oper($sql,$request,$request->parameters,"User updated");
                $stmt=$this->gdb->prepare($sql);
                //$stmt->bindValue(":id_user",$id);
                $stmt->bindValue(':id',$id,\PDO::PARAM_INT);
                $parameter=":".$field;
                $stmt->bindValue($parameter,$value,\PDO::PARAM_STR);
               // $stmt->bindValue(":value",$request->parameter[$field]);
                $stmt->bindValue(':fecha_act',$datenow,\PDO::PARAM_STR);
                $stmt->execute();
                if($stmt->execute()){
                    return ['msg'=>'Usuario actualizado'];
                }else{
                    return ['msg'=>'No se ha podido actualizar el usuario'];
                }
            }


        }

    }

    /**
     * Delete user: curl -v -X DELETE api/user/id
     * @param $request
     * @return array
     */

    function delete($request){
        if($_SERVER['REQUEST_METHOD']!='DELETE'){
            return ['error'=>'Request not valid'];
        }
        if($request->parameters==null){
            return ['msg'=>'Usuario no definido'];
        }else{
            $id=$request->parameters['id_user'];
            //$sql="DELETE FROM usuarios WHERE id_user=:id_user";
            $sql="DELETE FROM usuarios WHERE id=:id";
            $stmt=$this->gdb->prepare($sql);
            //$stmt->bindValue(':id_user',$id);
            $stmt->bindValue(':id',$id,\PDO::PARAM_INT);
            $stmt->execute();
            if($stmt->execute()){
                return ['msg'=>'Usuario eliminado correctament'];
            }else{
                return ['msg'=>'No se ha podido eliminar el usuario'];
            }
        }

    }

}