
function show_prod_table(){ //========================== แสดงค้นหา และปุ่มเพิ่ม หมวดรายการ
    var html = `
  <div class="container animate__animated animate__fadeIn">
    <div class="row">                
        <div class="col-lg-6 mx-auto mt-3">
            <form id="fmsearch_prod">
                <div class="input-group mb-2">
                    <input type="text" id="search_prod" name="search_prod" class="form-control" placeholder="คำค้นหา.." aria-label="Search" aria-describedby="button-search">
                    <button class="btn btn-success" type="button" id="bt_search_prod" name="bt_search_prod" title="ค้นหา"><i class="fas fa-search"></i></button>
                    <button class="btn btn-primary ms-2" id="bt_add_prod" name="bt_add_prod" style="width: 42px;" type="button" title="เพิ่มข้อมูล"><i class="fas fa-plus"></i></button>
                    <button class="btn btn-warning ms-2" id="bt_back" name="bt_back" type="button" title="กลับ"><i class="fas fa-reply"></i></button>
                </div>
            </form>
        </div>
    </div>   
    <div class="row">  
        <div class="col-lg-6 mx-auto" id="add_prod"></div>
    </div>   
    <div class="row">  
        <div class="col-lg-6 mx-auto" id="edit_prod"></div>
    </div>   
    <div class="row">  
        <div class="col-lg-8 mx-auto" id="table_prod"></div>
    </div>
  </div>
    `;
    $("#content").html(html);
    showprodtable(rowperpage,page_sel); //<<<<<< แสดงตาราง
}

