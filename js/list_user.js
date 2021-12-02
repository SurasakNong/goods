
function show_user_table(){ //========================== แสดงค้นหา และปุ่มเพิ่ม หมวดรายการ
    var html = `
  <div class="container animate__animated animate__fadeIn">
    <div class="row">                
        <div class="col-lg-6 mx-auto mt-3">
            <form id="fmsearch_user">
                <div class="input-group mb-2">
                    <input type="text" id="search_user" name="search_user" class="form-control" placeholder="คำค้นหา.." aria-label="Search" aria-describedby="button-search">
                    <button class="btn btn-success" type="button" id="bt_search_user" name="bt_search_user" title="ค้นหา"><i class="fas fa-search"></i></button>
                    <button class="btn btn-primary ms-2" id="bt_add_user" name="bt_add_user" style="width: 42px;" type="button" title="เพิ่มข้อมูล"><i class="fas fa-plus"></i></button>
                    <button class="btn btn-warning ms-2" id="bt_back" name="bt_back" type="button" title="กลับ"><i class="fas fa-reply"></i></button>
                </div>
            </form>
        </div>
    </div>   
    <div class="row">  
        <div class="col-lg-6 mx-auto" id="adduser"></div>
    </div>   
    <div class="row">  
        <div class="col-lg-6 mx-auto" id="edituser"></div>
    </div>   
    <div class="row">  
        <div class="col-lg-8 mx-auto" id="tableuser"></div>
    </div>
  </div>
    `;
    $("#content").html(html);
    showusertable(rowperpage,page_sel); //<<<<<< แสดงตารางผู้ใช้งาน
}

