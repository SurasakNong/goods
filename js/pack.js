
function show_pack(){ //========================== แสดงค้นหา และปุ่มเพิ่ม หมวดรายการ
    var html = `
  <div class="container animate__animated animate__fadeIn">
    <div class="row">                
        <div class="col-lg-10 mx-auto mt-1">
        <h4 style="color:#ffff; text-align: center; text-shadow: 2px 2px #542305, 3px 2px #542305; background-color:#af480a; padding: 8px 0; border-radius: 20px 20px 0 0;">บันทึกการบรรจุ</h4>
            <form id="fmrec_pack">
                <div class="row">    
                    <div class="col-md-6 mb-2">
                        <div class="input-group">
                            <div class="input-group-text" style="width: 60px; background-color: #f9d0bf;">วันที่</div>
                            <input type="text" class="form-control" name="packdate" id="picker_pack">
                        </div>
                    </div>    
                    <div class="col-md-6 mb-2">
                        <div class="input-group">
                            <div class="input-group-text" style="width: 60px; background-color: #f9d0bf;">เลขบิล</div>
                            <input type="text" class="form-control" name="packbill" id="packbill" maxlength="25">      
                        </div>     
                    </div> 
                </div> 
                <div class="row"> 
                    <div class="col-md-6 mb-2">
                        <div class="input-group">
                            <div class="input-group-text" style="width: 60px; background-color: #f9d0bf;">ผู้บรรจุ</div>
                            <select class="form-select" name="packerid" id="packerid"></select>   
                        </div>     
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="input-group">
                            <input type="text" id="search_pack" name="search_pack" class="form-control" placeholder="คำค้นหา.." aria-label="Search" aria-describedby="button-search">
                            <button class="btn btn-success" type="button" id="bt_search_pack" name="bt_search_pack" title="ค้นหา"><i class="fas fa-search"></i></button>
                            <button class="btn btn-primary ms-2" type="button" id="bt_add_bag" name="bt_add_bag" title="เพิ่มรายการบรรจุ"><i class="fas fa-plus"></i></button>
                        </div> 
                        
                    </div>
                </div>
            </form>
        </div>
    </div>       
    <div class="row mt-2">  
        <div class="col-lg-10 mx-auto" id="table_pack"></div>
    </div>


    <div id="edit_data">
        <form id="fmedit_data">
            <div class="input-group">
            <input type="hidden" name="id_data" id="id_data">
            <input type="hidden" name="head_data" id="head_data">
                <input type="number" id="ed_data" name="ed_data" class="form-control" step="1" required>
                <button class="btn btn-success" type="submit" id="edit_data_bt" name="edit_data_bt" title="บันทึก"><i class="fas fa-check"></i></button>                    
                <button class="btn btn-danger ms-2" id="cancel_edit_bt" name="cancel_edit_bt" type="button" title="ยกเลิก"><i class="fas fa-times"></i></button>
            </div>
        </form>
    </div>
  </div>
    `;
    $("#content").html(html); 

    var today = new Date();
    const dd = String( today.getDate() ).padStart( 2, '0' );
    const mm = String( today.getMonth() + 1 ).padStart( 2, '0' ); //January is 0!
    const yyyy = today.getFullYear();
    today = yyyy + '-' + mm + '-' + dd;
    jQuery.datetimepicker.setLocale( 'th' );
    $( '#picker_pack' ).datetimepicker( {
        timepicker: false,
        datepicker: true,
        format: 'd/m/Y',
        value:today,
        mask: true 
    } );

    let dropdown = $('#packerid');
        dropdown.empty();
        dropdown.append('<option value="0" >-- เลือกผู้บรรจุ --</option>');
        dropdown.prop('selectedIndex', 0);
        $.ajax({
                  type: "POST",
                  url: "api/getDropdown.php",
                  data: {id:'',fn:'depart'},
                  success: function(result){
                    $.each(result, function (key, entry) {
                      dropdown.append($('<option></option>').attr('value', entry.id_depart).text(entry.depart));
                    })  
                    $("#packerid option[value='"+u_depart+"']").attr("selected","selected");                             
                  }
                });  
  
    //$('#pcs_num').prop('readonly', true);
    showpacktable(rowperpage,page_sel); //<<<<<< แสดงตาราง    
}

