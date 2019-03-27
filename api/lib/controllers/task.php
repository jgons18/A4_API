<?php
/**
 * Created by PhpStorm.
 * User: linux
 * Date: 27/03/19
 * Time: 17:29
 */

namespace Api\Lib\Controllers;

use Api\Lib\SPDO;

class Task
{
    protected $gbd;
    function __construct()
    {
        $this->gbd=SPDO::singleton();
    }
    /**
     * obtener tareas curl -v -X GET api/task
     * obtener tarea  curl -v -X GET api/task/id
     * @param null $request
     * @return array
     */
    function get($request=null){
        if($_SERVER['REQUEST_METHOD']!='GET'){
            return ['error' => 'Request not valid'];
        }

        else{
            if($request->parameters==null){

                $sql="SELECT id_task,usuarios_id_user,descripcion,pendiente,fecha FROM todo_tasks";
                $stmt=$this->gbd->prepare($sql);
                $stmt->execute();
                $rows=$stmt->fetchAll(\PDO::FETCH_ASSOC);
            }else{
                $sql="SELECT id_task,id_usuario,titulo,estado,fecha_creado,fecha_act FROM tareas WHERE id=:id";
                $stmt=$this->gbd->prepare($sql);
                $id=$request->parameters;
                $stmt->bindValue(':id_task',$id,\PDO::PARAM_INT);
                $stmt->execute();
                $rows=$stmt->fetchAll(\PDO::FETCH_ASSOC);
            }
            if($rows==null){
                return ['msg'=>'No se ha encontrado la tarea'];
            }
            return $rows;
        }
    }
    /**
     * Añadir tarea curl -v -X POST api/task -d '{"field":"value","field2":"value2"}'
     * @param null $request
     * @return array
     */
    function post($request=null){
        if($_SERVER['REQUEST_METHOD']!='POST'){
            return array('error'=>'Request not valid');
        }else{
            if(!empty($request->parameters['usuarios_id_user']) && !empty($request->parameters['descripcion']) ){
                // asigno estado pendiente
                $estado=0;
                // asigno la fecha actual
                $datenow=date('Y-m-d H:i:s');
                $sql="INSERT INTO todo_tasks (usuarios_id_user, descripcion, pendiente, fecha) ";
                $sql.="VALUES (:usuarios_id_user, :descripcion, :pendiente, :fecha)";
                $stmt=$this->gbd->prepare($sql);
                $stmt->bindValue(':id_usuario',$request->parameters['usuarios_id_user'],\PDO::PARAM_INT);
                $stmt->bindValue(':descripcion',$request->parameters['descripcion'],\PDO::PARAM_STR);
                $stmt->bindValue(':pendiente',$estado,\PDO::PARAM_INT);
                $stmt->bindValue(':fecha',$datenow,\PDO::PARAM_STR);
                $result=$stmt->execute();

                if($result){
                    return ['msg'=>'Tarea creada correctamente'];
                }else{
                    return ['msg'=>'No se ha podido crear la tarea'];
                }
            }else{
                return ['msg' => 'Comprueba que no falte ningún campo por completar!'];
            }
        }
    }
    /**
     * Eliminar tarea curl -v -X DELETE api/task/id
     * @param $request
     * @return array
     */
    function delete($request=null){
        if($_SERVER['REQUEST_METHOD']!='DELETE'){
            return array('error'=>'Request not valid');
        }
        if($request->parameters==null){
            return ['msg'=>'Task not defined'];
        }else{
            $id=$request->parameters;
            $sql="DELETE FROM todo_tasks WHERE id_task=:id_task";
            $stmt=$this->gbd->prepare($sql);
            $stmt->bindValue(':id_task',$id,\PDO::PARAM_INT);
            if($stmt->execute()){
                if($stmt->rowCount()!=0){
                    return ['msg'=>'Tarea eliminada correctamente'];
                }else{
                    return ['msg'=>'No se ha podido eliminar la tarea'];
                }
            }else{
                return ['msg'=>'Fallo al eliminar la tarea'];
            }
        }
    }
    /**
     * Actualizar curl -v -X PUT api/task/id -d '{"field":"value","field2":"value2"}'
     * @param $request
     * @return array
     */
    function put($request=null){
        if($_SERVER['REQUEST_METHOD']!='PUT'){
            return array('error'=>'Request not valid');
        }else{
            if(empty($request->parameters['id'])){
                return ['msg'=>'Task not defined'];
            }else{
                $id=$request->parameters['id'];
                // fecha de la modificación
                $datenow=date('Y-m-d H:i:s');
                // a través de la siguiente variable,compruebo los campos que habrá que actualizar
                $camposaactualizar=false;
                foreach ($request->parameters as $field=>$value){
                    if($field!="id_task"){
                        $camposaactualizar=true;
                        $sql="UPDATE todo_tasks SET $field=:$field,fecha=:fecha WHERE id_task=:id_task";
                        $stmt=$this->gbd->prepare($sql);
                        $stmt->bindValue(':id_task',$id,\PDO::PARAM_INT);
                        $parameter=":".$field;
                        if($field=="pendiente"){
                            if ($value=="Pendiente"){
                                $value=0;
                            }elseif($value=="Tarea finalizada"){
                                $value=1;
                            }else{
                                return ['msg'=>'Estado invalido'];
                            }
                            $stmt->bindValue($parameter,$value,\PDO::PARAM_INT);
                        }else{
                            $stmt->bindValue($parameter,$value,\PDO::PARAM_STR);
                        }
                        $stmt->bindValue(':fecha',$datenow,\PDO::PARAM_STR);
                        $result=$stmt->execute();
                        if(!$result) {
                            return ['msg'=>'No se pudo actualizar la tarea'];
                        }else{
                            if($stmt->rowCount()==0){
                                return ['msg'=>'No se pudo actualizar la tarea'];
                            }
                        }
                    }
                }
                if($camposaactualizar){
                    return ['msg'=>'Tarea actualizada'];
                }else{
                    return ['msg'=>'No hay campos para actualizar'];
                }
            }
        }
    }


}