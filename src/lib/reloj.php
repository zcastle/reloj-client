<?PHP

namespace Lib;

class Reloj {

  private $logger;

  public function __construct($logger){
    $this->logger = $logger;
  }

  public function get($ip, $port = 4370){
    $zk = new ZKLib($ip, $port);
    $ret = $zk->connect();
    if($ret){
        $zk->disableDevice();
        sleep(1);
    }

    $attendance = $zk->getAttendance();
    //$reloj_serie = $zk->serialNumber();
    sleep(1);
    $rows = array();
    while(list($idx, $attendancedata) = each($attendance)){
        $codigo = $attendancedata[1];
        $fechaHora = date("Y-m-d H:i:s", strtotime($attendancedata[3]));

        array_push($rows, array(
            "codigo" => $codigo,
            "fecha_hora" => $fechaHora
        ));
    }
    $zk->enableDevice();
    sleep(1);
    $zk->disconnect();

    return $rows;
  }
}

?>