function showpacktable(per,p){ //======================== แสดงตาราง   
  var i = ((p-1)*per);
  var obj = new Object();
  obj.search = document.getElementById('search_pack').value;
  obj.perpage = per;
  obj.page = p;
  obj.jwt = getCookie("jwt");   
  obj.packdate = document.getElementById('picker_pack').value;  
  obj.packbill = document.getElementById('packbill').value;  
  obj.packerid = document.getElementById('packerid').value; 
  const mydata = JSON.stringify(obj);
  const wait_msg =`<div class="my_loading" align="center"><br><i class="fas fa-spinner fa-pulse fa-2x"></i>&nbsp;&nbsp; กำลังโหลดข้อมูล.....</div> `;
  $("#table_pack").html(wait_msg);  
  $("#search_pack").attr("readonly", false);
  $("#bt_search_pack").attr("disabled",false);
  $("#bt_add_bag").attr("disabled",false);
  $.ajax({
    type: "POST", 
    url: "api/data_pack.php",
    data: mydata,
    success: function(result){
      const tt=`
      <table class="list-table table animate__animated animate__fadeIn" id="packtable">
        <thead >
          <tr style="background-color:#f9d0bf;">
            <th class="text-center" style="width:5%">ลำดับ</th> 
            <th class="text-start">เลขบรรจุ</th>
            <th class="text-start">รหัส</th>         
            <th class="text-start"><a id="sum_pack">สเปค1</a></th> 
            <th class="text-end">รายการ</th>    
            <th class="text-end">ผืน</th>    
            <th class="text-end">ก.ก.</th>
            <th class="text-end">ก.ก.(มตฐ)</th>
            <th class="text-center">แก้ไข&nbsp;&nbsp;ลบ</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
      <div class="mb-3" id="pagination">
      `;              
      $("#table_pack").html(tt);      
      pagination_show(p,result.page_all,per,'showpacktable'); //<<<< แสดงตัวจัดการหน้าข้อมูล Pagination >>const.js
      $.each(result.data, function (key, entry) {
        i++;
        bagTable(entry,i); //<<<<< แสดงรายการทั้งหมด             
      });   
      var txtsum = addCommas(result.list_all)+" รายการ ( "+addCommas(result.pcs_all)+ " ผืน/ "+addCommas((result.kg_all).toFixed(2))+ " ก.ก. )";      
      //$("#sum_pack").text(txtsum);    
      document.getElementById('sum_pack').text = txtsum;
    },
    error: function(xhr, resp, text) {
        if (xhr.responseJSON.message == "Access denied.") {
          showLoginPage();
          Signed("warning", "โปรดเข้าสู่ระบบก่อน !");            
        }else{
          showLoginPage();
          Signed("warning", "โปรดเข้าสู่ระบบก่อน !");
        }
    }
  });
}


