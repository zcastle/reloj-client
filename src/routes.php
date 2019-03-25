<?php

use Slim\Http\Request;
use Slim\Http\Response;
//
use Lib\Reloj;
use Lib\Data;
// Routes

/*$app->get("/crear", function(){
	$reloj = new Reloj("192.168.0.250");
	echo $reloj->setUser(5001, 5001, "MAS");
	echo "crear";
});

$app->get("/test", function(){
    $st = "000001003433303439353830000000000000000000000000000000000189b5bb24000000000000";
	echo intval( str_replace("\0", '', hex2bin( substr($st, 6, 22) ) ) );
});*/

/*$app->get("/test02", function(){
    $zk = new ZKLib('192.168.1.201');
    $ret = $zk->connect();
    if ($ret){
        $zk->disable();
    }
    //print_r($zk->getSerialNumber());
    //print_r($zk->getTime()->format('r'));
    print_r($zk->getAttendances());
    $zk->enable();
    $zk->disconnect();
});*/

$app->group("/reloj/v1", function(\Slim\App $app){

    $app->get('/get', function(Request $request, Response $response, $args) {
        $return = array("success" => true, "message" => null);

        $reloj = null;
        try{
            $reloj = new Reloj("192.168.1.201"); // 201-PRIMAVERA 10.10.10.250-ANGAMOS
            $rows = $reloj->get();
        
            //$return["data"] = $rows;
            $return["message"] = "Total marcaciones descargadas: " . count($rows);
            
            $registradas = 0;
            if(count($rows) > 0){
                $data = new Data($this->db, $this->logger);
                foreach($rows AS $row){
                    if(!$data->existeRegistro($row)){
                        $registradas++;
                        $data->insertarRegistro($row);
                    }
                }
            }
            $return["message"] = $return["message"] . ", total marcaciones registradas: " . $registradas;
            //$reloj->clear();
            $reloj->close();

        }catch(Exception $e){
            $return["message"] = $e->getMessage();
        }finally{
            if($reloj != null){
                $reloj->close();
            }
        }
        return $response->withJson($return);
	    //print_r($return["data"]);
    });

    $app->get('/sync', function(Request $request, Response $response, $args) {
        $result = array("success" => true, "message" => null);

        $body = array("success" => true, "data" => null);
        $data = new Data($this->db, $this->logger);
        $rows = $data->getData();
        $count = count($rows);
        if($count > 0){
            $body["data"] = base64_encode($rows);

            $url = "http://190.40.109.228:8181/reloj/v1/";
            $res = \Httpful\Request::post($url)
                ->expectsJson()
                ->sendsJson()
                ->body($body)
                ->send();

            if(!$res->body->error){
                foreach($rows AS $row){
                    $data->setEnviado((array) $row);
                }
                $result["message"] = "Sincronizado correctamente $count registros";
            } else {
                $result["message"] = $res->body->message;
            }
        }else{
            $result["message"] = "No hay informacion para sincronizar";
        }

        return $response->withJson($result);
    });

    /*$app->post("/create", function(Request $request, Response $response, $args){
        $result = array("success" => true);

        $body = $request->getParsedBody();

        $reloj = new Reloj("10.10.10.250");
        //$reloj->setUser(69, 96, "jc");
        $reloj->setUser($body["id"], $body["codigo"], $body["nombre"]);
        
        return $response->withJson($result);
    });*/
});