function showprodtable(per,p){ //======================== แสดงตาราง
  var ss = document.getElementById('search_prod').value;            
  var jwt = getCookie("jwt");
  var i = ((p-1)*per);
  $.ajax({
    type: "POST", 
    url: "api/data_product.php",
    data: {search:ss,perpage:per,page:p,jwt:jwt},
    success: function(result){
      var tt=`
      <table class="list-table table animate__animated animate__fadeIn" id="prodtable" >
        <thead>
          <tr>
            <th class="text-center" style="width:5%">ลำดับ</th> 
            <th class="text-left">Code</th>
            <th class="text-center">สเปค</th>
            <th class="text-left">ป้าย</th>
            <th class="text-left">ผืน</th>
            <th class="text-left">ก.ก/ผืน</th>
            <th class="text-left">ประเภท</th>
            <th class="text-center">แก้ไข&nbsp;&nbsp;&nbsp;ลบ</th>                
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
      <div class="mb-3" id="pagination">
      `;              
      $("#table_prod").html(tt);      
      pagination_show(p,result.page_all,per,'showprodtable'); //<<<<<<<< แสดงตัวจัดการหน้าข้อมูล Pagination >>const.js      
      $.each(result.data, function (key, entry) {
        i++;
        listprodTable(entry,i); //<<<<< แสดงรายการทั้งหมด             
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

function listprodTable(ob,i){  //=========================== ฟังก์ชั่นเพิ่ม Row ตารางประเเภท
  let tableName = document.getElementById('prodtable');
    let prev = tableName.rows.length;           
    let row = tableName.insertRow(prev);
    row.id = "row" + ob.id_prod;
    row.style.verticalAlign = "top";
    let txtDel = `<i class="fas fa-trash-alt" style="cursor:not-allowed; color:#939393;"></i>`;
    if(u_type == "2"){
      txtDel = `<i class="fas fa-trash-alt" onclick="delete_prod_Row(` + ob.id_prod + `)" style="cursor:pointer; color:#d9534f;"></i>`;
    }
    let n_col = 8;
    let col = [];
    for(let ii=0; ii<n_col; ii++){
      col[ii] = row.insertCell(ii);
    }
    let spect = ob.dia+' '+ob.color+' '+ob.knot+' '+ob.ms+ob.ms_unit+'x'+ob.md+ob.md_unit+'x'+ob.ml+ob.ml_unit;
    col[0].innerHTML = `<div id="no" class="text-center">`+i+`</div>`;
    col[1].innerHTML = `<div id="code` + ob.id_prod + `" class="text-left">`+ob.code+`</div>`;
    col[2].innerHTML = `<div id="spect` + ob.id_prod + `" class="text-center">`+spect+`</div>`;
    col[3].innerHTML = `<div id="label` + ob.id_prod + `" class="text-left">`+ob.label+`</div>`;
    col[4].innerHTML = `<div id="pcs` + ob.id_prod + `" class="text-left">`+ob.pcs+`</div>`;
    col[5].innerHTML = `<div id="wt` + ob.id_prod + `" class="text-left">`+ob.wt+`</div>`;
    col[6].innerHTML = `<div id="searchtxt` + ob.id_prod + `" class="text-left">`+ob.search+`</div>`;
    col[n_col-1].innerHTML = `
    <input type="hidden" id="id_prod` + ob.id_prod + `" name="id_prod` + ob.id_prod + `" value="` + ob.id_prod + `" />
    <input type="hidden" id="dia` + ob.id_prod + `" name="dia` + ob.id_prod + `" value="` + ob.dia + `" />
    <input type="hidden" id="color` + ob.id_prod + `" name="color` + ob.id_prod + `" value="` + ob.color + `" />
    <input type="hidden" id="knot` + ob.id_prod + `" name="knot` + ob.id_prod + `" value="` + ob.knot + `" />
    <input type="hidden" id="ms` + ob.id_prod + `" name="ms` + ob.id_prod + `" value="` + ob.ms + `" />
    <input type="hidden" id="ms_unit` + ob.id_prod + `" name="ms_unit` + ob.id_prod + `" value="` + ob.ms_unit + `" />
    <input type="hidden" id="md` + ob.id_prod + `" name="md` + ob.id_prod + `" value="` + ob.md + `" />
    <input type="hidden" id="md_unit` + ob.id_prod + `" name="md_unit` + ob.id_prod + `" value="` + ob.ms_unit + `" />
    <input type="hidden" id="ml` + ob.id_prod + `" name="ml` + ob.id_prod + `" value="` + ob.ml + `" />
    <input type="hidden" id="ml_unit` + ob.id_prod + `" name="ml_unit` + ob.id_prod + `" value="` + ob.ml_unit + `" />

    <i class="fas fa-edit me-3" onclick="edit_prod_Row(` + ob.id_prod + `)" style="cursor:pointer; color:#5cb85c;"></i>
    `+txtDel; 
    col[n_col-1].style = "text-align: center;";
}

function delete_prod_Row(id){ //================================ ลบข้อมูลในตาราง
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
            url: "api/product_acc.php",
            type: "POST",
            contentType: "application/json",
            data: data,
            success: function(res) {
              swalWithBootstrapButtons.fire(
                  'ข้อมูลถูกลบ!',
                  'ข้อมูลของคุณได้ถูกลบออกจากระบบแล้ว!',
                  'success'
              );  
              showprodtable(rowperpage,page_sel);
            },
            error: function(xhr, resp, text) {
                if (xhr.responseJSON.message == "Unable to delete Code.") {
                    Signed("error", "ลบข้อมูลไม่สำเร็จ !="+xhr.responseJSON.code);
                } else if (xhr.responseJSON.message == "Unable to access Code.") {
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

function edit_prod_Row(id){ //============================== แก้ไขข้อมูลในตาราง    
    let id_prod = document.getElementById('id_prod'+id).value;
    let code = document.getElementById('code'+id).innerText;
    let dia = document.getElementById('dia'+id).value;
    let color = document.getElementById('color'+id).value;
    let knot = document.getElementById('knot'+id).value;
    let ms = document.getElementById('ms'+id).value;
    let ms_unit = document.getElementById('ms_unit'+id).value;
    let md = document.getElementById('md'+id).value;
    let md_unit = document.getElementById('md_unit'+id).value;
    let ml = document.getElementById('ml'+id).value;
    let ml_unit = document.getElementById('ml_unit'+id).value;
    let label = document.getElementById('label'+id).innerText;
    let pcs = document.getElementById('pcs'+id).innerText;
    let wt = document.getElementById('wt'+id).innerText;
    let searchtxt = document.getElementById('searchtxt'+id).innerText;
 
    var html = `          
    <div class="edit_prod animate__animated animate__fadeIn mt-3 mb-4">
      <div style="text-align: center;">
        <i class="far fa-edit fa-3x"></i>&nbsp;&nbsp;&nbsp;<a style="font-size: 2rem">แก้ไขข้อมูล</a>   
      </div>  
      <form class="myForm mt-2" id="edit_prod_form">
        <input type="hidden" name="id_prod" value="`+id_prod+`">
        <div class="row mb-3"> 
            <div class="form-group">
                <label for="code">Code :</label>
                <input type="text" class="form-control" name="code" id="code" maxlength="10" required value="` +
                code + `">
            </div>
        </div>
        <div class="row mb-2">    
          <div class="col-md-4 mb-2">                     
                <div class="form-group">
                    <label for="dia">เบอร์ด้าย :</label>
                    <input type="text" class="form-control" name="dia" id="dia" maxlength="100" required value="` +
                    dia + `">
                </div>
          </div>    
          <div class="col-md-4  mb-2">
                <div class="form-group">
                    <label for="color">สี :</label>
                    <input type="text" class="form-control" name="color" id="color" maxlength="10" required value="` +
                    color + `">        
                </div>     
          </div>                
          <div class="col-md-4  mb-2">
                <div class="form-group">
                    <label for="knot">เงื่อน :</label>
                    <select class="form-select" name="knot" id="knot" required>
                        <option value="ไม่ระบุ" selected>ไม่ระบุ</option>
                        <option value="SK" >SK</option>
                        <option value="DK" >DK</option>
                    </select>   
                </div>                     
          </div>     
        </div>

        <div class="row mb-2">    
          <div class="col-md-4 mb-2">                     
                <div class="form-group">
                    <label for="ms">ขนาดตา :</label>
                    <div class="input-group">
                        <input type="number" class="form-control" name="ms" id="ms" step="0.01" required value="` +
                        ms + `">     
                        <select class="form-select" name="ms_unit" id="ms_unit" style="background-color:#f8dec7;" required>
                            <option value="ไม่ระบุ" selected>ไม่ระบุ</option>
                            <option value="cm" >cm</option>
                            <option value="in" >in</option>
                            <option value="mm" >mm</option>
                            <option value="inc2" >in2</option>
                            <option value="mmsq" >mmsq</option>
                        </select> 
                    </div>                         
                </div> 
          </div>    
          <div class="col-md-4 mb-2">                     
                <div class="form-group">
                    <label for="md">ความลึก :</label>
                    <div class="input-group">
                        <input type="number" class="form-control" name="md" id="md" step="0.5" required value="` +
                        md + `">     
                        <select class="form-select" name="md_unit" id="md_unit" style="background-color:#f8dec7;" required>
                            <option value="ไม่ระบุ" selected>ไม่ระบุ</option>
                            <option value="md" >md</option>
                            <option value="cm" >cm</option>
                            <option value="ft" >ft</option>                        
                            <option value="mtr" >mtr</option>
                        </select> 
                    </div>                            
                </div> 
          </div>  
          <div class="col-md-4 mb-2">                     
                <div class="form-group">
                    <label for="ml">ความยาว :</label>
                    <div class="input-group">
                        <input type="number" class="form-control" name="ml" id="ml" step="0.01" required value="` +
                        ml + `">     
                        <select class="form-select" name="ml_unit" id="ml_unit" style="background-color:#f8dec7;" required>
                            <option value="ไม่ระบุ" selected>ไม่ระบุ</option>
                            <option value="mtr" >mtr</option>
                            <option value="ml" >ml</option> 
                            <option value="g" >g</option>
                            <option value="kg" >kg</option>
                            <option value="lb" >lb</option>
                            <option value="ft" >ft</option>
                            <option value="yds" >yds</option>
                            <option value="fthm" >fthm</option>
                            <option value="kt" >kt</option>
                        </select> 
                    </div>                         
                </div> 
          </div>                   
        </div>

        <div class="row mb-3">    
            <div class="col-md-4  mb-2">
                <div class="form-group">
                    <label for="label">ป้าย :</label>
                    <input type="text" class="form-control" name="label" id="label" maxlength="100" required value="` +
                    label + `">  
                </div>                     
            </div> 
          <div class="col-md-4 mb-2">                     
                <div class="form-group">
                    <label for="pcs">ผืน :</label>
                    <input type="number" class="form-control" name="pcs" id="pcs" step="1" required value="` +
                    pcs + `">
                </div>
          </div>    
          <div class="col-md-4  mb-2">
                <div class="form-group">
                    <label for="searchtxt">ก.ก./ผืน :</label>
                    <input type="number" class="form-control" name="wt" id="wt" min="0" max="100" step="0.001" required value="` +
                    wt + `">        
                </div>     
          </div>         
        </div>
        <div class="row mb-3">  
            <div class="col-md-12  mb-2">
                <div class="form-group">
                    <label for="searchtxt">ประเภท :</label>
                    <input type="text" class="form-control" name="searchtxt" id="searchtxt" maxlength="50" required value="` +
                    searchtxt + `">        
                </div>     
            </div>  
        </div>
        <div class="row mt-4 justify-content-center" >
            <button type="submit" class="btn btn-primary me-3" style="width :80px;">บันทึก</button>
            <button id="bt_cancel_editprod" type="button" class="btn btn-danger ms-3" style="width :80px;">ยกเลิก</button>
        </div>
      </form>
    </div>
      `;
      $("#edit_prod").html(html); 
      $("#knot option[value='"+knot+"']").attr("selected","selected");  
      $("#ms_unit option[value='"+ms_unit+"']").attr("selected","selected");  
      $("#md_unit option[value='"+md_unit+"']").attr("selected","selected");  
      $("#ml_unit option[value='"+ml_unit+"']").attr("selected","selected");  
    
    $("#add_prod").html("");  
    $("#table_prod").html("");              

}

//=========================== Even เกี่ยวกับ รายการ ======================================
$(document).on('click',"#bt_search_prod",function () {  //ค้นหารายการ
  $("#edit_prod").html("");
  $("#add_prod").html("");
  $("#bt_add_prod").show();
  showprodtable(rowperpage,'1');          
});

$(document).on("click", "#bt_add_prod", function() { // แสดงหน้าบันทึกเพิ่มข้อมูล  
  var html = `          
        <div class="add_prod animate__animated animate__fadeIn mt-3 mb-4">
          <div style="text-align: center;">
            <i class="fab fa-buffer fa-3x"></i>&nbsp;&nbsp;&nbsp;<a style="font-size: 2rem">เพิ่มข้อมูล</a>   
          </div> 
          <form class="myForm mt-2" id="add_prod_form">
            <div class="row mb-3"> 
                <div class="form-group">
                    <label for="code">Code :</label>
                    <input type="text" class="form-control" name="code" id="code" maxlength="10" required>
                </div>
            </div>
            <div class="row mb-2">    
                <div class="col-md-4 mb-2">                     
                    <div class="form-group">
                        <label for="dia">เบอร์ด้าย :</label>
                        <input type="text" class="form-control" name="dia" id="dia" maxlength="100" required>
                    </div>
                </div>    
                <div class="col-md-4  mb-2">
                    <div class="form-group">
                        <label for="color">สี :</label>
                        <input type="text" class="form-control" name="color" id="color" maxlength="10" required>        
                    </div>     
                </div>                
                <div class="col-md-4  mb-2">
                    <div class="form-group">
                        <label for="knot">เงื่อน :</label>
                        <select class="form-select" name="knot" id="knot" required>
                            <option value="ไม่ระบุ" selected>ไม่ระบุ</option>
                            <option value="SK" >SK</option>
                            <option value="DK" >DK</option>
                        </select>   
                    </div>                     
                </div>     
            </div>

            <div class="row mb-2">    
                <div class="col-md-4 mb-2">                     
                    <div class="form-group">
                        <label for="ms">ขนาดตา :</label>
                        <div class="input-group">
                            <input type="number" class="form-control" name="ms" id="ms" step="0.01" required>     
                            <select class="form-select" name="ms_unit" id="ms_unit" style="background-color:#f8dec7;" required>
                                <option value="ไม่ระบุ" selected>ไม่ระบุ</option>
                                <option value="cm" >cm</option>
                                <option value="in" >in</option>
                                <option value="mm" >mm</option>
                                <option value="inc2" >in2</option>
                                <option value="mmsq" >mmsq</option>
                            </select> 
                        </div>                         
                    </div> 
                </div>    
                <div class="col-md-4 mb-2">                     
                    <div class="form-group">
                        <label for="md">ความลึก :</label>
                        <div class="input-group">
                            <input type="number" class="form-control" name="md" id="md" step="0.5" required>     
                            <select class="form-select" name="md_unit" id="md_unit" style="background-color:#f8dec7;" required>
                                <option value="ไม่ระบุ" selected>ไม่ระบุ</option>
                                <option value="md" >md</option>
                                <option value="cm" >cm</option>
                                <option value="ft" >ft</option>                        
                                <option value="mtr" >mtr</option>
                            </select> 
                        </div>                            
                    </div> 
                </div>  
                <div class="col-md-4 mb-2">                     
                    <div class="form-group">
                        <label for="ml">ความยาว :</label>
                        <div class="input-group">
                            <input type="number" class="form-control" name="ml" id="ml" step="0.01" required>     
                            <select class="form-select" name="ml_unit" id="ml_unit" style="background-color:#f8dec7;" required>
                                <option value="ไม่ระบุ" selected>ไม่ระบุ</option>
                                <option value="mtr" >mtr</option>
                                <option value="ml" >ml</option> 
                                <option value="g" >g</option>
                                <option value="kg" >kg</option>
                                <option value="lb" >lb</option>
                                <option value="ft" >ft</option>
                                <option value="yds" >yds</option>
                                <option value="fthm" >fthm</option>
                                <option value="kt" >kt</option>
                            </select> 
                        </div>                         
                    </div> 
                </div>                   
            </div>

            <div class="row mb-3">    
                <div class="col-md-4  mb-2">
                    <div class="form-group">
                        <label for="label">ป้าย :</label>
                        <input type="text" class="form-control" name="label" id="label" maxlength="100" required>  
                    </div>                     
                </div> 
                <div class="col-md-4 mb-2">                     
                    <div class="form-group">
                        <label for="pcs">ผืน :</label>
                        <input type="number" class="form-control" name="pcs" id="pcs" step="1" required>
                    </div>
                </div>    
                <div class="col-md-4  mb-2">
                    <div class="form-group">
                        <label for="searchtxt">ก.ก./ผืน :</label>
                        <input type="number" class="form-control" name="wt" id="wt" min="0" max="100" step="0.001" required>        
                    </div>     
                </div>         
                </div>
                <div class="row mb-3">  
                    <div class="col-md-12  mb-2">
                        <div class="form-group">
                            <label for="searchtxt">ประเภท :</label>
                            <input type="text" class="form-control" name="searchtxt" id="searchtxt" maxlength="50" required>        
                        </div>     
                    </div>  
                </div>


            <div class="row mt-3 justify-content-center" >
                <button type="submit" class="btn btn-primary me-3" style="width :80px;">บันทึก</button>
                <button id="bt_cancel_prod" type="button" class="btn btn-danger ms-3" style="width :80px;">ยกเลิก</button>
            </div>
          </form>
        </div>
  
          `;
  $("#add_prod").html(html); 
  $("#edit_prod").html("");
  $("#bt_add_prod").hide();
  $("#table_prod").html("");  
});

$(document).on("click", "#bt_cancel_prod", function() {  // ปิดฟอร์มเพิ่มข้อมูล
    $("#add_prod").html("");
    $("#bt_add_prod").show();
    showprodtable(rowperpage,'1');
});

$(document).on("click", "#bt_cancel_editprod", function() {  // ปิดฟอร์มแก้ไขข้อมูล
  $("#edit_prod").html("");
  $("#bt_add_prod").show();
  showprodtable(rowperpage,'1');
});

$(document).on("submit", "#add_prod_form", function() {   // บันทึกเพิ่มข้อมูล
    var add_form = $(this);
    var jwt = getCookie("jwt");
    var add_form_obj = add_form.serializeObject();
    add_form_obj.jwt = jwt;
    add_form_obj.acc = "add";
    var form_data = JSON.stringify(add_form_obj);
    $.ajax({
        url: "api/product_acc.php",
        type: "POST",
        contentType: "application/json",
        data: form_data,
        success: function(result) {
            $("#add_prod").html("");
            $("#bt_add_prod").show();
            Signed("success", " บันทึกข้อมูลสำเร็จ ");
            showprodtable(rowperpage,'1');
        },
        error: function(xhr, resp, text) {
            if (xhr.responseJSON.message == "Unable to create Code.") {
                Signed("error", " บันทึกข้อมูลไม่สำเร็จ ");
            } else if (xhr.responseJSON.message == "Code Exit.") {
                swalertshow('warning', 'บันทึกข้อมูลไม่สำเร็จ', 'ชื่อรหัส นี้มีอยู่แล้ว !');
            } else if (xhr.responseJSON.message == "Unable to access Code.") {
                Signed("warning", "ปฏิเสธการเข้าใช้ โปรดลองใหม่!");
            }else{
              showLoginPage();
              Signed("warning", "ปฏิเสธการเข้าใช้ โปรดเข้าสู่ระบบก่อน!");
            }
        },
    });
    return false;
});

$(document).on("submit", "#edit_prod_form", function() {   // แก้ไขข้อมูล
    var edit_form = $(this);
    var jwt = getCookie("jwt");
    var edit_form_obj = edit_form.serializeObject();
    edit_form_obj.jwt = jwt;
    edit_form_obj.acc = "up";
    var form_data = JSON.stringify(edit_form_obj);    
    $.ajax({
        url: "api/product_acc.php",
        type: "POST",
        contentType: "application/json",
        data: form_data,
        success: function(result) {
            $("#edit_prod").html("");
            Signed("success", "แก้ไขข้อมูลสำเร็จ ");
            showprodtable(rowperpage,page_sel);
        },
        error: function(xhr, resp, text) {
            if (xhr.responseJSON.message == "Unable to update Code.") {
                Signed("error", " แก้ไขข้อมูลไม่สำเร็จ ");
            } else if (xhr.responseJSON.message == "Code Exit.") {
                swalertshow('warning', 'แก้ไขข้อมูลไม่สำเร็จ', 'ชื่อรหัสนี้ นี้มีอยู่แล้ว !');
            } else if (xhr.responseJSON.message == "Unable to access Code.") {
                Signed("warning", "ปฏิเสธการเข้าใช้ โปรดลองใหม่!");
            }else{
              showLoginPage();
              Signed("warning", "ปฏิเสธการเข้าใช้ โปรดเข้าสู่ระบบก่อน!");
            }
        },
    });
    return false;
});