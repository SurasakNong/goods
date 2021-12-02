function show_first(){ //========================== แสดงหน้าหลัก
  var html = `
<div class="animate__animated animate__fadeIn">  
  <div class="fullscreen-block d-flex justify-content-center" >
      <h1 class="animate__animated animate__backInRight"> โปรแกรมบันทึกรับเข้าอวนสำเร็จรูป</h1>
      <h2 class="animate__animated animate__backInLeft"> บรรจุหีบห่อ (Packaging)</h2>
  </div>       
</div>
  `;
  $("#content").html(html);
  
}

function showHome() {  //==================================== show home page 
  // validate jwt to verify access          
  var jwt = getCookie("jwt");
  $.post("api/validate_token.php", JSON.stringify({ jwt: jwt }))
      .done(function (result) {
      u_type = result.data.type;
      var dp_name = result.data.departname;
      var nm_type="";
      if(u_type == 2){
        nm_type = "ผู้ดูแลระบบ"
      }else if(u_type == 1){
        nm_type = "เจ้าหน้าที่"
      }else{
        nm_type = "ผู้ใช้งานทั่วไป"
      }

      u_name = result.data.firstname +" "+ result.data.lastname;
      u_id = result.data.id;      
      $("#myMenu").show();
      $("#B_logout").show();  
      $("#content").html("");
      if(u_type == "2"){ //สำหรับ Addmin ผู้ดูแลระบบ จัดการได้ทุกอย่าง
        $("#add_account").show();
        $("#list_account").show();
        $("#data_menu").show();
      }else if(u_type == "1"){ //สำหรับเจ้าหน้าที่ จัดการข้อมูลพื้นฐานได้ และเพิ่มผู้ใช้งานทั่วไปได้
        $("#add_account").show();
        $("#list_account").hide();
        $("#data_menu").show();
      }else{ //สำหรับผู้ใช้งานทั่วไป จัดการข้อมูลตัวเอง และ ดูข้อมูล เท่านั้น
        $("#add_account").hide();
        $("#list_account").hide();
        $("#data_menu").hide();
      }
      $(".user_name").html(`<span class="navbar-text" style="font-size: 12px;" title="${nm_type} (${dp_name})"><i class="far fa-user"></i>&nbsp;&nbsp;`+u_name+`</span>`);      
      //show_rec_prod();    
      show_first(); 
        
      })
      // show login page on error
      .fail(function (result) {
      showLoginPage();
      Signed('info',' กรุณาเข้าสู่ระบบก่อน ');
      });
  }

function showLoginPage() {  //==================== show login page    
    setCookie("jwt", "", 1);// remove jwt
    $("#myMenu").hide();
    $("#B_logout").hide();       
    u_id = "";
    u_name = "";
    $(".user_name").html("");  
    
    var html = `          
        <div class="container">
          <form class="form-signin animate__animated animate__fadeIn" id='login_form'>
            <img class="mb-4" src="image/goods.png" alt="" width="100" height="100">
            <label for="username" class="sr-only">User Name</label>
            <input type="text" id="inputUsername" class="form-control" name="username" placeholder="User Name" required autofocus> 
            <label for="password" class="sr-only">Password</label>
            <input type="password" id="inputPassword" class="form-control" name="password" placeholder="Password" required>

            <button class="btn btn-lg btn-primary btn-block mb-4 mt-3" type="submit" name="submit" >Login</button>

            <p class="mt-4 mb-3 text-muted">&copy; FG&P : Finished Goods and Packaging @2021</p>
            <a class="mt-4 mb-3 text-muted">[ Version : 2111-1515 ]</a>
          </form>
        </div>
    
            `;
    $("#content").html(html);

}

