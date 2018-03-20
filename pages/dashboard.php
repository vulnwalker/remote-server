<?php
class dashboard extends baseObject{
  var $Prefix = "dashboard";
  var $formName = "dashboardForm";
  var $tableName = "ref_server";

  function jsonGenerator(){
    $cek = ''; $err=''; $content=''; $json=TRUE;
    foreach ($_POST as $key => $value) {
       $$key = $value;
    }
	  switch($_GET['API']){
      case 'refreshList':{
        if(!empty($filterCari)){
          $arrKondisi[] = "nama_server like '%$filterCari%' ";
          $arrKondisi[] = "alamat_ip like '%$filterCari%' ";
          $arrKondisi[] = "user_ftp like '%$filterCari%' ";
          $arrKondisi[] = "password_ftp like '%$filterCari%' ";
          $arrKondisi[] = "port_ftp like '%$filterCari%' ";
          $arrKondisi[] = "status like '%$filterCari%' ";
          $kondisi = join(" or ",$arrKondisi);
          $kondisi = " where $kondisi ";
        }
        // if(!empty($limitTable)){
        //     if($pageKe == 1){
        //        $queryLimit  = " limit 0,$limitTable";
        //     }else{
        //        $dataMulai = ($pageKe - 1)  * $limitTable;
        //        $dataMulai +=1;
        //        $queryLimit  = " limit $dataMulai,$limitTable";
        //     }
        // }
        // if (!empty($sorter)) {
        //   $kondisiSort = "ORDER BY $sorter $ascending";
        // }
        $cek = "select * from $this->tableName $kondisi";
        $content=array('tableContent' => $this->generateTable($kondisi));
        // $getData = $this->sqlQuery("select * from $tableName $kondisi $kondisiSort $queryLimit");

  		break;
  		}
      case 'statusServer':{
        $getInfoServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
        foreach ($getInfoServer as $key => $value) {
            $$key = $value;
        }
        $content = array(
                          'statusPing' => $this->pingAddress($alamat_ip),
                          'apacheStatus' => $this->apacheStatus($alamat_ip),
                          'osName' => $this->osName($id),
                          'memorySize' => $this->memorySize($id),
                          'diskSize' => $this->diskSize($id),
                        );
  		break;
  		}
      case 'rebootServer':{
        $this->rebootServer($id);
  		break;
  		}
      case 'killApache':{
        $this->killApache($id);
  		break;
  		}
      case 'lifeApache':{
        $this->lifeApache($id);
  		break;
  		}
      case 'Edit':{
        $content = array("idEdit" => $dashboard_cb[0]);
  		break;
  		}
      case 'saveNew':{
  			if(empty($namaServer)){
          $err = "Isi Nama Server";
        }elseif(empty($alamatIP)){
          $err = "Isi Alamat Server";
        }elseif(empty($userFTP)){
          $err = "Isi User FTP";
        }elseif(empty($passwordFTP)){
          $err = "Isi Password FTP";
        }elseif(empty($ftpPort)){
          $err = "Isi PORT FTP";
        }
        if(empty($err)){
          $dataInsert = array(
            'nama_server' => $namaServer,
            'alamat_ip' => $alamatIP,
            'user_ftp' => $userFTP,
            'password_ftp' => $passwordFTP,
            'port_ftp' => $ftpPort,
            'public_key' => $publicKey,
            'status' => $statusServer,
          );
          $query = $this->sqlInsert($this->tableName,$dataInsert);
          $this->sqlQuery($query);
          $cek = $query;
        }
  		break;
  		}
      case 'saveEdit':{
  			if(empty($namaServer)){
          $err = "Isi Nama Server";
        }elseif(empty($alamatIP)){
          $err = "Isi Alamat Server";
        }elseif(empty($userFTP)){
          $err = "Isi User FTP";
        }elseif(empty($passwordFTP)){
          $err = "Isi Password FTP";
        }elseif(empty($ftpPort)){
          $err = "Isi PORT FTP";
        }
        if(empty($err)){
          $dataUpdate = array(
            'nama_server' => $namaServer,
            'alamat_ip' => $alamatIP,
            'user_ftp' => $userFTP,
            'password_ftp' => $passwordFTP,
            'port_ftp' => $ftpPort,
            'public_key' => $publicKey,
            'status' => $statusServer,
          );
          $query = $this->sqlUpdate($this->tableName,$dataUpdate,"id = '$idEdit'");
          $this->sqlQuery($query);
          $cek = $query;
        }
  		break;
  		}
      case 'Hapus':{
        for ($i=0; $i < sizeof($dashboard_cb) ; $i++) {
          $query = "delete from $this->tableName where id = '".$dashboard_cb[$i]."'";
          $this->sqlQuery($query);
        }
        $cek = $query;
        break;
      }
      default:{
        $content = "API NOT FOUND";
      break;
      }
	 }

    return json_encode(array ('cek'=>$cek, 'err'=>$err, 'content'=>$content));
  }
  function __construct(){
    if(!isset($_GET['API'])){
        if(empty($_GET['action'])){
          echo $this->pageShow();
        }else{
          if($_GET['action'] == 'new'){
            echo $this->pageShowNew();
          }else{
            echo $this->pageShowEdit($_GET['idEdit']);
          }
        }
    }else{
       echo $this->jsonGenerator();
    }
  }
  function loadScript(){
    return "
    <script type='text/javascript' src='js/dashboard.js'></script>
    <script src='plugins/contextMenu/contextMenu.min.js'></script>
    <link rel='stylesheet' type='text/css' href='plugins/contextMenu/contextMenu.min.css'>
    ";
  }