function bagTable(ob,i){  //=========================== ฟังก์ชั่นเพิ่ม Row ตาราง
  let tableName = document.getElementById('packtable');
    let prev = tableName.rows.length;           
    let row = tableName.insertRow(prev);
    row.id = "row" + ob.bag_id;
    row.style.verticalAlign = "top";
    let txtDel = `<i class="fas fa-trash-alt" style="cursor:not-allowed; color:#939393;"></i>`;
    let set_value = ` style="text-decoration:none;" `;
    if(u_type > 0){
      txtEdit = `<i class="fas fa-edit me-3" onclick="edit_bag_Row(` + ob.bag_id + `,'` + ob.bag_no+`',` + ob.bag_kg+`)" style="cursor:pointer; color:#5cb85c;"></i>`;
      txtDel = `<i class="fas fa-trash-alt" onclick="delete_bag_Row(` + ob.bag_id + `)" style="cursor:pointer; color:#d9534f;"></i>`;
      set_value = ` onmouseover="edit_bag(this.id);" style="text-decoration:none; cursor:pointer;" `;
    }
    

    let n_col = 9;
    let col = [];
    let nn = 0;
    for(let ii=0; ii<n_col; ii++){
      col[ii] = row.insertCell(ii);
    }
    col[nn++].innerHTML = `<div id="no" class="text-center">`+i+`</div>`;
    col[nn++].innerHTML = `<div id="bagno` + ob.bag_id + `" class="text-start">`+ob.bag_no+`</div>`;
    col[nn++].innerHTML = `<div id="bagname` + ob.bag_id + `" class="text-start">`+ob.bag_name+`</div>`;
    col[nn++].innerHTML = `<div id="spec` + ob.bag_id + `" class="text-start">`+ob.bag_desc+`</div>`;
    col[nn++].innerHTML = `<div id="baglist` + ob.bag_id + `" class="text-end">`+(ob.bag_amount*1).toFixed(0)+`</div>`;
    col[nn++].innerHTML = `<div id="bagpcs` + ob.bag_id + `" class="text-end">`+(ob.bag_pcs*1).toFixed(0)+`</div>`;
    col[nn++].innerHTML = `<div id="bagkg` + ob.bag_id + `" class="text-end">`+(ob.bag_kg*1).toFixed(2)+`</div>`;
    col[nn++].innerHTML = `<div id="bagstdkg` + ob.bag_id + `" class="text-end">`+(ob.bag_std_kg*1).toFixed(2)+`</div>`;
    col[nn].innerHTML = `
    <input type="hidden" id="bagid` + ob.bag_id + `" name="bagid` + ob.bag_id + `" value="` + ob.bagid + `" />
    `+txtEdit+txtDel; 
    col[nn].style = "text-align: center;";
}

$(document).on('click',"#bt_add_bag",function () {  //เพิ่มข้อมูลบรรจุ
  const bill = document.getElementById('packbill').value;
  if(bill){
  var html = `
  <div class="col-lg-11 mx-auto mt-1">
    <div class="row">  
      <form id="fm_add_list">
          <div class="row">    
              <div class="col-md-3 mb-2">
                  <div class="input-group">
                    <div class="input-group-text">เลขบรรจุ</div>
                    <input type="text" class="form-control" name="packno" id="packno" style="text-transform:uppercase;" maxlength="25"> 
                    <button class="btn btn-success" type="button" id="bt_save_bag" name="bt_save_bag" onclick="check_bagno_exit()" title="บันทึำรายการบรรจุ"><i class="fas fa-plus"></i></button>
                  </div>
              </div>    
              <div class="col-md-3 mb-2">
                  <div class="input-group pack-kg">
                      <div class="input-group-text" >น้ำหนัก</div>
                      <input type="number" class="form-control" name="packkg" id="packkg" step="0.01" placeholder="0.00 ก.ก.">     
                      <button class="btn btn-success" type="button" id="bt_save_kg" name="bt_save_kg" onclick="add_bag_kg()" title="บันทึกน้ำหนัก"><i class="fas fa-plus"></i></button> 
                  </div>     
              </div> 
              <div class="col-md-2 mb-2">
                  <div class="input-group list-pcs">
                    <input class="form-check-input" style="margin-top:12px;" type="checkbox" value="1" id="pcslist_ck" name="pcslist_ck">
                    <label class="form-check-label me-2 pt-2" for="pcslist_ck">&nbsp;ระบุผืน</label>                        
                    <input type="number" id="list_pcs_num"  name="list_pcs_num" class="form-control" min="1" max="100" step="1" value="1">       
                  </div>     
              </div> 
              <div class="col-md-4 mb-2">
                  <div class="input-group">                  
                      <input type="text"  id="scan_list" name="scan_list" class="form-control" style="background-color:#fafadc; text-transform:uppercase;" placeholder="สแกนรหัส.." onkeydown="scanlistCode()" aria-label="Scan barcode" aria-describedby="barcode scan">
                      <button class="btn btn-warning ms-2" type="button" id="bt_back_pack" name="bt_back_pack" title="กลับ">กลับ</button>
                  </div>                   
              </div>
          </div>           
      </form>
    </div>
    <div class="row mt-1">  
      <div class="col-lg-12 mx-auto" id="table_pack_list"></div>
    </div>
  </div>  
    `;
    $("#table_pack").html(html); 
    $("#packno").attr("readonly", false);
    $("#packno").focus();
    $("#search_pack").attr("readonly", true);
    $("#bt_search_pack").attr("disabled",true);
    $("#bt_add_bag").attr("disabled",true);

    $(".pack-kg").hide();
    $(".list-pcs").hide();
    $("#scan_list").hide();
    $("#bt_save_bag").show();
  }else{
    Signed("warning", "โปรดระบุวันที่ หรือ เลขบิล !"); 
  }    
            
});

