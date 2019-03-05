<?PHP

namespace Lib;

class Reloj {

  private $ip;
  private $port;

  public function __construct($ip, $port = 4370){
    $this->ip = $ip;
    $this->port = $port;
  }

  public function get(){
    $zk = new \ZKLib($this->ip, $this->port);
    $ret = $zk->connect();
    sleep(1);
    if($ret){
        $zk->disableDevice();
        sleep(1);
    }

    $attendance = $zk->getAttendance();
    $relojSerie = explode("=", $zk->serialNumber())[1];
    sleep(1);
    $rows = array();
    while(list($idx, $attendancedata) = each($attendance)){
        $codigo = $attendancedata[1];
        $fechaHora = date("Y-m-d H:i:s", strtotime($attendancedata[3]));
        
        //array_push($rows, $attendancedata);

        array_push($rows, array(
          "codigo" => $codigo,
          "fecha_hora" => $fechaHora,
          "reloj_serie" => $relojSerie
        ));
    }
    /*$user = $zk->getUser();
    sleep(1);
    while(list($uid, $userdata) = each($user)){
      array_push($rows, $userdata);
    }*/
    $zk->enableDevice();
    sleep(1);
    $zk->disconnect();

    return $rows;
  }

  public function setUser($uid, $userId, $nombre){
    $zk = new \ZKLib($this->ip, $this->port);
    $ret = $zk->connect();
    sleep(1);
    if($ret){
        $zk->disableDevice();
        sleep(1);
    }

    //$zk->enrollUser($uid);
    $zk->setUser($uid, $userId, $nombre, '', LEVEL_USER);

    $zk->enableDevice();
    sleep(1);
    $zk->disconnect();
  }
}

?>