function showRegPage() {  //==================== show registion page      
    var html = `          
    <div class="container">
      <div class="col-md-4 mx-auto">
        <div class="reg_user animate__animated animate__fadeIn mt-4 mb-4">
          <div class="row">
              <div style="text-align: center;">
                <i class="fas fa-user-plus fa-3x"></i>&nbsp;&nbsp;&nbsp;<a style="font-size: 2rem">เพิ่มผู้ใช้งาน</a>   
              </div>      
          </div>     
          <form class="myForm mt-2" id="create_user_form">
            <div class="row">
                <div class="form-group mb-3">
                  <label for="shopname">ชื่อ :</label>
                  <input type="text" class="form-control" name="firstname" id="firstname" maxlength="100" required>
                </div>                     
            </div>

            <div class="row">
                <div class="form-group mb-3">
                  <label for="shopname">นามสกุล :</label>
                  <input type="text" class="form-control" name="lastname" id="lastname" maxlength="100" required>
                </div>
            </div>

            <div class="row">
              <div class="form-group">
                  <label for="depart">หน่วยงาน :</label>
                  <select class="form-select" name="depart" id="depart" required>
                  </select>
              </div>
            </div>

            <div class="row">
                <div class="input-group mt-3 mb-3">    
                  <div class="input-group-text">User Name</div>
                <input type="text" class="form-control" name="username" id="username" maxlength="100" placeholder="" required>
              </div>
            </div>

            <div class="row justify-content-center mt-4">
                <button type="submit" class="btn btn-primary me-3" style="width :80px;">บันทึก</button>
                <button id="bt_cancel" type="button" class="btn btn-danger ms-3" style="width :80px;">ยกเลิก</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    
            `;
    $("#content").html(html);
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

}

function userUpdate() {  //==================== show update page    
  
  // validate jwt to verify access
  var jwt = getCookie("jwt");
  $.post("api/validate_token.php", JSON.stringify({ jwt: jwt }))
      .done(function (result) {
      var html = `
  <div class="container">
    <div class="col-md-4 mx-auto">
      <div class="update_user animate__animated animate__fadeIn mt-4 mb-4">
        <div class="row">
            <div style="text-align: center;">
              <i class="fas fa-user-edit fa-3x"></i>&nbsp;&nbsp;&nbsp;<a style="font-size: 2rem">ข้อมูลผู้ใช้งาน</a>   
            </div>  
        </div> 
        <form class="myForm mt-2" id="update_user_form">
          <div class="row">
              <div class="form-group mb-3">
                <label for="firstname">ชื่อ :</label>
                <input type="text" class="form-control" name="firstname" id="firstname" maxlength="100" required value="` +
                result.data.firstname +
                `">
              </div>                     
          </div>
  
          <div class="row">
              <div class="form-group mb-3">
                <label for="lastname">นามสกุล :</label>
                <input type="text" class="form-control" name="lastname" id="lastname" maxlength="100" required value="` +
                result.data.lastname +
                `">
              </div>
          </div>
  
          <div class="row">
            <div class="form-group">
              <label for="depart">หน่วยงาน :</label>
              <select class="form-select" name="depart" id="depart" required>
              </select>        
            </div>
          </div>

          <div class="row">
              <div class="input-group mt-3">    
                <div class="input-group-text" style="width: 100px;">User Name</div>
                <input type="text" class="form-control" name="username" id="username" maxlength="100" required value="` +
                result.data.username +
                `">
              </div>
              <input type="hidden" name="id" value="`+result.data.id+`">
              <input type="hidden" name="type" value="`+result.data.type+`">
          </div>

          <div class="row">
              <div class="input-group mt-2">    
                <div class="input-group-text" style="width: 100px; background-color: aquamarine;">รหัสผ่านเดิม</div>
                <input type="password" class="form-control" name="password" id="password" maxlength="50">
              </div>                 
          </div>
          <div class="row">
              <div class="input-group mt-1 mb-2">    
                <div class="input-group-text" style="width: 100px; background-color: aquamarine;">รหัสผ่านใหม่</div>
                <input type="password" class="form-control" name="newpassword" id="newpassword" maxlength="50">
              </div>                 
          </div>
  
          <div class="row justify-content-center mt-4">
              <button type="submit" class="btn btn-primary me-3" style="width :80px;">บันทึก</button>
              <button id="bt_cancel" type="button" class="btn btn-danger ms-3" style="width :80px;">ยกเลิก</button>
          </div>
        </form>
        
      </div>
    </div>
  </div>
          `;
      $("#content").html(html);

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
          $("#depart option[value='"+result.data.depart+"']").attr("selected","selected");                             
        }
      });

      })
      // on error/fail, tell the user he needs to login to show the account page
      .fail(function (result) {
      showLoginPage();
      Signed('warning','กรุณาเข้าสู่ระบบก่อน ')
      });
}