  function setMenuEdit(){
    // $setMenuEdit = "
    // <div id='header-nav-right'>
    // <a href='#' class='hdr-btn popover-button' title='Search' data-placement='bottom' data-id='#popover-search'>
    //   <i class='glyph-icon icon-search'></i>
    // </a>
    // <div class='hide' id='popover-search'>
    //   <div class='pad5A '>
    //       <div class='input-group'>
    //           <input type='text' class='form-control' id='filterCari' name='filterCari' onkeyup=$this->Prefix.setValueFilter(this) placeholder='Cari data'>
    //           <span class='input-group-btn' onclick=$this->Prefix.refreshList();>
    //               <a class='btn btn-primary' >Cari</a>
    //           </span>
    //       </div>
    //   </div>
    // </div>
    //
    //
    //
    // </div>
    //
    // ";
    return $setMenuEdit;
  }
  function pageContent(){
    $pageContent = "
    <div id='page-title'>
      <h2>Server Status</h2>
    </div>
    <div class='panel'>
      <div class='panel-body'>
          <div class='example-box-wrapper'>
                  ".$this->generateTable()."
          </div>
      </div>
    </div>

    ";
    return $pageContent;
  }

  function setKolomHeader(){
    $kolomHeader = "
    <thead>
      <tr>
          <th>No</th>
          <th>Nama Server</th>
          <th>Alamat IP</th>
          <th>Status</th>
          <th>WEB</th>
          <th>OS</th>
          <th>RAM</th>
          <th>DISK</th>
      </tr>
    </thead>";
    return $kolomHeader;
  }
  function setKolomData($no,$arrayData){
    foreach ($arrayData as $key => $value) {
        $$key = $value;
    }


    if($status == '1'){
      $statusServer = "LIVE";
    }elseif($status == '2'){
      $statusServer = "ERROR";
    }elseif($status == '3'){
      $statusServer = "DIE";
    }
    $rightClick = "
    <script>
    var menuOption = [
      {
        name: 'Status',
        img: 'assets/icons/status.png',
        title: 'Status',
        fun: function () {
            $this->Prefix.statusServer($id);
        }
      },
      {
          name: 'Reboot',
          img: 'assets/icons/reboot.png',
          title: 'Reboot',
          fun: function () {
              $this->Prefix.rebootServer('$id');
          }

      },
      {
          name: 'Off Website',
          img: 'assets/icons/shutdown.png',
          title: 'Off Website',
          fun: function () {
              $this->Prefix.killApache('$id');
          }

      },
      {
          name: 'On Website',
          img: 'assets/icons/on.png',
          title: 'On Website',
          fun: function () {
              $this->Prefix.lifeApache('$id');
          }

      },
      {
          name: 'Manage Server',
          img: 'assets/icons/server.png',
          title: 'Manage Server',
          fun: function () {
              $this->Prefix.manageServer('$id');
          }

      }
    ];
    var menuTrgr=$('#rightClick$id');
    menuTrgr.contextMenu(menuOption,{
         triggerOn :'contextmenu',
         mouseClick : 'right'
    });
    </script>
    ";
    //".$this->pingAddress($alamat_ip)."
    //".$this->apacheStatus($alamat_ip)."
    //".$this->osName($id)."
    //".$this->memorySize($id)."
    //".$this->diskSize($id)."
    $tableRow = "
    <tr class='$classRow' id ='rightClick$id' >
        <td>$no</td>
        <td>$nama_server</td>
        <td>$alamat_ip</td>
        <td><span id='statusPing$id'> </span></td>
        <td><span id='apacheStatus$id'> </span></td>
        <td align='right'><span id='osName$id'> </span></td>
        <td align='right'><span id='memorySize$id'> </span></td>
        <td align='right'><span id='diskSize$id'> </span></td>

    </tr>
    $rightClick
    ";
    return $tableRow;
  }
  function generateTable($kondisiTable){
    $no = 1;
    $getDataServer = $this->sqlQuery("select * from ref_server ".$kondisiTable." ");
    while ($dataServer = $this->sqlArray($getDataServer)) {
      $kolomData.= $this->setKolomData($no,$dataServer);
      $no++;
    }
    $htmlTable = "
      <form name='$this->formName' id='$this->formName'>
        <table class='table table-bordered table-striped table-condensed table-hover'>
            ".$this->setKolomHeader()."
            <tbody>
              $kolomData
            </tbody>
        </table>
        <input type='hidden' name='".$this->Prefix."_jmlcek' id='".$this->Prefix."_jmlcek' value='0'>
      </form>
    ";
    return $htmlTable;
  }
  function pingAddress($ip) {
      $pingresult = exec("/bin/ping -c2 -w2 $ip", $outcome, $status);
      if ($status==0) {
      $status = "LIVE";
      } else {
      $status = "DEAD";
      }
      $message = $status;
      return $message;
  }
  function apacheStatus($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    $rt = curl_exec($ch);
    $info = curl_getinfo($ch);
    $info = $info["http_code"];
    // if($info == 200 ){
    //   $status = "<span style='text-color:green;'>ACTIVE</span>";
    // }
    // if($info == 504 ){
    //   $status = "<span style='text-color:red;'>DIE</span>";
    // }
    // if($info == 500 ){
    //   $status = "<span style='text-color:green;'>ACTIVE</span>";
    // }
    // if($info == 302 ){
    //   $status = "<span style='text-color:green;'>ACTIVE</span>";
    // }
    // if($info == 404 ){
    //   $status = "<span style='text-color:green;'>ACTIVE</span>";
    // }
    $status = "DIE";
    if($info == 200 ){
      $status = "ACTIVE";
    }
    if($info == 504 ){
      $status = "DIE";
    }
    if($info == 500 ){
      $status = "ACTIVE";
    }
    if($info == 302 ){
      $status = "ACTIVE";
    }
    if($info == 404 ){
      $status = "ACTIVE";
    }
    return $status;
  }
  function killApache($id) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    return $this->sshCommand($sshConnection,"service apache2 stop");
  }
  function lifeApache($id) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    return $this->sshCommand($sshConnection,"service apache2 start");
  }
  function rebootServer($id) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    return $this->sshCommand($sshConnection,"reboot");
  }
  function memorySize($id) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    return $this->numberFormat($this->sshCommand($sshConnection,"echo $(awk '/^MemTotal:/{print $2}' /proc/meminfo)"))." Byte";
  }
  function diskSize($id) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    // return $this->numberFormat(str_replace("(","",$this->sshCommand($sshConnection,"dmesg | grep blocks | grep GB | grep -o -P '(?<=blocks:).*(?=GB)'")))." GB";
    return $this->numberFormat($this->sshCommand($sshConnection,"blockdev --getsize64 /dev/sda"))." Byte";
  }
  function osName($id) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    return $this->sshCommand($sshConnection,"cat /etc/issue");
  }
  function pageShowNew(){
    $pageShow = "
    ".$this->loadJSandCSS()."
        <body class='fixed-header'>
        <div id='loading'>
            <div class='spinner'>
                <div class='bounce1'></div>
                <div class='bounce2'></div>
                <div class='bounce3'></div>
            </div>
        </div>
        <div id='page-wrapper'>
        ".$this->emptyMenuBar()."
        ".$this->sidebar()."
        <div id='page-content-wrapper'>
            <div id='page-content'>
              <div class='container'>
                ".$this->formBaru()."
              </div>
            </div>
        </div>
      </div>
      </body>
      </html>
          ";

    return $pageShow;
  }
  function pageShowEdit($idEdit){
    $pageShow = "
    ".$this->loadJSandCSS()."
        <body class='fixed-header'>
        <div id='loading'>
            <div class='spinner'>
                <div class='bounce1'></div>
                <div class='bounce2'></div>
                <div class='bounce3'></div>
            </div>
        </div>
        <div id='page-wrapper'>
        ".$this->emptyMenuBar()."
        ".$this->sidebar()."
        <div id='page-content-wrapper'>
            <div id='page-content'>
              <div class='container'>
                ".$this->formEdit($idEdit)."
              </div>
            </div>
        </div>
      </div>
      </body>
      </html>
          ";

    return $pageShow;
  }



}
$dashboard = new dashboard();


 ?>
