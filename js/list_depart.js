
function show_depart_table(){ //========================== แสดงค้นหา และปุ่มเพิ่ม หมวดรายการ
    var html = `
  <div class="container animate__animated animate__fadeIn">
    <div class="row">                
        <div class="col-lg-6 mx-auto mt-3">
            <form id="fmsearch_depart">
                <div class="input-group mb-2">
                    <input type="text" id="search_depart" name="search_depart" class="form-control" placeholder="คำค้นหา.." aria-label="Search" aria-describedby="button-search">
                    <button class="btn btn-success" type="button" id="bt_search_depart" name="bt_search_depart" title="ค้นหา"><i class="fas fa-search"></i></button>
                    <button class="btn btn-primary ms-2" id="bt_add_depart" name="bt_add_depart" type="button" title="เพิ่มข้อมูล"><i class="fas fa-plus"></i></button>
                    <button class="btn btn-warning ms-2" id="bt_back" name="bt_back" type="button" title="กลับ"><i class="fas fa-reply"></i></button>
                </div>
            </form>
        </div>
    </div>   
    <div class="row">  
        <div class="col-lg-6 mx-auto" id="add_depart"></div>
    </div>   
    <div class="row">  
        <div class="col-lg-6 mx-auto" id="edit_depart"></div>
    </div>   
    <div class="row">  
        <div class="col-lg-6 mx-auto" id="table_depart"></div>
    </div>
  </div>
    `;
    $("#content").html(html);
    showdeparttable(rowperpage,page_sel); //<<<<<< แสดงตาราง
}