function edit_bag_Row(id,bno,bkg){
  bag_id_acc = id;
  var html = `
  <div class="col-lg-11 mx-auto mt-1">
    <div class="row">  
      <form id="fm_add_list">
          <div class="row">    
              <div class="col-md-3 mb-2">
                  <div class="input-group">
                    <div class="input-group-text">เลขบรรจุ</div>
                    <input type="text" class="form-control" name="packno" id="packno" style="text-transform:uppercase;" maxlength="25"> 
                    <button class="btn btn-success" type="button" id="bt_save_bag" name="bt_save_bag" onclick="check_bagno_exit()" title="บันทึำรายการบรรจุ"><i class="fas fa-plus"></i></button>
                  </div>
              </div>    
              <div class="col-md-3 mb-2">
                  <div class="input-group pack-kg">
                      <div class="input-group-text" >น้ำหนัก</div>
                      <input type="number" class="form-control" name="packkg" id="packkg" step="0.01" placeholder="0.00 ก.ก.">     
                      <button class="btn btn-success" type="button" id="bt_save_kg" name="bt_save_kg" onclick="add_bag_kg()" title="บันทึกน้ำหนัก"><i class="fas fa-plus"></i></button> 
                  </div>     
              </div> 
              <div class="col-md-2 mb-2">
                  <div class="input-group list-pcs">
                    <input class="form-check-input" style="margin-top:12px;" type="checkbox" value="1" id="pcslist_ck" name="pcslist_ck">
                    <label class="form-check-label me-2 pt-2" for="pcslist_ck">&nbsp;ระบุผืน</label>                        
                    <input type="number" id="list_pcs_num"  name="list_pcs_num" class="form-control" min="1" max="100" step="1" value="1">       
                  </div>     
              </div> 
              <div class="col-md-4 mb-2">
                  <div class="input-group">                  
                      <input type="text"  id="scan_list" name="scan_list" class="form-control" style="background-color:#fafadc; text-transform:uppercase;" placeholder="สแกนรหัส.." onkeydown="scanlistCode()" aria-label="Scan barcode" aria-describedby="barcode scan">
                      <button class="btn btn-warning ms-2" type="button" id="bt_back_pack" name="bt_back_pack" title="กลับ">กลับ</button>
                  </div>                   
              </div>
          </div>           
      </form>
    </div>
    <div class="row mt-1">  
      <div class="col-lg-12 mx-auto" id="table_pack_list"></div>
    </div>
  </div>  
    `;
    $("#table_pack").html(html); 
        
    $("#search_pack").attr("readonly", true);
    $("#bt_search_pack").attr("disabled",true);
    $("#bt_add_bag").attr("disabled",true);
   
    document.getElementById("packno").value = bno;
    document.getElementById("packkg").value = bkg;
    $("#packno").attr("readonly", true);

    $("#bt_save_bag").hide();
    $(".pack-kg").show();
    $(".list-pcs").show();
    $("#list_pcs_num").attr("readonly", true);
    $("#scan_list").show();
    $("#scan_list").focus();
    showlisttable(rowperpage,page_sel);
}