function showusertable(per,p){ //======================== แสดงตารางหมวดสินค้า    
  var ss = document.getElementById('search_user').value;            
  var jwt = getCookie("jwt");
  var i = ((p-1)*per);
  $.ajax({
    type: "POST",
    url: "api/data_user.php",
    data: {search:ss,perpage:per,page:p,jwt:jwt},
    success: function(result){
      var tt=`
      <table class="list-table table animate__animated animate__fadeIn" id="usertable" >
        <thead>
          <tr>
            <th class="text-center" style="width:5%">ลำดับ</th> 
            <th class="text-left">ชื่อ-สกุล</th>
            <th >หน่วยงาน</th>
            <th >UserName</th>
            <th >ประเภท</th>
            <th class="text-center">แก้ไข&nbsp;&nbsp;&nbsp;ลบ</th>                
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
      <div class="mb-3" id="pagination">
      `;              
      $("#tableuser").html(tt);      
      pagination_show(p,result.page_all,per,'showusertable'); //<<<<<<<< แสดงตัวจัดการหน้าข้อมูล Pagination >>const.js
      $.each(result.data, function (key, entry) {
        i++;
        listUserTable(entry.id,entry.firstname,entry.lastname,entry.id_depart,entry.depart,entry.username,entry.type,entry.tp,i); //<<<<< แสดงรายการทั้งหมด             
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

function listUserTable(id,fn,ln,idp,dp,un,tp,tpn,i){  //=========================== ฟังก์ชั่นเพิ่ม Row ตารางประเเภท
  var tableName = document.getElementById('usertable');
    var prev = tableName.rows.length;           
    var row = tableName.insertRow(prev);
    row.id = "row" + id;
    row.style.verticalAlign = "top";    
    var col1 = row.insertCell(0);
    var col2 = row.insertCell(1);
    var col3 = row.insertCell(2);
    var col4 = row.insertCell(3);
    var col5 = row.insertCell(4);
    var collast = row.insertCell(5);
    col1.innerHTML = `<div id="no" class="text-center">`+i+`</div>`;
    col2.innerHTML = `<div id="name` + id + `" class="text-left">`+fn+`&nbsp;`+ln+`</div>`;
    col3.innerHTML = `<div id="dp` + id + `" class="text-left">`+dp+`</div>`;
    col4.innerHTML = `<div id="u_name` + id + `" name="u_name` + id + `" class="text-left">`+un+`</div>`;
    col5.innerHTML = `<div id="type` + id + `" class="text-left">`+tpn+`</div>`;
    collast.innerHTML = `
    <input type="hidden" id="fname` + id + `" name="fname` + id + `" value="` + fn + `" />
    <input type="hidden" id="lname` + id + `" name="lname` + id + `" value="` + ln + `" />
    <input type="hidden" id="id` + id + `" name="id` + id + `" value="` + id + `" />
    <input type="hidden" id="tp` + id + `" name="tp` + id + `" value="` + tp + `" />
    <input type="hidden" id="idp` + id + `" name="idp` + id + `" value="` + idp + `" />

    <i class="fas fa-edit me-3" onclick="editUserRow(` + id + `)" style="cursor:pointer; color:#5cb85c;"></i> 
    <i class="fas fa-trash-alt" onclick="deleteUserRow(` + id + `)" style="cursor:pointer; color:#d9534f;"></i>`;           
    collast.style = "text-align: center;";
}

function deleteUserRow(id){ //================================ ลบข้อมูลในตาราง
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
            url: "api/user_acc.php",
            type: "POST",
            contentType: "application/json",
            data: data,
            success: function(res) {
              swalWithBootstrapButtons.fire(
                  'ข้อมูลถูกลบ!',
                  'ข้อมูลของคุณได้ถูกลบออกจากระบบแล้ว!',
                  'success'
              );  
              showusertable(rowperpage,page_sel);
            },
            error: function(xhr, resp, text) {
                if (xhr.responseJSON.message == "Unable to delete User.") {
                    Signed("error", "ลบข้อมูลไม่สำเร็จ !="+xhr.responseJSON.code);
                } else if (xhr.responseJSON.message == "Unable to access User.") {
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

function editUserRow(id){ //============================== แก้ไขข้อมูลในตาราง       
  var id_depart = document.getElementById('idp'+id).value;
    var html = `          
    <div class="edit_user animate__animated animate__fadeIn mt-3 mb-4">
      <div style="text-align: center;">
        <i class="fas fa-user-edit fa-3x"></i>&nbsp;&nbsp;&nbsp;<a style="font-size: 2rem">ข้อมูลผู้ใช้งาน</a>   
      </div>  
      <form class="myForm mt-2" id="edit_user_form">
        <div class="row mb-2">    
          <div class="col-md-6 mb-2">                     
              <div class="form-group">
                <label for="firstname">ชื่อ :</label>
                <input type="text" class="form-control" name="firstname" id="firstname" maxlength="100" required value="` +
                document.getElementById('fname'+id).value +
                `">
              </div>
          </div>    
          <div class="col-md-6">
            <div class="form-group">
              <label for="lastname">นามสกุล :</label>
              <input type="text" class="form-control" name="lastname" id="lastname" maxlength="100" required value="` +
              document.getElementById('lname'+id).value +
              `">
            </div>
          </div>                  
        </div>

        <div class="row mb-2">
          <div class="col-md-6 mb-2">
            <div class="form-group">
              <label for="depart">หน่วยงาน :</label>
              <select class="form-select" name="depart" id="depart" required>
              </select>          
            </div> 
          </div> 
          <div class="col-md-6">       
            <div class="form-group">
              <label for="type">ประเภทผู้ใช้งาน :</label>
              <select class="form-select" name="type" id="type" required>
                <option value="0" >ทั่วไป</option>
                <option value="1" >เจ้าหน้าที่</option>
                <option value="2" >ผู้ดูแลระบบ</option>
              </select>    
            </div>   
          </div>
        </div>

        <div class="row">
            <div class="input-group mt-3 mb-2">    
              <div class="input-group-text" style="width: 100px;">User Name</div>
              <input type="text" class="form-control" name="username" id="username" maxlength="100" required value="` +
              document.getElementById('u_name'+id).innerText +
              `">
            </div>
            <input type="hidden" name="id" value="`+document.getElementById('id'+id).value+`">
        </div>
        
              <input type="hidden" name="password" value="nong_reset">
 
        <div class="row">
            <div class="input-group mt-1 mb-3">    
              <div class="input-group-text" style="width: 100px; background-color: aquamarine;">รหัสผ่านใหม่</div>
              <input type="password" class="form-control" name="newpassword" id="newpassword" maxlength="50">
            </div>              
        </div>            

        <div class="row mt-3 justify-content-center" >
            <button type="submit" class="btn btn-primary me-3" style="width :80px;">บันทึก</button>
            <button id="bt_cancel_edituser" type="button" class="btn btn-danger ms-3" style="width :80px;">ยกเลิก</button>
        </div>
      </form>
    </div>
      `;
      $("#edituser").html(html); 
      $.ajax({
        type: "POST",
        url: "api/getDropdown.php",
        data: {id:'',fn:'depart'},
        success: function(res){
          let dropdown = $('#depart');
          dropdown.empty();
          $.each(res, function (key, entry) {
            dropdown.append($('<option></option>').attr('value', entry.id_depart).text(entry.depart));
          })
          $("#depart option[value='"+id_depart+"']").attr("selected","selected");                             
        }
      });
      $("#type option[value='"+document.getElementById('tp'+id).value+"']").attr("selected","selected");

    
    $("#adduser").html("");  
    $("#tableuser").html("");              

}

//=========================== Even เกี่ยวกับ หมวดสินค้า ======================================
$(document).on('click',"#bt_search_user",function () {  //ค้นหาหมวดสินค้า
  $("#edituser").html("");
  $("#adduser").html("");
  $("#bt_add_user").show();
  showusertable(rowperpage,'1');          
});

$(document).on("click", "#bt_add_user", function() { // แสดงหน้าบันทึกเพิ่มข้อมูล  
  var html = `          
        <div class="add_user animate__animated animate__fadeIn mt-3 mb-4">
          <div style="text-align: center;">
            <i class="fas fa-user-plus fa-3x"></i>&nbsp;&nbsp;&nbsp;<a style="font-size: 2rem">เพิ่มผู้ใช้งาน</a>   
          </div> 
          <form class="myForm mt-2" id="add_user_form">
            <div class="row mb-2">    
              <div class="col-md-6 mb-2">                     
                  <div class="form-group">
                    <label for="firstname">ชื่อ :</label>
                    <input type="text" class="form-control" name="firstname" id="firstname" maxlength="100" required>
                  </div>
              </div>    
              <div class="col-md-6">
                <div class="form-group">
                  <label for="lastname">นามสกุล :</label>
                  <input type="text" class="form-control" name="lastname" id="lastname" maxlength="100" required>
                </div>
              </div>                  
            </div>

            <div class="row mb-2">
              <div class="col-md-6 mb-2">
                <div class="form-group">
                  <label for="depart">หน่วยงาน :</label>
                  <select class="form-select" name="depart" id="depart" required>
                  </select>          
                </div> 
              </div> 
              <div class="col-md-6">       
                <div class="form-group">
                  <label for="type">ประเภทผู้ใช้งาน :</label>
                  <select class="form-select" name="type" id="type" required>
                    <option value="0" selected>ทั่วไป</option>
                    <option value="1" >เจ้าหน้าที่</option>
                    <option value="2" >ผู้ดูแลระบบ</option>
                  </select>    
                </div>   
              </div>
            </div>

            <div class="row">
                <div class="input-group mt-3 mb-1">    
                  <div class="input-group-text" style="width: 100px;">User Name</div>
                  <input type="text" class="form-control" name="username" id="username" maxlength="100" placeholder="" required>
                </div>
            </div>

            <div class="row">
                <div class="input-group mt-1 mb-3">    
                  <div class="input-group-text" style="width: 100px;">รหัสผ่าน</div>
                  <input type="password" class="form-control" name="password" id="password" maxlength="50"  required>
                </div>              
            </div>            

            <div class="row mt-3 justify-content-center" >
                <button type="submit" class="btn btn-primary me-3" style="width :80px;">บันทึก</button>
                <button id="bt_cancel_user" type="button" class="btn btn-danger ms-3" style="width :80px;">ยกเลิก</button>
            </div>
          </form>
        </div>
  
          `;
  $("#adduser").html(html);
  let dropdown = $('#depart');
      dropdown.empty();
      dropdown.append('<option value="" disabled>--เลือกหน่วยงาน--</option>');
      dropdown.prop('selectedIndex', 0);
      $.ajax({
                type: "POST",
                url: "api/getDropdown.php",
                data: {id:'',fn:'depart'},
                success: function(result){
                  $.each(result, function (key, entry) {
                    dropdown.append($('<option></option>').attr('value', entry.id_depart).text(entry.depart));
                  })                             
                }
              }); 
 
  $("#edituser").html("");
  $("#bt_add_user").hide();
  $("#tableuser").html("");  
});

$(document).on("click", "#bt_cancel_user", function() {  // ปิดฟอร์มเพิ่มข้อมูล
    $("#adduser").html("");
    $("#bt_add_user").show();
    showusertable(rowperpage,'1');
});

$(document).on("click", "#bt_cancel_edituser", function() {  // ปิดฟอร์มแก้ไขข้อมูล
  $("#edituser").html("");
  $("#bt_add_user").show();
  showusertable(rowperpage,'1');
});

$(document).on("submit", "#add_user_form", function() {   // บันทึกเพิ่มข้อมูล
    var add_form = $(this);
    var jwt = getCookie("jwt");
    var add_form_obj = add_form.serializeObject();
    add_form_obj.jwt = jwt;
    add_form_obj.acc = "add";
    var form_data = JSON.stringify(add_form_obj);
    $.ajax({
        url: "api/user_acc.php",
        type: "POST",
        contentType: "application/json",
        data: form_data,
        success: function(result) {
            $("#adduser").html("");
            $("#bt_add_user").show();
            Signed("success", " บันทึกข้อมูลสำเร็จ ");
            showusertable(rowperpage,'1');
        },
        error: function(xhr, resp, text) {
            if (xhr.responseJSON.message == "Unable to create User.") {
                Signed("error", " บันทึกข้อมูลไม่สำเร็จ ");
            } else if (xhr.responseJSON.message == "Username Exit.") {
                swalertshow('warning', 'บันทึกข้อมูลไม่สำเร็จ', 'UserName นี้มีอยู่แล้ว !');
            } else if (xhr.responseJSON.message == "Unable to access User.") {
                Signed("warning", "ปฏิเสธการเข้าใช้ โปรดลองใหม่!");
            }else{
              showLoginPage();
              Signed("warning", "ปฏิเสธการเข้าใช้ โปรดเข้าสู่ระบบก่อน!");
            }
        },
    });
    return false;
});

$(document).on("submit", "#edit_user_form", function() {   // แก้ไขข้อมูล
    var edit_form = $(this);
    var jwt = getCookie("jwt");
    var edit_form_obj = edit_form.serializeObject();
    edit_form_obj.jwt = jwt;
    edit_form_obj.acc = "up";
    var form_data = JSON.stringify(edit_form_obj);    
    $.ajax({
        url: "api/user_acc.php",
        type: "POST",
        contentType: "application/json",
        data: form_data,
        success: function(result) {
            $("#edituser").html("");
            Signed("success", "แก้ไขข้อมูลสำเร็จ ");
            showusertable(rowperpage,page_sel);
        },
        error: function(xhr, resp, text) {
            if (xhr.responseJSON.message == "Unable to update User.") {
                Signed("error", " แก้ไขข้อมูลไม่สำเร็จ ");
            } else if (xhr.responseJSON.message == "Username Exit.") {
                swalertshow('warning', 'แก้ไขข้อมูลไม่สำเร็จ', 'UserName นี้มีอยู่แล้ว !');
            } else if (xhr.responseJSON.message == "Unable to access User.") {
                Signed("warning", "ปฏิเสธการเข้าใช้ โปรดลองใหม่!");
            }else{
              showLoginPage();
              Signed("warning", "ปฏิเสธการเข้าใช้ โปรดเข้าสู่ระบบก่อน!");
            }
        },
    });
    return false;
});