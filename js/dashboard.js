var dashboard = new baseObject2({
	prefix : 'dashboard',
	url : 'pages.php?page=dashboard',
	formName : 'dashboardForm',

	getJumlahChecked: function() {
    var jmldata= document.getElementById( this.prefix+'_jmlcek' ).value;
    for(var i=0; i < jmldata; i++){
      var box = document.getElementById( this.prefix+'_cb' + i);
      if( box.checked){
        break;
      }
    }
    var err = "";
    if(jmldata == 0){
        err = "Pilih data";
    }else if(jmldata > 1){
        err = "Pilih hanya satu data";
    }
    return err;
	},
	checkSemua: function(jumlahData,fldName,elHeaderChecked,elJmlCek,fuckYeah) {
    if (!fldName) {
      fldName = 'cb';
    }
    if (!elHeaderChecked) {
      elHeaderChecked = 'toggle';
    }
    var c = fuckYeah.checked;
    var n2 = 0;
    for (i=0; i < jumlahData ; i++) {
     cb = document.getElementById(fldName+i);
     if (cb) {
       cb.checked = c;
       n2++;
     }
    }
    if (c) {
     document.getElementById(elJmlCek).value = n2;
    } else {
     document.getElementById(elJmlCek).value = 0;
    }
	},
	thisChecked: function(idCheckbox,elJmlCek) {
    var c = document.getElementById(idCheckbox).checked;
    var jumlahCheck = parseInt($("#"+elJmlCek).val());
    if(c){
        document.getElementById(elJmlCek).value = jumlahCheck + 1;
    }else{
        document.getElementById(elJmlCek).value = jumlahCheck - 1;
    }
	},
	formatCurrency: function(num) {
		num = num.toString().replace(/\$|\,/g,'');
		if(isNaN(num))
		num = "0";
		sign = (num == (num = Math.abs(num)));
		num = Math.floor(num*100+0.50000000001);
		cents = num%100;
		num = Math.floor(num/100).toString();
		if(cents<10)
		cents = "0" + cents;
		for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
		num = num.substring(0,num.length-(4*i+3))+'.'+
		num.substring(num.length-(4*i+3));
		return (((sign)?'':'-') + '' + num + ',' + cents);
	},
	setValueFilter: function(a){
		$("#filterCari").val(a.value);
	},
	refreshList: function(){
		$.ajax({
			type:'POST',
			data : {
								filterCari : $('#filterCari').val()
							},
			url: this.url+'&API=refreshList',
			success: function(data) {
				var resp = eval('(' + data + ')');
				if(resp.err==''){
					$("#dashboardForm").html(resp.content.tableContent);
				}else{
					dashboard.errorAlert(resp.err);
				}
			}
		});
	},
	Baru: function(){
		window.location = this.url+"&action=new";
	},
	Edit: function(){
	  var errMsg = dashboard.getJumlahChecked();
		urlEdit = this.url;
	  if(errMsg == ''){
	    $.ajax({
	      type:'POST',
	      data : $("#"+this.formName).serialize(),
	      url: this.url+'&API=Edit',
	      success: function(data) {
	        var resp = eval('(' + data + ')');
	        if(resp.err==''){
	          window.location = urlEdit+"&action=edit&idEdit="+resp.content.idEdit;
	        }else{
	          dashboard.errorAlert(resp.err);
	        }
	      }
	    });
	  }else{
	     dashboard.errorAlert(errMsg);
	  }
	},
	Hapus: function(){
  var errMsg = this.getJumlahChecked();
  if(errMsg == '' || errMsg=='Pilih hanya satu data'){
    swal({
          title: "Hapus Data ?",
          text: "",
          type: "warning",
          showCancelButton: true,
          confirmButtonClass: "btn-danger",
          confirmButtonText: "Ya",
          cancelButtonText: "Tidak",
          closeOnConfirm: false
        },
        function(){
          $.ajax({
              type:'POST',
							data : $("#"+dashboard.formName).serialize(),
				      url: dashboard.url+'&API=Hapus',
              success: function(data) {
                var resp = eval('(' + data + ')');
                if(resp.err==''){
                  dashboard.suksesAlert("Data Terhapus",dashboard.homePage);
                }else{
                  dashboard.errorAlert(resp.err);
                }
              }
            });
        });
    }else{
      dashboard.errorAlert(errMsg);
    }
  },
	homePage: function(){
		window.location = dashboard.url;
	},
	closeMassage: function(){
		swal.close();
	},
  saveNew: function(){
	  // swal({
	  //       title: "Simpan Data ?",
	  //       text: "",
	  //       type: "info",
	  //       confirmButtonText: "Ya",
	  //       cancelButtonText: "Tidak",
	  //       showCancelButton: true,
	  //       closeOnConfirm: false,
	  //       showLoaderOnConfirm: true
	  //     }, function () {
	        $.ajax({
	          type:'POST',
	          data : $("#"+this.formName+"_new").serialize(),
	          url: this.url+'&API=saveNew',
	            success: function(data) {
	            var resp = eval('(' + data + ')');
	              if(resp.err==''){
	                dashboard.suksesAlert("Data Tersimpan",dashboard.homePage);
	              }else{
	                dashboard.errorAlert(resp.err);
	              }
	            }
	        });
	      // });
		},
  rebootServer: function(id){
	  swal({
	        title: "Reboot Server ?",
	        text: "",
	        type: "info",
	        confirmButtonText: "Ya",
	        cancelButtonText: "Tidak",
	        showCancelButton: true,
	        closeOnConfirm: false,
	        showLoaderOnConfirm: true
	      }, function () {
	        $.ajax({
	          type:'POST',
	          data: {id : id},
	          url: dashboard.url+'&API=rebootServer',
	            success: function(data) {
	            var resp = eval('(' + data + ')');
	              if(resp.err==''){
	                dashboard.suksesAlert("Reboot Sukses",dashboard.homePage);
	              }else{
	                dashboard.errorAlert(resp.err);
	              }
	            }
	        });
	      });
		},
  statusServer: function(id){
	  swal({
	        title: "Ambil Info Server ?",
	        text: "",
	        type: "info",
	        confirmButtonText: "Ya",
	        cancelButtonText: "Tidak",
	        showCancelButton: true,
	        closeOnConfirm: false,
	        showLoaderOnConfirm: true
	      }, function () {
	        $.ajax({
	          type:'POST',
	          data: {id : id},
	          url: dashboard.url+'&API=statusServer',
	            success: function(data) {
	            var resp = eval('(' + data + ')');
	              if(resp.err==''){
	                dashboard.suksesAlert("Sukses Ambil Info Server",dashboard.closeMassage);
									$("#statusPing"+id).text(resp.content.statusPing);
									$("#apacheStatus"+id).text(resp.content.apacheStatus);
									$("#osName"+id).text(resp.content.osName);
									$("#memorySize"+id).text(resp.content.memorySize);
									$("#diskSize"+id).text(resp.content.diskSize);

	              }else{
	                dashboard.errorAlert(resp.err);
	              }
	            }
	        });
	      });
		},
  lifeApache: function(id){
	  swal({
	        title: "Nyalakan Website ?",
	        text: "",
	        type: "info",
	        confirmButtonText: "Ya",
	        cancelButtonText: "Tidak",
	        showCancelButton: true,
	        closeOnConfirm: false,
	        showLoaderOnConfirm: true
	      }, function () {
	        $.ajax({
	          type:'POST',
	          data: {id : id},
	          url: dashboard.url+'&API=lifeApache',
	            success: function(data) {
	            var resp = eval('(' + data + ')');
	              if(resp.err==''){
	                dashboard.suksesAlert("Website dinyalakan",dashboard.homePage);
	              }else{
	                dashboard.errorAlert(resp.err);
	              }
	            }
	        });
	      });
		},
  killApache: function(id){
	  swal({
	        title: "Matikan Website ?",
	        text: "",
	        type: "info",
	        confirmButtonText: "Ya",
	        cancelButtonText: "Tidak",
	        showCancelButton: true,
	        closeOnConfirm: false,
	        showLoaderOnConfirm: true
	      }, function () {
	        $.ajax({
	          type:'POST',
	          data: {id : id},
	          url: dashboard.url+'&API=killApache',
	            success: function(data) {
	            var resp = eval('(' + data + ')');
	              if(resp.err==''){
	                dashboard.suksesAlert("Website dimatikan",dashboard.homePage);
	              }else{
	                dashboard.errorAlert(resp.err);
	              }
	            }
	        });
	      });
		},
  manageServer: function(id){
	      window.location = "pages.php?page=manageServer&id="+id;
		},
  saveEdit: function(idEdit){
	  // swal({
	  //       title: "Simpan Data ?",
	  //       text: "",
	  //       type: "info",
	  //       confirmButtonText: "Ya",
	  //       cancelButtonText: "Tidak",
	  //       showCancelButton: true,
	  //       closeOnConfirm: false,
	  //       showLoaderOnConfirm: true
	  //     }, function () {
	        $.ajax({
	          type:'POST',
	          data : $("#"+this.formName+"_edit").serialize()+"&idEdit="+idEdit,
	          url: this.url+'&API=saveEdit',
	            success: function(data) {
	            var resp = eval('(' + data + ')');
	              if(resp.err==''){
	                dashboard.suksesAlert("Data Tersimpan",dashboard.homePage);
	              }else{
	                dashboard.errorAlert(resp.err);
	              }
	            }
	        });
	      // });
		},



});