function check_bagno_exit(){ //======================== ตรวจ bag no ซ้ำหรือไม่ 
    var obj = new Object();
    obj.jwt = getCookie("jwt");    
    obj.bag_no = document.getElementById('packno').value;  
    obj.pack_date = document.getElementById('picker_pack').value;  
    obj.pack_bill = document.getElementById('packbill').value;  
    obj.packer_id = document.getElementById('packerid').value; 
    obj.acc = "exit";
    const mydata = JSON.stringify(obj);
  $.ajax({
    type: "POST", 
    url: "api/packbag_acc.php",
    data: mydata,
    success: function(result){      
      if((result.message == "Bag no is not exits.") && (obj.bag_no != '')){ //ไม่มีรหัสซ้ำ
        //Signed("success", "รหัสผ่าน !"); 
        create_bagno();
        $(".pack-kg").show();
        $(".list-pcs").show();
        $("#list_pcs_num").attr("readonly", true);
        $("#scan_list").show();
        $("#scan_list").focus();
        $("#scan_list").select();
        $("#packno").attr("readonly", true);
        $("#bt_save_bag").hide();
      }else if((result.message =="Bag no is exits.") || (obj.bag_no == '')){ //มีรหัสซ้ำ
        Signed("warning", "โปรดระบุเลขบรรจุ หรือ เลขบรรจุอาจซ้ำ !"); 
        $("#packno").focus();
        $("#packno").select();
        $(".text").hide();
        $(".pack-kg").hide();
        $(".list-pcs").hide();
        $("#scan_list").hide();
        $("#bt_save_bag").show();
      }      
    },
    error: function(xhr, resp, text) {
        if (xhr.responseJSON.message == "Access denied.") {          
          Signed("warning", "โปรดเข้าสู่ระบบก่อน !");            
        }else{
          showLoginPage();
          Signed("warning", "โปรดเข้าสู่ระบบก่อน !");
        }
    }
  });
}

function create_bagno(){ //======================== สร้าง bag no 
  var obj = new Object();
  obj.jwt = getCookie("jwt");    
  obj.bag_no = document.getElementById('packno').value;  
  obj.pack_date = document.getElementById('picker_pack').value;  
  obj.pack_bill = document.getElementById('packbill').value;  
  obj.packer_id = document.getElementById('packerid').value; 
  obj.acc = "add";
  const mydata = JSON.stringify(obj);
  
  $.ajax({
    type: "POST", 
    url: "api/packbag_acc.php",
    data: mydata,
    success: function(result){      
      if(result.message == "Bag_no was created."){ //สร้างสำเร็จ
        Signed("success", "บันทึกสำเร็จ !"); 
        bag_id_acc = result.bag_id; 
        showlisttable(rowperpage,page_sel);
      }else if(result.message =="Bag_no can not Create."){ //สร้างไม่สำเร็จ
        Signed("warning", "บันทึกไม่สำเร็จ โปรดลองใหม่ !"); 
      }      
    },
    error: function(xhr, resp, text) {
        if (xhr.responseJSON.message == "Access denied.") {          
          Signed("warning", "โปรดเข้าสู่ระบบก่อน !");            
        }else{
          showLoginPage();
          Signed("warning", "โปรดเข้าสู่ระบบก่อน !");
        }
    }
  });
}

