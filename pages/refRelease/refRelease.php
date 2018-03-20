<?php
class refRelease extends baseObject{
  var $Prefix = "refRelease";
  var $formName = "refReleaseForm";
  var $tableName = "ref_release";

  function jsonGenerator(){
    $cek = ''; $err=''; $content=''; $json=TRUE;
    foreach ($_POST as $key => $value) {
       $$key = $value;
    }
	  switch($_GET['API']){
      case 'Release':{
        $getData = $this->sqlArray($this->sqlQuery("select * from $this->tableName where id = '".$refRelease_cb[0]."'"));
        $formCaption = "Push Release To Server";
        $comboRelease = $this->cmbQuery("cmbRelease","","select id,nama_release from ref_release","onchange=$this->Prefix.releaseChanged() class='form-control'","-- RELEASE --");
        $content="
        <div class='modal fade bs-example-modal-lg' id='modalRelease' tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel' aria-hidden='true'>
          <form name='".$this->formName."_release' id='".$this->formName."_release'>
            <div class='modal-dialog modal-lg'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                        <h4 class='modal-title'>$formCaption</h4>
                    </div>
                    <div class='modal-body'>

                      <div class='form-group'>
                        <div class='row'>
                        <label class='col-sm-3 control-label'>Server Tujuan</label>
                          <div class='col-sm-9'>
                              ".$this->cmbQueryEmpty("listServer[]",$listServer,"select id,nama_server from ref_server","multiple class='multi-select'")."
                          </div>
                        </div>
                      </div>
                      <div class='form-group'>
                        <div class='row'>
                          <label class='col-sm-3 control-label' style='margin-top:6px;'>Nama Release</label>
                          <div class='col-sm-9'>
                            <input type='text' class='form-control' name='namaRelease' id='namaRelease' value='".$getData['nama_release']."' readonly>
                          </div>
                        </div>
                      </div>
                      <div class='form-group'>
                        <div class='row'>
                          <label class='col-sm-3 control-label' style='margin-top:6px;'>Nama Database</label>
                          <div class='col-sm-9'>
                            <input type='text' class='form-control' value='".$getData['nama_database']."' readonly>
                          </div>
                        </div>
                      </div>
                      <div class='form-group'>
                        <div class='row'>
                          <label class='col-sm-3 control-label' style='margin-top:6px;'>Sql File</label>
                          <div class='col-sm-9'>
                            <input type='text' class='form-control' value='".$getData['mysql_file']."' readonly>
                          </div>
                        </div>
                      </div>
                      <div class='form-group'>
                        <div class='row'>
                          <label class='col-sm-3 control-label' style='margin-top:6px;'>Option</label>
                          <div class='col-sm-2'>
                            <input type='checkbox' name='optionCreateDatabase' id='optionCreateDatabase' value='optionCreateDatabase'> Create Database
                          </div>
                          <div class='col-sm-2'>
                            <input type='checkbox' name='optionExecuteSql' id='optionExecuteSql' value='databaseOnly'> Execute Sql File
                          </div>
                          <div class='col-sm-2'>
                            <input type='checkbox' name='optionPushFile' id='optionPushFile' value='optionPushFile'> Push Files
                          </div>
                        </div>
                      </div>

                    </div>
                    <div class='modal-footer'>
                        <input type='hidden' name='idRelease' id='idRelease' value='".$refRelease_cb[0]."'>
                        <button type='button' class='btn btn-primary' onclick=$this->Prefix.executeRelease();>Push</button>
                        <button type='button' class='btn btn-default' data-dismiss='modal' id='closeModal'>Batal</button>
                    </div>
                </div>
            </div>
          </form>
        </div>";
  		break;
  		}
      case 'pushRelease':{
            $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '".$listServer[$urutanKe - 1]."'"));
            $getDataRelease = $this->sqlArray($this->sqlQuery("select * from ref_release where id = '$idRelease'"));
            $databasePassword = $getDataServer["password_mysql"];
            $userMysql = $getDataServer["user_mysql"];
            $databaseName = $getDataRelease['nama_database'];
            $sqlLocation = $getDataRelease['mysql_file'];
            $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
            $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
            if(!empty($optionPushFile)){
              $this->xSFTP($getDataRelease['directory_location'],$getDataRelease['directory_location'],$sshConnection);
            }
            if(!empty($optionCreateDatabase)){
              $createDatabase = "mysql -u".$userMysql." -p".$databasePassword." -e 'CREATE DATABASE ".$databaseName." CHARACTER SET latin1 COLLATE latin1_general_ci '";
              $this->sshCommand($sshConnection,$createDatabase);
            }
            if(!empty($optionExecuteSql)){
              $this->xSFTP($getDataRelease['mysql_file'],$getDataRelease['mysql_file'],$sshConnection);
              $executeSqlFile = "mysql -u".$userMysql." -p".$databasePassword." $databaseName < $sqlLocation";
              $this->sshCommand($sshConnection,$executeSqlFile);
            }
            if($urutanKe == sizeof($listServer)){
              $status = "OK";
            }
            $cek = $executeSqlFile;
            $content = array('urutanKe' => $urutanKe + 1, 'status' => $status);
  		break;
  		}
      case 'executeRelease':{
            if(sizeof($listServer) == 0){
              $err = "Pilih Server Tujuan";
            }
            $content = array('urutanKe' =>  1);
  		break;
  		}
      case 'refreshList':{
        if(!empty($filterCari)){
          $arrKondisi[] = "nama_release like '%$filterCari%' ";
          $arrKondisi[] = "tanggal_release like '%".$this->generateDate($filterCari)."%' ";
          $arrKondisi[] = "directory_location like '%$filterCari%' ";
          $kondisi = join(" or ",$arrKondisi);
          $kondisi = " where $kondisi ";
        }
        $cek = "select * from $this->tableName $kondisi";
        $content=array('tableContent' => $this->generateTable($kondisi));
  		break;
  		}
      case 'Edit':{
        $content = array("idEdit" => $refRelease_cb[0]);
  		break;
  		}
      case 'saveNew':{
  			if(empty($namaRelease)){
          $err = "Isi Nama Release";
        }elseif(empty($tanggalRelease)){
          $err = "Isi Tanggal Release";
        }elseif(empty($directoryLocation)){
          $err = "Isi Directory Location";
        }
        if(empty($err)){
          $dataInsert = array(
            'nama_release' => $namaRelease,
            'tanggal_release' => $this->generateDate($tanggalRelease),
            'directory_location' => $directoryLocation,
            'nama_database' => $namaDatabase,
            'last_modified' => date("Y-m-d H:i:s"),
          );
          $query = $this->sqlInsert($this->tableName,$dataInsert);
          $this->sqlQuery($query);
          mkdir($directoryLocation);
          $cek = $query;
        }
  		break;
  		}
      case 'saveEdit':{
        if(empty($namaRelease)){
          $err = "Isi Nama Release";
        }elseif(empty($tanggalRelease)){
          $err = "Isi Tanggal Release";
        }elseif(empty($directoryLocation)){
          $err = "Isi Directory Location";
        }
        if(empty($err)){
          $getDataSebelumnya = $this->sqlArray($this->sqlQuery("select * from $this->tableName where id ='$idEdit'"));
          $dataUpdate = array(
            'nama_release' => $namaRelease,
            'tanggal_release' => $this->generateDate($tanggalRelease),
            'directory_location' => $directoryLocation,
            'nama_database' => $namaDatabase,
            'last_modified' => date("Y-m-d H:i:s"),
          );
          $query = $this->sqlUpdate($this->tableName,$dataUpdate,"id = '$idEdit'");
          $this->sqlQuery($query);
          rename($getDataSebelumnya['directory_location'],$directoryLocation);
          $cek = $query;
        }
  		break;
  		}
      case 'Hapus':{
        for ($i=0; $i < sizeof($refRelease_cb) ; $i++) {
          $query = "delete from $this->tableName where id = '".$refRelease_cb[$i]."'";
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
    <script type='text/javascript' src='js/refRelease/refRelease.js'></script>
    <script type='text/javascript' src='assets/widgets/datepicker/datepicker.js'></script>
    <script type='text/javascript' src='assets/widgets/multi-select/multiselect.js'></script>
    <script>
      $(document).ready(function() {
            $('.bootstrap-datepicker').bsdatepicker({
                format: 'dd-mm-yyyy'
            });
      });
    </script>

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

    <a class='header-btn' style='cursor:pointer;'  onclick=$this->Prefix.Release(); title='Push To Release'>
      <i class='glyph-icon icon-rss'></i>
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
      <h2>Referensi Release</h2>
    </div>
    <div class='panel'>
      <div class='panel-body'>
          <div class='example-box-wrapper'>
                  ".$this->generateTable()."
          </div>
      </div>
      <div id='tempatModal'> </div>
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
          <th>Nama Release</th>
          <th>Tanggal Release</th>
          <th>Directory Location</th>
          <th>Nama Database</th>
          <th>SQL File</th>
          <th>Last Modified</th>
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
        <td><span style='cursor:pointer;' onclick=$this->Prefix.detailRelease($id)>$nama_release</span?</td>
        <td>".$this->generateDate($tanggal_release)."</td>
        <td>$directory_location</td>
        <td>$nama_database</td>
        <td>$mysql_file</td>
        <td>$last_modified</td>
    </tr>
    ";
    return $tableRow;
  }
  function generateTable($kondisiTable){
    $no = 1;
    $getDataServer = $this->sqlQuery("select * from ref_release ".$kondisiTable);
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
      <h2>REFERENSI RELEASE BARU</h2>
    </div>
    <div class='panel'>
        <div class='panel-body'>
            <div class='example-box-wrapper'>
                <form class='form-horizontal bordered-row' name='".$this->formName."_new' id='".$this->formName."_new'>
                    <div class='form-group'>
                        <label class='col-sm-1 control-label'>Nama Release</label>
                        <div class='col-sm-11'>
                            <input type='text' class='form-control'  placeholder='Nama Release' id='namaRelease' name='namaRelease'>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-sm-1 control-label'>Tanggal Relase</label>
                        <div class='col-sm-11'>
                            <input type='text' class='bootstrap-datepicker form-control'  placeholder='Tanggal Release' id='tanggalRelease' name='tanggalRelease'>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-sm-1 control-label'>Directory Location</label>
                        <div class='col-sm-11'>
                            <input type='text' class='form-control'  placeholder='Directory Location' id='directoryLocation' name='directoryLocation'>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-sm-1 control-label'>Nama Database</label>
                        <div class='col-sm-11'>
                            <input type='text' class='form-control'  placeholder='Nama Database' id='namaDatabase' name='namaDatabase'>
                        </div>
                    </div>
                    <div class='form-group' style='float:right;'>
                        <div class='col-sm-12' >
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
      <h2>EDIT RELEASE</h2>
    </div>
    <div class='panel'>
        <div class='panel-body'>
            <div class='example-box-wrapper'>
                <form class='form-horizontal bordered-row' name='".$this->formName."_edit' id='".$this->formName."_edit'>
                  <div class='form-group'>
                      <label class='col-sm-1 control-label'>Nama Release</label>
                      <div class='col-sm-11'>
                          <input type='text' class='form-control'  placeholder='Nama Release' id='namaRelease' name='namaRelease' value='$nama_release'>
                      </div>
                  </div>
                  <div class='form-group'>
                      <label class='col-sm-1 control-label'>Tanggal Relase</label>
                      <div class='col-sm-11'>
                          <input type='text' class='bootstrap-datepicker form-control'  placeholder='Tanggal Release' id='tanggalRelease' name='tanggalRelease' value='".$this->generateDate($tanggal_release)."'>
                      </div>
                  </div>
                  <div class='form-group'>
                      <label class='col-sm-1 control-label'>Directory Location</label>
                      <div class='col-sm-11'>
                          <input type='text' class='form-control'  placeholder='Directory Location' id='directoryLocation' name='directoryLocation' value='$directory_location'>
                      </div>
                  </div>
                  <div class='form-group'>
                      <label class='col-sm-1 control-label'>Nama Database</label>
                      <div class='col-sm-11'>
                          <input type='text' class='form-control'  placeholder='Nama Database' id='namaDatabase' name='namaDatabase' value='$nama_database'>
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
$refRelease = new refRelease();


 ?>
