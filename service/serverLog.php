<?php
include "../base/config.php";
class serverLog extends Config{
  var $Prefix = "serverLog";
  var $formName = "serverLogForm";
  var $tableName = "ref_server";
  function __construct(){
      $getDataServer = $this->sqlQuery("select * from ref_server");
      while ($dataServer = $this->sqlArray($getDataServer)) {
          $arrayResultLog = array(
              'memoryUsage' => $this->memoryUsage($dataServer['id']),
              'cpuUsage' => $this->cpuUsage($dataServer['id']),
              'diskUsage' => $this->diskUsage($dataServer['id']),
          );
          $dataLogServer =array(
              'id_server' => $dataServer['id'],
              'tanggal' => date("Y-m-d"),
              'jam' => date("H:i"),
              'result' => json_encode($arrayResultLog),
          );
          $query = $this->sqlInsert("log_server",$dataLogServer);
          $this->sqlQuery($query);
          echo $dataServer['nama_server'] ." => ".json_encode($arrayResultLog)." \n";
      }
  }
  function memoryUsage($id) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    $memUsage = '"%u%%"';
    return $this->sshCommand($sshConnection,"awk '/^Mem/ {printf($memUsage, 100*$3/$2);}' <(free -m)");
  }
  function cpuUsage($id) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    return str_replace("\n","",$this->sshCommand($sshConnection,"grep 'cpu ' /proc/stat | awk '{usage=($2+$4)*100/($2+$4+$5)} END {print usage}'"));
  }
  function diskUsage($id) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    $diskUsg = '"%s"';
    $varNF = "$"."NF";
    $bagi = '"/"';
    return $this->sshCommand($sshConnection,"df -h | awk '$varNF==$bagi{printf $diskUsg, $5}'");
  }

}
$serverLog = new serverLog();

 ?>
