<?PHP

namespace Lib;

class Reloj {

  private $zk;

  public function __construct($ip, $port = 4370){
    $this->zk = new \ZKLib($ip, $port);
  }

  public function get(){
    $ret = $this->zk->connect();
    sleep(1);
    if($ret){
        $this->zk->disableDevice();
        sleep(1);
    }

    $relojSerie = explode("=", $this->zk->serialNumber())[1];
    //$relojSerie = $this->zk->serialNumber();
    $attendance = $this->zk->getAttendance();
    sleep(1);
    $rows = array();
    while(list($idx, $attendancedata) = each($attendance)){
        $codigo = $attendancedata[1];
        $status = "I";
        if ($attendancedata[2] == 14){
          $status = "O";
        }
        $fechaHora = date("Y-m-d H:i:s", strtotime($attendancedata[3]));
        
        //array_push($rows, $attendancedata);

        array_push($rows, array(
          "codigo" => $codigo,
          "estado" => $status,
          "fecha_hora" => $fechaHora,
          "reloj_serie" => $relojSerie
        ));
    }
    /*$user = $zk->getUser();
    sleep(1);
    while(list($uid, $userdata) = each($user)){
      array_push($rows, $userdata);
    }*/
    
    return $rows;
  }

  public function clear(){
    $this->zk->clearAttendance();
  }

  public function close(){
    $this->zk->enableDevice();
    sleep(1);
    $this->zk->disconnect();
  }

  public function setUser($uid, $userId, $nombre){
    $ret = $this->zk->connect();
    sleep(1);
    if($ret){
        $this->zk->disableDevice();
        sleep(1);
    }

    $zk->enrollUser($uid);
    $zk->setUser($uid, $userId, $nombre, '', LEVEL_USER);

  }
}

?>
