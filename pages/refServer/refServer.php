<?php
class refServer extends baseObject{
  var $Prefix = "refServer";
  var $formName = "refServerForm";
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
          $arrKondisi[] = "alias like '%$filterCari%' ";
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
      case 'Edit':{
        $content = array("idEdit" => $refServer_cb[0]);
  		break;
  		}
      case 'ManageDisk':{
        $content = array("idEdit" => $refServer_cb[0]);
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
        }elseif(empty($userMysql)){
          $err = "Isi User FTP";
        }elseif(empty($passwordMysql)){
          $err = "Isi Password FTP";
        }elseif(empty($portMysql)){
          $err = "Isi PORT FTP";
        }
        if(empty($err)){
          $dataInsert = array(
            'nama_server' => $namaServer,
            'alias' => $aliasServer,
            'alamat_ip' => $alamatIP,
            'user_ftp' => $userFTP,
            'password_ftp' => $passwordFTP,
            'port_ftp' => $ftpPort,
            'user_mysql' => $userMysql,
            'password_mysql' => $passwordMysql,
            'port_mysql' => $portMysql,
            // 'status' => $statusServer,
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
        }elseif(empty($userMysql)){
          $err = "Isi User FTP";
        }elseif(empty($passwordMysql)){
          $err = "Isi Password FTP";
        }elseif(empty($portMysql)){
          $err = "Isi PORT FTP";
        }
        if(empty($err)){
          $dataUpdate = array(
            'nama_server' => $namaServer,
            'alias' => $aliasServer,
            'alamat_ip' => $alamatIP,
            'user_ftp' => $userFTP,
            'password_ftp' => $passwordFTP,
            'port_ftp' => $ftpPort,
            'user_mysql' => $userMysql,
            'password_mysql' => $passwordMysql,
            'port_mysql' => $portMysql,
            // 'status' => $statusServer,
          );
          $query = $this->sqlUpdate($this->tableName,$dataUpdate,"id = '$idEdit'");
          $this->sqlQuery($query);
          $cek = $query;
        }
  		break;
  		}
      case 'Hapus':{
        for ($i=0; $i < sizeof($refServer_cb) ; $i++) {
          $query = "delete from $this->tableName where id = '".$refServer_cb[$i]."'";
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
    <script type='text/javascript' src='js/refServer/refServer.js'></script>
    ";
  }
  
  function setMenuEdit(){
    $setMenuEdit = "
    <div id='header-nav-right'>
    <a href='#' class='hdr-btn popover-button' title='Search' data-placement='bottom' data-id='#popover-search'>
      <i class='glyph-icon icon-search'></i>
    </a>
    <div class='hide' id='popover-search'>
      <div class='pad5A '>
          <div class='input-group'>
              <input type='text' class='form-control' id='filterCari' name='filterCari' onkeyup=$this->Prefix.setValueFilter(this) placeholder='Cari data'>
              <span class='input-group-btn' onclick=$this->Prefix.refreshList();>
                  <a class='btn btn-primary' >Cari</a>
              </span>
          </div>
      </div>
    </div>

    <a class='header-btn' style='cursor:pointer;' id='logout-btn' onclick=$this->Prefix.ManageDisk(); title='Manage Disk'>
      <i class='glyph-icon icon-inbox'></i>
    </a>
    <a class='header-btn' style='cursor:pointer;' id='logout-btn' onclick=$this->Prefix.Baru(); title='Baru'>
      <i class='glyph-icon icon-plus'></i>
    </a>
    <a class='header-btn' style='cursor:pointer;' id='logout-btn' onclick=$this->Prefix.Edit(); title='Edit'>
      <i class='glyph-icon icon-pencil'></i>
    </a>
    <a class='header-btn' style='cursor:pointer;' id='logout-btn' onclick=$this->Prefix.Hapus(); title='Hapus'>
      <i class='glyph-icon icon-trash'></i>
    </a>

    </div>

    ";
    return $setMenuEdit;
  }
  function pageContent(){
    $pageContent = "
    <div id='page-title'>
      <h2>Referensi Server</h2>
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
          <th style='text-align:center;'>".$this->checkAll(25,$this->Prefix)."</th>
          <th>Nama Server</th>
          <th>Alias</th>
          <th>Alamat IP</th>
          <th>User FTP</th>
          <th>Password FTP</th>
          <th>Port FTP</th>
          <th>User Mysql</th>
          <th>Password Mysql</th>
          <th>Port Mysql</th>
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
    $tableRow = "
    <tr class='$classRow'>
        <td>$no</td>
        <td style='text-align:center;'>".$this->setCekBox($no - 1,$id,$this->Prefix)."</td>
        <td>$nama_server</td>
        <td>$alias</td>
        <td>$alamat_ip</td>
        <td>$user_ftp</td>
        <td>$password_ftp</td>
        <td>$port_ftp</td>
        <td>$user_mysql</td>
        <td>$password_mysql</td>
        <td>$port_mysql</td>
    </tr>
    ";
    return $tableRow;
  }
  function generateTable($kondisiTable){
    $no = 1;
    $getDataServer = $this->sqlQuery("select * from ref_server ".$kondisiTable);
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

  function formBaru(){
    $arrayStatus = array(
      array('1','LIVE'),
      array('2','ERROR'),
      array('3','DIE'),
    );
    $comboStatus = $this->cmbArray("statusServer","",$arrayStatus,"-- STATUS --","class='form-control' ");
    $formBaru = "
    <div id='page-title'>
      <h2>Referensi Server Baru</h2>
    </div>
    <div class='panel'>
        <div class='panel-body'>
            <div class='example-box-wrapper'>
                <form class='form-horizontal bordered-row' name='".$this->formName."_new' id='".$this->formName."_new'>
                    <div class='form-group'>
                        <label class='col-sm-1 control-label'>Nama Server</label>
                        <div class='col-sm-11'>
                            <input type='text' class='form-control'  placeholder='Nama Server' id='namaServer' name='namaServer'>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-sm-1 control-label'>Nama Alias</label>
                        <div class='col-sm-11'>
                            <input type='text' class='form-control'  placeholder='Nama Alias' id='aliasServer' name='aliasServer'>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-sm-1 control-label'>Alamat IP</label>
                        <div class='col-sm-11'>
                            <input type='text' class='form-control'  placeholder='Alamat IP' id='alamatIP' name='alamatIP'>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-sm-1 control-label'>User FTP</label>
                        <div class='col-sm-11'>
                            <input type='text' class='form-control'  placeholder='User FTP' id='userFTP' name='userFTP'>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-sm-1 control-label'>Password FTP</label>
                        <div class='col-sm-11'>
                            <input type='text' class='form-control'  placeholder='Password FTP' id='passwordFTP' name='passwordFTP'>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-sm-1 control-label'>FTP PORT</label>
                        <div class='col-sm-11'>
                            <input type='text' class='form-control'  placeholder='PORT FTP' id='ftpPort' name='ftpPort'>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-sm-1 control-label'>USER MYSQL</label>
                        <div class='col-sm-11'>
                            <input type='text' class='form-control'  placeholder='USER MYSQL' id='userMysql' name='userMysql'>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-sm-1 control-label'>PASSWORD MYSQL</label>
                        <div class='col-sm-11'>
                            <input type='text' class='form-control'  placeholder='PASSWORD MYSQL' id='passwordMysql' name='passwordMysql'>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-sm-1 control-label'>PORT MYSQL</label>
                        <div class='col-sm-11'>
                            <input type='text' class='form-control'  placeholder='POT MYSQL' id='portMysql' name='portMysql'>
                        </div>
                    </div>
                    <div class='form-group' style='float:right;'>
                        <div class='col-sm-12'>
                        <button type='button' onclick=$this->Prefix.saveNew(); class='btn btn-alt btn-hover btn-success'>
                            <span>Simpan</span>
                            <i class='glyph-icon icon-save'></i>
                        </button>
                        <button type='button' onclick=$this->Prefix.homePage(); class='btn btn-alt btn-hover btn-danger'>
                            <span>Batal</span>
                            <i class='glyph-icon icon-times'></i>
                        </button>
                        </div>
                    </div>



                </form>
            </div>
        </div>
    </div>
    ";
    return $formBaru;
  }
  function formEdit($idEdit){
    $getData = $this->sqlArray($this->sqlQuery("select * from $this->tableName where id='$idEdit'"));
    foreach ($getData as $key => $value) {
       $$key = $value;
    }
    $arrayStatus = array(
      array('1','LIVE'),
      array('2','ERROR'),
      array('3','DIE'),
    );
    $comboStatus = $this->cmbArray("statusServer",$status,$arrayStatus,"-- STATUS --","class='form-control' ");
    $formEdit = "
    <div id='page-title'>
      <h2>Referensi Server Baru</h2>
    </div>
    <div class='panel'>
        <div class='panel-body'>
            <div class='example-box-wrapper'>
                <form class='form-horizontal bordered-row' name='".$this->formName."_edit' id='".$this->formName."_edit'>
                    <div class='form-group'>
                        <label class='col-sm-1 control-label'>Nama Server</label>
                        <div class='col-sm-11'>
                            <input type='text' class='form-control'  placeholder='Nama Server' id='namaServer' name='namaServer' value='$nama_server'>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-sm-1 control-label'>Nama Alias</label>
                        <div class='col-sm-11'>
                            <input type='text' class='form-control'  placeholder='Nama Alias' id='aliasServer' name='aliasServer' value='$alias'>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-sm-1 control-label'>Alamat IP</label>
                        <div class='col-sm-11'>
                            <input type='text' class='form-control'  placeholder='Alamat IP' id='alamatIP' name='alamatIP' value='$alamat_ip'>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-sm-1 control-label'>User FTP</label>
                        <div class='col-sm-11'>
                            <input type='text' class='form-control'  placeholder='User FTP' id='userFTP' name='userFTP' value='$user_ftp'>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-sm-1 control-label'>Password FTP</label>
                        <div class='col-sm-11'>
                            <input type='text' class='form-control'  placeholder='Password FTP' id='passwordFTP' name='passwordFTP' value='$password_ftp'>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-sm-1 control-label'>FTP PORT</label>
                        <div class='col-sm-11'>
                            <input type='text' class='form-control'  placeholder='PORT FTP' id='ftpPort' name='ftpPort' value='$port_ftp'>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-sm-1 control-label'>USER MYSQL</label>
                        <div class='col-sm-11'>
                            <input type='text' class='form-control'  placeholder='USER MYSQL' id='userMysql' name='userMysql' value='$user_mysql'>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-sm-1 control-label'>PASSWORD MYSQL</label>
                        <div class='col-sm-11'>
                            <input type='text' class='form-control'  placeholder='PASSWORD MYSQL' id='passwordMysql' name='passwordMysql' value='$password_mysql'>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-sm-1 control-label'>PORT MYSQL</label>
                        <div class='col-sm-11'>
                            <input type='text' class='form-control'  placeholder='POT MYSQL' id='portMysql' name='portMysql' value='$port_mysql'>
                        </div>
                    </div>
                    <div class='form-group' style='float:right;'>
                        <div class='col-sm-12'>
                        <button type='button' onclick=$this->Prefix.saveEdit($idEdit); class='btn btn-alt btn-hover btn-success'>
                            <span>Simpan</span>
                            <i class='glyph-icon icon-save'></i>
                        </button>
                        <button type='button' onclick=$this->Prefix.homePage(); class='btn btn-alt btn-hover btn-danger'>
                            <span>Batal</span>
                            <i class='glyph-icon icon-times'></i>
                        </button>
                        </div>
                    </div>



                </form>
            </div>
        </div>
    </div>
    ";
    return $formEdit;
  }


}
$refServer = new refServer();


 ?>