$(document).on("click", "#bt_cancel", function () {
    showHome();    
});

$(document).on("submit", "#login_form", function () { //======= ทำการเข้าสู่ระบบ
  // get form data
  var login_form = $(this);
  var form_data = JSON.stringify(login_form.serializeObject());
  // submit form data to api
  $.ajax({
    url: "api/login.php",
    type: "POST",
    contentType: "application/json",
    data: form_data,
    success: function (result) {
      
      // store jwt to cookie
      setCookie("jwt", result.jwt, 1);
      // show home page & tell the user it was a successful login
      showHome();          
      Signed("success"," เข้าสู่ระบบสำเร็จ ");
    },
    error: function (xhr, resp, text) {
      // on error, tell the user login has failed & empty the input boxes
      swalertshow('warning','เข้าสู่ระบบไม่สำเร็จ','ชื่อ หรือ รหัสผ่านไม่ถูกต้อง');              
      login_form.find("input[type=password]").val("");              
    },
  });
  return false;
});

$(document).on("submit", "#update_user_form", function () { //===== ทำการแก้ไขข้อมูลผู้ใชงาน
  var update_account_form = $(this);
  var jwt = getCookie("jwt");
  var update_account_form_obj = update_account_form.serializeObject();
  update_account_form_obj.jwt = jwt;
  // convert object to json string
  var form_data = JSON.stringify(update_account_form_obj);       
    $.ajax({
      url: "api/update_user.php",
      type: "POST",
      contentType: "application/json",
      data: form_data,
      success: function (result) {
        Signed("success"," ปรับปรุงข้อมูลผู้ใช้งานสำเร็จ ");        
        // store new jwt to coookie
        setCookie("jwt", result.jwt, 1);
        showHome();   
      },
      error: function (xhr, resp, text) {
        if (xhr.responseJSON.message == "Unable to update user.") {
          Signed("error"," ปรับปรุงบัญชีไม่สำเร็จ ");
        } else if (xhr.responseJSON.message == "This username to used.") {  
          swalertshow('warning','ปรับปรุงข้อมูลไม่สำเร็จ','Username มีผู้อื่นใช้งานแล้ว !');  
        } else if (xhr.responseJSON.message == "Invalid password.") {  
          swalertshow('warning','ปรับปรุงข้อมูลไม่สำเร็จ','รหัสผ่านเดิมไม่ถูกต้อง !');  
        } else if (xhr.responseJSON.message == "Access denied.") {
          showLoginPage();
          Signed("warning","ปฏิเสธการเข้าใช้ โปรดเข้าสู่ระบบก่อน");
        }
      },
    });
  return false;
});

$(document).on("submit", "#create_user_form", function () {  //===== บันทึกลงทะเบียนผู้ใช้งาน
// get form data
var create_user_form = $(this);
var form_data = JSON.stringify(create_user_form.serializeObject());
// submit form data to api
    $.ajax({
        url: "api/create_user.php",
        type: "POST",
        contentType: "application/json",
        data: form_data,
        success: function (result) {
        console.log(result);
        Signed('success',' ลงทะเบียนสำเร็จแล้ว.. โปรดเข้าสู่ระบบ ');
        showLoginPage();
        create_user_form.find("input").val("");
        },
        error: function (xhr, resp, text) {
        if (xhr.responseJSON.message == "Unable to create user.") {
            swalertshow("error","ลงทะเบียน ไม่สำเร็จ","โปรดตรวจสอบ หรือลองใหม่อีกครั้ง !");
        } else if (xhr.responseJSON.message == "Username Exit.") {  
            swalertshow("error","ลงทะเบียน ไม่สำเร็จ","Username นี้มีการใช้ลงทะเบียนไว้แล้ว !");           
        }
        },
    });
return false;
}); 