function delete_bag_Row(id){ //================================ ลบข้อมูลในตาราง
  const data = document.getElementById("bagno"+id).innerHTML;
  const swalWithBootstrapButtons = Swal.mixin({
      customClass: {
        confirmButton: 'btn btn-primary me-3',
        cancelButton: 'btn btn-danger ms-3'
      },
      buttonsStyling: false
  })
  swalWithBootstrapButtons.fire({
      title: 'โปรดยืนยัน',
      text: "ต้องการลบข้อมูล "+ data +" หรือไม่?",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: ' ใช่ ',
      cancelButtonText: ' ไม่ ',
      reverseButtons: false
  }).then((result) => {
      if (result.isConfirmed) {
        var jwt = getCookie("jwt");
        var obj = new Object();
        obj.bag_id = id;
        obj.jwt = jwt;
        obj.acc = "del";
        var mydata = JSON.stringify(obj);
        $.ajax({
            url: "api/packbag_acc.php",
            type: "POST",
            contentType: "application/json",
            data: mydata,
            success: function(res) {
              swalWithBootstrapButtons.fire(
                  'ข้อมูลถูกลบ!',
                  'ข้อมูลของคุณได้ถูกลบออกจากระบบแล้ว!',
                  'success'
              );  
              showpacktable(rowperpage,page_sel);
            },
            error: function(xhr, resp, text) {
                if (xhr.responseJSON.message == "Unable to delete Bag.") {
                    Signed("error", "ลบข้อมูลไม่สำเร็จ !="+xhr.responseJSON.code);
                } else if (xhr.responseJSON.message == "Unable to access Bag.") {
                    Signed("error", "ไม่สามารถดำเนินการลบข้อมูลได้ โปรดลองใหม่!");
                }else{
                  showLoginPage();
                  Signed("warning", "ปฏิเสธการเข้าใช้ โปรดเข้าสู่ระบบก่อน!");
                }
            },
        });
        
          
      } else if ( result.dismiss === Swal.DismissReason.cancel ){
          swalWithBootstrapButtons.fire(
              'ยกเลิก',
              'ข้อมูลของคุณยังไม่ถูกลบ :)',
              'error'
          )
      }
  })
}

function showlisttable(per,p){ //======================== แสดงตาราง 
  var i = ((p-1)*per);
    var obj = new Object();
    obj.bag_id = bag_id_acc;
    obj.perpage = per;
    obj.page = p;
    obj.jwt = getCookie("jwt");    
    const mydata = JSON.stringify(obj);
    const wait_msg =`<div class="my_loading" align="center"><br><i class="fas fa-spinner fa-pulse fa-2x"></i>&nbsp;&nbsp; กำลังโหลดข้อมูล.....</div> `;
    $("#table_pack_list").html(wait_msg); 
  $.ajax({
    type: "POST", 
    url: "api/data_pack_list.php",
    data: mydata,
    success: function(result){      
      const tt=`
      <table class="list-table table animate__animated animate__fadeIn" id="packlisttable" >
        <thead >
          <tr style="background-color:#c0c0c0;">
            <th class="text-center" style="width:5%">ลำดับ</th>
            <th class="text-start">รหัส</th>         
            <th class="text-start"><a id="summary">สเปค1</a></th> 
            <th class="text-end">ผืน</th>    
            <th class="text-end">ก.ก.</th>
            <th class="text-center">ลบ</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
      <div class="mb-3" id="pagination">
      `;              
      $("#table_pack_list").html(tt);      
      pagination_show(p,result.page_all,per,'showlisttable'); //<<<< แสดงตัวจัดการหน้าข้อมูล Pagination >>const.js
      $.each(result.data, function (key, entry) {
        i++;
        listTable(entry,i); //<<<<< แสดงรายการทั้งหมด             
      });   
      var txtsum = addCommas(result.list_all)+" รายการ ( "+addCommas(result.pcs_all)+ " ผืน/ "+addCommas((result.kg_all).toFixed(2))+ " ก.ก. )";      
      $("#summary").text(txtsum);    
    },
    error: function(xhr, resp, text) {
        if (xhr.responseJSON.message == "Access denied.") {
          showLoginPage();
          Signed("warning", "โปรดเข้าสู่ระบบก่อน !");            
        }else{
          showLoginPage();
          Signed("warning", "โปรดเข้าสู่ระบบก่อน !");
        }
    }
  });
}

