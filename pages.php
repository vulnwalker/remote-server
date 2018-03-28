<?php
session_start();
include "base/config.php";
include "base/baseObject.php";
function checkLogin(){
  if(!isset($_SESSION['username'])){
    header('Location: index.php');
  }
}
$pages = $_GET['page'];
switch ($pages) {
  case 'refServer':{
    checkLogin();
    include "pages/refServer/refServer.php";
    break;
  }
  case 'fileManager':{
    checkLogin();
    include "pages/fileManager/fileManager.php";
    break;
  }
  case 'refRelease':{
    checkLogin();
    include "pages/refRelease/refRelease.php";
    break;
  }
  case 'detailRelease':{
    checkLogin();
    include "pages/refRelease/detailRelease.php";
    break;
  }
  case 'manageServer':{
    checkLogin();
    include "pages/manageServer/manageServer.php";
    break;
  }
  case 'historyBackup':{
    checkLogin();
    include "pages/historyBackup/historyBackup.php";
    break;
  }
  case 'refDisk':{
    checkLogin();
    include "pages/refServer/refDisk.php";
    break;
  }
  case 'bandingLocalFile':{
    checkLogin();
    include "pages/bandingLocalFile/bandingLocalFile.php";
    break;
  }
  case 'refDirCheck':{
    checkLogin();
    include "pages/bandingLocalFile/refDirCheck.php";
    break;
  }
  case 'bandingLocalFile':{
    checkLogin();
    include "pages/bandingLocalFile/bandingLocalFile.php";
    break;
  }
  case 'bandingServerFile':{
    checkLogin();
    include "pages/bandingServerFile/bandingServerFile.php";
    break;
  }
  case 'bandingDatabaseLocal':{
    checkLogin();
    include "pages/bandingDatabaseLocal/bandingDatabaseLocal.php";
    break;
  }
  case 'bandingDatabaseServer':{
    checkLogin();
    include "pages/bandingDatabaseServer/bandingDatabaseServer.php";
    break;
  }
  case 'bugReport':{
    checkLogin();
    include "pages/bugReport/bugReport.php";
    break;
  }
  case 'logout':{
    $_SESSION['username'] = '';
    unset($_SESSION['username']);
    session_destroy();
    checkLogin();
    break;
  }
  default:{
    checkLogin();
    include "pages/dashboard.php";
    break;
  break;
  }
}



 ?>
