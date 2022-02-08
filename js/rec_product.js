
function show_rec_prod(){ //========================== แสดงค้นหา และปุ่มเพิ่ม หมวดรายการ
    var html = `
  <div class="container animate__animated animate__fadeIn">
    <div class="row">                
        <div class="col-lg-10 mx-auto mt-1">
        <h4 style="color:#ffff; text-align: center; text-shadow: 2px 2px #0d470c, 3px 2px #0d470c; background-color:#388752; padding: 8px 0; border-radius: 20px 20px 0 0;">บันทึกรับเข้า</h4>
            <form id="fmrec_prod">
                <div class="row">    
                    <div class="col-md-6 mb-2">
                        <div class="input-group">
                            <div class="input-group-text" style="width: 60px; background-color: aquamarine;">วันที่</div>
                            <input type="text" class="form-control" name="daterec" id="picker">
                        </div>
                    </div>    
                    <div class="col-md-6 mb-2">
                        <div class="input-group">
                            <div class="input-group-text" style="width: 60px; background-color: aquamarine;">เลขบิล</div>
                            <input type="text" class="form-control" name="bill" id="bill" maxlength="25">      
                        </div>     
                    </div> 
                </div> 
                <div class="row"> 
                    <div class="col-md-6 mb-2">
                        <div class="input-group">
                            <div class="input-group-text" style="width: 60px; background-color: aquamarine;">ผู้รับ</div>
                            <select class="form-select" name="dp_rec" id="dp_rec"></select>   
                        </div>     
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="input-group">
                            <div class="input-group-text" style="width: 60px; background-color: aquamarine;">ผู้ส่ง</div>
                            <select class="form-select" name="dp_post" id="dp_post"></select>   
                        </div>     
                    </div>
                </div>

                <div class="row"> 
                    <div class="col-md-4 mb-2" style="display: flex; justify-content: center;"> 
                        <div class="input-group" style="width:180px;">
                            <input class="form-check-input" style="margin-top:12px;" type="checkbox" value="1" id="pcs_ck" name="pcs_ck">
                            <label class="form-check-label pt-2 me-3" for="pcs_ck">&nbsp;ระบุผืน&nbsp;</label>                        
                            <input type="number" id="pcs_num" name="pcs_num" class="form-control" min="1" max="100" step="1" value="1">                            
                        </div>
                    </div>  
                    <div class="col-md-4 mb-2"> 
                        <div class="input-group">
                            <input type="text" id="scan_prod" name="scan_prod" class="form-control" style="background-color:#fafadc; text-transform:uppercase;" placeholder="สแกนรหัส.." onkeydown="scanCode()">
                            <button class="btn btn-primary" id="bt_add_rec" name="bt_add_rec" type="button" title="เพิ่มข้อมูล"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>
                    <div class="col-md-4"> 
                        <div class="input-group">
                            <input type="text" id="search_rec" name="search_rec" class="form-control" placeholder="คำค้นหา.." aria-label="Search" aria-describedby="button-search">
                            <button class="btn btn-success" type="button" id="bt_search_rec" name="bt_search_rec" title="ค้นหา"><i class="fas fa-search"></i></button>
                        </div> 
                    </div>
                                      
                </div>
                <div class="row">
                                       
                </div>
            </form>
        </div>
    </div>   
    <div class="row">  
        <div class="col-lg-10 mx-auto" id="table_rec_prod"></div>
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
    var dd = String( today.getDate() ).padStart( 2, '0' );
    var mm = String( today.getMonth() + 1 ).padStart( 2, '0' ); //January is 0!
    var yyyy = today.getFullYear();
    today = yyyy + '-' + mm + '-' + dd;

    jQuery.datetimepicker.setLocale( 'th' );

    $( '#picker' ).datetimepicker( {
        timepicker: false,
        datepicker: true,
        format: 'd/m/Y',
        value:today,
        mask: true 
    } );

    let dropdown = $('#dp_rec');
        dropdown.empty();
        dropdown.append('<option value="0" >-- เลือกผู้รับ --</option>');
        dropdown.prop('selectedIndex', 0);
        $.ajax({
                  type: "POST",
                  url: "api/getDropdown.php",
                  data: {id:'',fn:'depart'},
                  success: function(result){
                    $.each(result, function (key, entry) {
                      dropdown.append($('<option></option>').attr('value', entry.id_depart).text(entry.depart));
                    })  
                    $("#dp_rec option[value='"+u_depart+"']").attr("selected","selected");                             
                  }
                });  

    let dropdown2 = $('#dp_post');
    dropdown2.empty();
    dropdown2.append('<option value="0" >-- เลือกผู้ส่ง --</option>');
    dropdown2.prop('selectedIndex', 0);
    $.ajax({
                type: "POST",
                url: "api/getDropdown.php",
                data: {id:'',fn:'depart'},
                success: function(result){
                $.each(result, function (key, entry) {
                    dropdown2.append($('<option></option>').attr('value', entry.id_depart).text(entry.depart));
                })                             
                }
            });      
    $('#pcs_num').prop('readonly', true);
    showrectable(rowperpage,page_sel); //<<<<<< แสดงตาราง
}

function showrectable(per,p){ //======================== แสดงตาราง
  var ss = document.getElementById('search_rec').value;       
  var daterec = document.getElementById('picker').value;    
  var bill = document.getElementById('bill').value;    
  var dprec = document.getElementById('dp_rec').value;    
  var dppost = document.getElementById('dp_post').value;         
  var jwt = getCookie("jwt");
  var i = ((p-1)*per);
  const wait_msg =`<div class="my_loading" align="center"><br><i class="fas fa-spinner fa-pulse fa-2x"></i>&nbsp;&nbsp; กำลังโหลดข้อมูล.....</div> `;
    $("#table_rec_prod").html(wait_msg); 
  $.ajax({
    type: "POST", 
    url: "api/data_rec_product.php", 
    data: {daterec:daterec,bill:bill,dp_rec:dprec,dp_post:dppost,search:ss,perpage:per,page:p,jwt:jwt},
    success: function(result){
      var tt=`
      <table class="list-table table animate__animated animate__fadeIn" id="recprodtable" >
        <thead>
          <tr style="background-color:#acf7e2;">
            <th class="text-center" style="width:5%">ลำดับ</th> 
            <th class="text-start">Code</th>
            <th class="text-start"><a id="summary">สเปค1</a></th>         
            <th class="text-start">ประเภท</th>   
            <th class="text-end">ก.ก.</th>   
            <th class="text-end">ผืน</th>    
            <th class="text-center">ลบ</th>        
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
      <div class="mb-3" id="pagination">
      `;              
      $("#table_rec_prod").html(tt);      
      pagination_show(p,result.page_all,per,'showrectable'); //<<<< แสดงตัวจัดการหน้าข้อมูล Pagination >>const.js
      $.each(result.data, function (key, entry) {
        i++;
        listrecTable(entry,i); //<<<<< แสดงรายการทั้งหมด             
      });   
      var txtsum = +result.list_all+" รายการ ( "+addCommas(result.pcs_all)+ " ผืน/ "+addCommas((result.kg_all).toFixed(2))+ " ก.ก. )";      
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

function scanCode(){
    const datascan = document.getElementById('scan_prod');    
    if(datascan.value.length >= 9){
      if(u_type > 0){
        scan_add();  
      }else{
        swalertshow("warning","ไม่ได้รับอนุญาติ","สิทธิการใช้งานของคุณคือเข้าดูข้อมูลอย่างเดียว" );
        var sel = document.getElementById("scan_prod");
        sel.value = "";
        sel.focus();
        //sel.select();
      }
    }
}

function listrecTable(ob,i){  //=========================== ฟังก์ชั่นเพิ่ม Row ตารางประเเภท
  let tableName = document.getElementById('recprodtable');
    let prev = tableName.rows.length;           
    let row = tableName.insertRow(prev);
    row.id = "row" + ob.id_rec;
    row.style.verticalAlign = "top";
    let txtDel = `<i class="fas fa-trash-alt" style="cursor:not-allowed; color:#939393;"></i>`;
    let set_value = ` style="text-decoration:none;" `;
    if(u_type > 0){
      txtDel = `<i class="fas fa-trash-alt" onclick="delete_rec_Row(` + ob.id_rec + `)" style="cursor:pointer; color:#d9534f;"></i>`;
      set_value = ` onmouseover="edit_data(this.id);" style="text-decoration:none; cursor:pointer;" `;
    }   

    let n_col = 7;
    let col = [];
    for(let ii=0; ii<n_col; ii++){
      col[ii] = row.insertCell(ii);
    }
    col[0].innerHTML = `<div id="no" class="text-center">`+i+`</div>`;
    col[1].innerHTML = `<div id="code` + ob.id_rec + `" class="text-start">`+ob.code+`</div>`;
    col[2].innerHTML = `<div id="spec` + ob.id_rec + `" class="text-start">`+ob.spec+`</div>`;
    col[3].innerHTML = `<div id="searchtxt` + ob.id_rec + `" class="text-start">`+ob.search+`</div>`;
    col[4].innerHTML = `<div id="kg` + ob.id_rec + `" class="text-end">`+(ob.wt*ob.pcs).toFixed(2)+`</div>`;
    col[5].innerHTML = `<div class="text-end"><a id="pcs-` + ob.id_rec + `" `+set_value+` >`+ob.pcs+`</a></div>`;
    col[n_col-1].innerHTML = `
    <input type="hidden" id="id_rec` + ob.id_rec + `" name="id_rec` + ob.id_rec + `" value="` + ob.id_rec + `" />
    `+txtDel; 
    col[n_col-1].style = "text-align: center;";
}

function edit_data(id){ // เมนูคลิ๊กขวาเพื่อแก้ไขข้อมูล
    var ele_sel = document.getElementById(id);
    ele_sel.addEventListener("contextmenu",function(event){
        event.preventDefault();
        var ctx = document.getElementById("edit_data");
        ctx.style.display = "block";
        var w_table =document.getElementById("recprodtable").clientWidth;
        var xx = 0;
        if((w_table - event.pageX)<250){ xx = -255;}
        ctx.style.left = (event.pageX + 5+xx)+"px";
        ctx.style.top = (event.pageY + 5)+"px";
        $("#ed_data").val(this.text);
        var str_arr = id.split("-");
        $("#head_data").val(str_arr[0]);
        $("#id_data").val(str_arr[1]);
    },false);
    ele_sel.addEventListener("click",function(event){
      close_edit_data();
    },false);
  }
  
  function close_edit_data(){
    var ctx = document.getElementById("edit_data");
            ctx.style.display = "";
            ctx.style.left = "";
            ctx.style.top = "";                    
  }

  $(document).on("click", "#recprodtable, #fmrec_prod, #cancel_edit_bt", function () { //ปิดหน้าเมนูคลิ๊กขวา
    close_edit_data();
  });

  $(document).on("submit", "#fmedit_data", function () { //===== ทำการแก้ไขข้อมูล (คลิ๊กขวา)
    var update_data_form = $(this);
    var jwt = getCookie("jwt");
    var update_data_form_obj = update_data_form.serializeObject();
    update_data_form_obj.jwt = jwt;
    update_data_form_obj.acc = "up";
    var form_data = JSON.stringify(update_data_form_obj);
      $.ajax({
        url: "api/product_rec_acc.php",
        type: "POST",
        contentType: "application/json",
        data: form_data,
        success: function (result) {
          Signed("success"," ปรับปรุงข้อมูลสำเร็จ ");   
          showrectable(rowperpage,page_sel);
        },
        error: function (xhr, resp, text) {
          if(xhr.responseJSON.message == "Unable to update receive.") {
            Signed("error"," ปรับปรุงข้อมูลไม่สำเร็จ ");         
          }else if(xhr.responseJSON.message == "Access denied.") {
            showLoginPage();
            Signed("warning","ปฏิเสธการเข้าใช้ โปรดเข้าสู่ระบบก่อน");
          }
        },
      });
      close_edit_data();      
    return false;
  });



function delete_rec_Row(id){ //================================ ลบข้อมูลในตาราง
  const swalWithBootstrapButtons = Swal.mixin({
      customClass: {
        confirmButton: 'btn btn-primary me-3',
        cancelButton: 'btn btn-danger ms-3'
      },
      buttonsStyling: false
  })
  swalWithBootstrapButtons.fire({
      title: 'โปรดยืนยัน',
      text: "ต้องการลบข้อมูลหรือไม่?",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: ' ใช่ ',
      cancelButtonText: ' ไม่ ',
      reverseButtons: false
  }).then((result) => {
      if (result.isConfirmed) {
        var jwt = getCookie("jwt");
        var obj = new Object();
        obj.id = id;
        obj.jwt = jwt;
        obj.acc = "del";
        var data = JSON.stringify(obj);
        $.ajax({
            url: "api/product_rec_acc.php",
            type: "POST",
            contentType: "application/json",
            data: data,
            success: function(res) {
              swalWithBootstrapButtons.fire(
                  'ข้อมูลถูกลบ!',
                  'ข้อมูลของคุณได้ถูกลบออกจากระบบแล้ว!',
                  'success'
              );  
              showrectable(rowperpage,page_sel);
              var sel = document.getElementById("scan_prod");
              sel.focus();
              sel.select();
            },
            error: function(xhr, resp, text) {
                if (xhr.responseJSON.message == "Unable to delete receive.") {
                    Signed("error", "ลบข้อมูลไม่สำเร็จ !="+xhr.responseJSON.code);
                } else if (xhr.responseJSON.message == "Unable to access receive.") {
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

//=========================== Even เกี่ยวกับ รายการ ======================================
$(document).on('click',"#bt_search_rec",function () {  //ค้นหารายการ  
  showrectable(rowperpage,'1'); 
});
$(document).on("change", "#picker,#bill,#dp_rec,#dp_post", function () {  
  showrectable(rowperpage,'1');          
});

$(document).on('click',"#bt_add_rec",function () {  //บันทึกข้อมูล
  if(u_type > 0){
    scan_add();  
  }else{
    swalertshow("warning","ไม่ได้รับอนุญาติ","สิทธิการใช้งานของคุณคือเข้าดูข้อมูลอย่างเดียว" );
    var sel = document.getElementById("scan_prod");
    sel.focus();
    sel.select();
  }
            
});

$(document).on('click',"#pcs_ck",function () {  
    $('#pcs_num').prop('readonly', !this.checked);
});

function scan_add(){
    var d_rec = document.getElementById("picker").value;
    var bill = document.getElementById("bill").value;
    var dprec = document.getElementById("dp_rec").value;
    var dppost = document.getElementById("dp_post").value;
    var code_nt = document.getElementById("scan_prod").value;
    var pcs = document.getElementById("pcs_num").value;
    var ck = document.getElementById("pcs_ck");
    var ck_num = (ck.checked)?"1":"0";
    var strtxt = "";
    let num = 0;
    if(d_rec == ""){
      num++;
      strtxt = "กรุณาระบุวันที่";
    }else if(bill == ""){
      num++;
      strtxt = "กรุณาระบุเลขที่บิล";
    }else if(dprec == 0){
      num++;
      strtxt = "กรุณาเลือกผู้รับ";
    }else if(dppost == 0){
      num++;
      strtxt = "กรุณาเลือกผู้ส่ง";
    }
  if(num == 0){

    var jwt = getCookie("jwt");
        var obj = new Object();
        obj.date_rec = d_rec;
        obj.bill = bill;
        obj.dp_rec = dprec;
        obj.dp_post = dppost;
        obj.code = code_nt.toUpperCase();
        obj.pcs = pcs;
        obj.ck_pcs = ck_num;
        obj.jwt = jwt;
        obj.acc = "add";
        var data = JSON.stringify(obj);
        $.ajax({
            url: "api/product_rec_acc.php",
            type: "POST",
            contentType: "application/json",
            data: data,
            success: function(res) {              
              showrectable(rowperpage,page_sel);
              var sel = document.getElementById("scan_prod");
              sel.value="";  
              sel.focus();
              //sel.select();              
            },
            error: function(xhr, resp, text) {
                if (xhr.responseJSON.message == "Not found code.") {
                    swalertshow("warning","ไม่พบข้อมูล","กรุณาเพิ่มข้อมูล "+ code_nt +" ก่อน");
                } else if (xhr.responseJSON.message == "Unable to access receive.") {
                    Signed("error", "ไม่สามารถดำเนินการบันทึกข้อมูลได้ โปรดลองใหม่!");
                } else if (xhr.responseJSON.message == "Unable to create receive.") {
                    Signed("error", "ไม่สามารถบันทึกข้อมูลได้ โปรดลองใหม่!");
                }else{
                  showLoginPage();
                  Signed("warning", "ปฏิเสธการเข้าใช้ โปรดเข้าสู่ระบบก่อน!");
                }
            },
        });
  }else{
    swalertshow("warning","ข้อมูลไม่ครบถ้วน ",strtxt );
  }
}