function listTable(ob,i){  //=========================== ฟังก์ชั่นเพิ่ม Row ตาราง
  let tableName = document.getElementById('packlisttable');
    let prev = tableName.rows.length;           
    let row = tableName.insertRow(prev);
    row.id = "row" + ob.list_id;
    row.style.verticalAlign = "top";
    let txtDel = `<i class="fas fa-trash-alt" style="cursor:not-allowed; color:#939393;"></i>`;
    let set_value = ` style="text-decoration:none;" `;
    if(u_type > 0){      
      txtDel = `<i class="fas fa-trash-alt" onclick="delete_list_Row(` + ob.list_id + `)" style="cursor:pointer; color:#d9534f;"></i>`;
      set_value = ` onmouseover="edit_bag(this.id);" style="text-decoration:none; cursor:pointer;" `;
    }
    
    let n_col = 6;
    let col = [];
    let nn = 0;
    for(let ii=0; ii<n_col; ii++){
      col[ii] = row.insertCell(ii);
    }
    col[nn++].innerHTML = `<div id="no" class="text-center">`+i+`</div>`;
    col[nn++].innerHTML = `<div id="listnt` + ob.list_id + `" class="text-start">`+ob.list_nt+`</div>`;
    col[nn++].innerHTML = `<div id="listdesc` + ob.list_id + `" class="text-start">`+ob.list_desc+`</div>`;
    col[nn++].innerHTML = `<div id="listpcs` + ob.list_id + `" class="text-end">`+ob.list_pcs+`</div>`;
    col[nn++].innerHTML = `<div id="listkg` + ob.list_id + `" class="text-end">`+ob.list_kg+`</div>`;
    col[nn].innerHTML = `
    <input type="hidden" id="listid` + ob.list_id + `" name="listid` + ob.list_id + `" value="` + ob.list_id + `" />
    `+txtDel; 
    col[nn].style = "text-align: center;";
}

function scanlistCode(){
    const datascan = document.getElementById('scan_list');    
    if(datascan.value.length >= 9){
      if(u_type > 0){
        scan_list_add();  
      }else{
        swalertshow("warning","ไม่ได้รับอนุญาติ","สิทธิการใช้งานของคุณคือเข้าดูข้อมูลอย่างเดียว" );
        const sel = document.getElementById("scan_list");
        sel.value = "";
        sel.focus();
        //sel.select();
      }
    }
}

function scan_list_add(){
  const sel = document.getElementById("scan_list");
  const code_nt = sel.value;
  const pcs = document.getElementById("list_pcs_num").value;
  const ck = document.getElementById("pcslist_ck");
  const ck_num = (ck.checked)?"1":"0";
  const jwt = getCookie("jwt");
      var obj = new Object();
      obj.code = code_nt.toUpperCase();
      obj.pcs = pcs;
      obj.ck_pcs = ck_num;
      obj.jwt = jwt;
      obj.bag_id = bag_id_acc;
      obj.acc = "add";
      const mydata = JSON.stringify(obj);
      $.ajax({
          url: "api/pack_list_acc.php",
          type: "POST",
          contentType: "application/json",
          data: mydata,
          success: function(res) {    
            showlisttable(rowperpage,page_sel);
            //var sel = document.getElementById("scan_list");
            sel.value="";  
            sel.focus();
            //sel.select();              
          },
          error: function(xhr, resp, text) {
              if (xhr.responseJSON.message == "Not found code.") {
                  swalertshow("warning","ไม่พบข้อมูล","กรุณาเพิ่มข้อมูล "+ obj.code +" ก่อน");
                  sel.value="";
              } else if (xhr.responseJSON.message == "Unable to access list.") {
                  Signed("error", "ไม่สามารถดำเนินการบันทึกข้อมูลได้ โปรดลองใหม่!");
              } else if (xhr.responseJSON.message == "Unable to create list.") {
                  Signed("error", "ไม่สามารถบันทึกข้อมูลได้ โปรดลองใหม่!");
              }else{
                showLoginPage();
                Signed("warning", "ปฏิเสธการเข้าใช้ โปรดเข้าสู่ระบบก่อน!");
              }
          },
      });

}