function showdeparttable(per,p){ //======================== แสดงตาราง
  var ss = document.getElementById('search_depart').value;            
  var jwt = getCookie("jwt");
  var i = ((p-1)*per);
  $.ajax({
    type: "POST",
    url: "api/data_depart.php",
    data: {search:ss,perpage:per,page:p,jwt:jwt},
    success: function(result){
      var tt=`
      <table class="list-table table animate__animated animate__fadeIn" id="departtable" >
        <thead>
          <tr>
            <th class="text-center" style="width:5%">ลำดับ</th> 
            <th class="text-left">หน่วยงาน</th>
            <th class="text-center">แก้ไข&nbsp;&nbsp;&nbsp;ลบ</th>                
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
      <div class="mb-3" id="pagination">
      `;              
      $("#table_depart").html(tt);      
      pagination_show(p,result.page_all,per,'showdeparttable'); //<<<<<<<< แสดงตัวจัดการหน้าข้อมูล Pagination >>const.js
      $.each(result.data, function (key, entry) {
        i++;
        listdepartTable(entry.id_depart,entry.depart,i); //<<<<< แสดงรายการทั้งหมด             
      });             
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

function listdepartTable(id,dp,i){  //=========================== ฟังก์ชั่นเพิ่ม Row ตารางประเเภท
  var tableName = document.getElementById('departtable');
    var prev = tableName.rows.length;           
    var row = tableName.insertRow(prev);
    row.id = "row" + id;
    row.style.verticalAlign = "top";
    var txtDel = `<i class="fas fa-trash-alt" style="cursor:not-allowed; color:#939393;"></i>`;
    if(u_type == "2"){
      txtDel = `<i class="fas fa-trash-alt" onclick="deletedepartRow(` + id + `)" style="cursor:pointer; color:#d9534f;"></i>`;
    }
    var col1 = row.insertCell(0);
    var col2 = row.insertCell(1);
    var collast = row.insertCell(2);
    col1.innerHTML = `<div id="no" class="text-center">`+i+`</div>`;
    col2.innerHTML = `<div id="depart` + id + `" class="text-left">`+dp+`</div>`;
    collast.innerHTML = `
    <input type="hidden" id="id` + id + `" name="id` + id + `" value="` + id + `" />

    <i class="fas fa-edit me-3" onclick="editdepartRow(` + id + `)" style="cursor:pointer; color:#5cb85c;"></i>`+txtDel; 
    collast.style = "text-align: center;";
}

function deletedepartRow(id){ //================================ ลบข้อมูลในตาราง
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
            url: "api/depart_acc.php",
            type: "POST",
            contentType: "application/json",
            data: data,
            success: function(res) {
              swalWithBootstrapButtons.fire(
                  'ข้อมูลถูกลบ!',
                  'ข้อมูลของคุณได้ถูกลบออกจากระบบแล้ว!',
                  'success'
              );  
              showdeparttable(rowperpage,page_sel);
            },
            error: function(xhr, resp, text) {
                if (xhr.responseJSON.message == "Unable to delete Depart.") {
                    Signed("error", "ลบข้อมูลไม่สำเร็จ !="+xhr.responseJSON.code);
                } else if (xhr.responseJSON.message == "Unable to access Depart.") {
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

function editdepartRow(id){ //============================== แก้ไขข้อมูลในตาราง       
    var html = `          
    <div class="edit_depart animate__animated animate__fadeIn mt-3 mb-4">
      <div style="text-align: center;">
        <i class="far fa-edit fa-3x"></i>&nbsp;&nbsp;&nbsp;<a style="font-size: 2rem">แก้ไขข้อมูล</a>   
      </div>  
      <form class="myForm mt-2" id="edit_depart_form">
        <div class="row mb-2">                      
              <div class="form-group">
                <label for="depart">หน่วยงาน :</label>
                <input type="text" class="form-control" name="depart" id="depart" maxlength="100" required value="` +
                document.getElementById('depart'+id).innerText +
                `">
              </div>       
              <input type="hidden" name="id" value="`+document.getElementById('id'+id).value+`">  
        </div>

        <div class="row mt-3 justify-content-center" >
            <button type="submit" class="btn btn-primary me-3" style="width :80px;">บันทึก</button>
            <button id="bt_cancel_editdepart" type="button" class="btn btn-danger ms-3" style="width :80px;">ยกเลิก</button>
        </div>
      </form>
    </div>
      `;
      $("#edit_depart").html(html); 
    
    $("#add_depart").html("");  
    $("#table_depart").html("");              

}

//=========================== Even เกี่ยวกับ รายการ ======================================
$(document).on('click',"#bt_search_depart",function () {  //ค้นหารายการ
  $("#edit_depart").html("");
  $("#add_depart").html("");
  $("#bt_add_depart").show();
  showdeparttable(rowperpage,'1');          
});

$(document).on("click", "#bt_add_depart", function() { // แสดงหน้าบันทึกเพิ่มข้อมูล  
  var html = `          
        <div class="add_depart animate__animated animate__fadeIn mt-3 mb-4">
          <div style="text-align: center;">
            <i class="fab fa-buffer fa-3x"></i>&nbsp;&nbsp;&nbsp;<a style="font-size: 2rem">เพิ่มข้อมูล</a>   
          </div> 
          <form class="myForm mt-2" id="add_depart_form">
            <div class="row mb-2">                      
                <div class="form-group">
                    <label for="depart">หน่วยงาน :</label>
                    <input type="text" class="form-control" name="depart" id="depart" maxlength="100" required>
                </div>       
            </div>

            <div class="row mt-3 justify-content-center" >
                <button type="submit" class="btn btn-primary me-3" style="width :80px;">บันทึก</button>
                <button id="bt_cancel_depart" type="button" class="btn btn-danger ms-3" style="width :80px;">ยกเลิก</button>
            </div>
          </form>
        </div>
  
          `;
  $("#add_depart").html(html);
 
  $("#edit_depart").html("");
  $("#bt_add_depart").hide();
  $("#table_depart").html("");  
});

$(document).on("click", "#bt_cancel_depart", function() {  // ปิดฟอร์มเพิ่มข้อมูล
    $("#add_depart").html("");
    $("#bt_add_depart").show();
    showdeparttable(rowperpage,'1');
});

$(document).on("click", "#bt_cancel_editdepart", function() {  // ปิดฟอร์มแก้ไขข้อมูล
  $("#edit_depart").html("");
  $("#bt_add_depart").show();
  showdeparttable(rowperpage,'1');
});

$(document).on("submit", "#add_depart_form", function() {   // บันทึกเพิ่มข้อมูล
    var add_form = $(this);
    var jwt = getCookie("jwt");
    var add_form_obj = add_form.serializeObject();
    add_form_obj.jwt = jwt;
    add_form_obj.acc = "add";
    var form_data = JSON.stringify(add_form_obj);
    $.ajax({
        url: "api/depart_acc.php",
        type: "POST",
        contentType: "application/json",
        data: form_data,
        success: function(result) {
            $("#add_depart").html("");
            $("#bt_add_depart").show();
            Signed("success", " บันทึกข้อมูลสำเร็จ ");
            showdeparttable(rowperpage,'1');
        },
        error: function(xhr, resp, text) {
            if (xhr.responseJSON.message == "Unable to create Depart.") {
                Signed("error", " บันทึกข้อมูลไม่สำเร็จ ");
            } else if (xhr.responseJSON.message == "Depart Exit.") {
                swalertshow('warning', 'บันทึกข้อมูลไม่สำเร็จ', 'หน่วยงาน นี้มีอยู่แล้ว !');
            } else if (xhr.responseJSON.message == "Unable to access Depart.") {
                Signed("warning", "ปฏิเสธการเข้าใช้ โปรดลองใหม่!");
            }else{
              showLoginPage();
              Signed("warning", "ปฏิเสธการเข้าใช้ โปรดเข้าสู่ระบบก่อน!");
            }
        },
    });
    return false;
});

$(document).on("submit", "#edit_depart_form", function() {   // แก้ไขข้อมูล
    var edit_form = $(this);
    var jwt = getCookie("jwt");
    var edit_form_obj = edit_form.serializeObject();
    edit_form_obj.jwt = jwt;
    edit_form_obj.acc = "up";
    var form_data = JSON.stringify(edit_form_obj);    
    $.ajax({
        url: "api/depart_acc.php",
        type: "POST",
        contentType: "application/json",
        data: form_data,
        success: function(result) {
            $("#edit_depart").html("");
            Signed("success", "แก้ไขข้อมูลสำเร็จ ");
            showdeparttable(rowperpage,page_sel);
        },
        error: function(xhr, resp, text) {
            if (xhr.responseJSON.message == "Unable to update Depart.") {
                Signed("error", " แก้ไขข้อมูลไม่สำเร็จ ");
            } else if (xhr.responseJSON.message == "Depart Exit.") {
                swalertshow('warning', 'แก้ไขข้อมูลไม่สำเร็จ', 'หน่วยงาน นี้มีอยู่แล้ว !');
            } else if (xhr.responseJSON.message == "Unable to access Depart.") {
                Signed("warning", "ปฏิเสธการเข้าใช้ โปรดลองใหม่!");
            }else{
              showLoginPage();
              Signed("warning", "ปฏิเสธการเข้าใช้ โปรดเข้าสู่ระบบก่อน!");
            }
        },
    });
    return false;
});