function delete_list_Row(id){ //================================ ลบข้อมูลในตาราง
  const swalWithBootstrapButtons = Swal.mixin({
      customClass: {
        confirmButton: 'btn btn-primary me-3',
        cancelButton: 'btn btn-danger ms-3'
      },
      buttonsStyling: false
  })
  swalWithBootstrapButtons.fire({
      title: 'โปรดยืนยัน',
      text: "ต้องการลบข้อมูล หรือไม่?",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: ' ใช่ ',
      cancelButtonText: ' ไม่ ',
      reverseButtons: false
  }).then((result) => {
      if (result.isConfirmed) {
        const jwt = getCookie("jwt");
        var obj = new Object();
        obj.list_id = id;
        obj.jwt = jwt;
        obj.acc = "del";
        const mydata = JSON.stringify(obj);
        $.ajax({
            url: "api/pack_list_acc.php",
            type: "POST",
            contentType: "application/json",
            data: mydata,
            success: function(res) {
              swalWithBootstrapButtons.fire(
                  'ข้อมูลถูกลบ!',
                  'ข้อมูล ได้ถูกลบออกจากระบบแล้ว!',
                  'success'
              );  
              showlisttable(rowperpage,page_sel);
            },
            error: function(xhr, resp, text) {
                if (xhr.responseJSON.message == "Unable to delete List.") {
                    Signed("error", "ลบข้อมูลไม่สำเร็จ !="+xhr.responseJSON.code);
                } else if (xhr.responseJSON.message == "Unable to access list.") {
                    Signed("error", "ไม่สามารถดำเนินการลบข้อมูลได้ โปรดลองใหม่!");
                }else{
                  showLoginPage();
                  Signed("warning", "ปฏิเสธการเข้าใช้ โปรดเข้าสู่ระบบก่อน!");
                }
            },
        });
        
          
      } else if ( result.dismiss === Swal.DismissReason.cancel ){
          swalWithBootstrapButtons.fire(
              'ยกเลิก',
              'ข้อมูล ยังไม่ถูกลบ :)',
              'error'
          )
      }
  })
}

function add_bag_kg(){
  const bag_kg = document.getElementById("packkg").value;
  const bag_no = document.getElementById("packno").value;
  const jwt = getCookie("jwt");
      var obj = new Object();
      obj.jwt = jwt;
      obj.bag_id = bag_id_acc;
      obj.bag_kg = bag_kg;
      obj.acc = "upKg";
      const mydata = JSON.stringify(obj);
      $.ajax({
          url: "api/packbag_acc.php",
          type: "POST",
          contentType: "application/json",
          data: mydata,
          success: function(res) {      
            Signed("success", "บันทึกน้ำหนักบรรจุ "+ bag_no +" สำเร็จ !"); 
            $("#scan_list").value="";
            $("#scan_list").focus();     
          },
          error: function(xhr, resp, text) {
              if (xhr.responseJSON.message == "Unable to update Kg.") {
                  Signed("error", "บันทึกข้อมูล "+ bag_no +" ไม่สำเร็จ โปรดลองใหม่!");
              } else if (xhr.responseJSON.message == "Unable to access Bag.") {
                  Signed("error", "ไม่สามารถบันทึกข้อมูลได้ โปรดลองใหม่!");
              }else{
                showLoginPage();
                Signed("warning", "ปฏิเสธการเข้าใช้ โปรดเข้าสู่ระบบก่อน!");
              }
          },
      });

}


//=========================== Even เกี่ยวกับ รายการ ======================================
$(document).on('click',"#bt_search_pack,#bt_back_pack",function () {  //ค้นหารายการ  
  showpacktable(rowperpage,'1'); 
});
$(document).on("change", "#picker_pack,#packbill,#packerid", function () {  
  showpacktable(rowperpage,'1');          
});

$(document).on('click',"#pcslist_ck",function () {  
    $('#list_pcs_num').prop('readonly', !this.checked);
});

