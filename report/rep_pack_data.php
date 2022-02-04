<!doctype html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>รายงานมิเตอร์</title>

  <link rel="shortcut icon" href="../image/Report.ico">
  <link rel="stylesheet" type="text/css" href="../css/report.css">
  <link rel="stylesheet" type="text/css" href="../css/printMe.css" media="print">
  <!-- Fonts awesome icons -->
  <link rel="stylesheet" href="../css/all.min.css" />
  <script src="../node_modules/jquery/dist/jquery.min.js"></script>
  <script src="../js/const.js"></script> 
  <script>
    const w_page = 750; //=== A4 แนวตั้ง=750 , A4 แนวนอน=1050
    var all_data =0;
    var dataPerpage = 50; //=== จำนวนบรรทัดต่อหน้า
    var n_page = 0;
    
  </script>

</head>

<body>
  <input id="date_fm" type="hidden" value="<?= isset($_POST['datefm'])?$_POST['datefm']:""; ?>"/>
  <input id="date_to" type="hidden" value="<?= isset($_POST['dateto'])?$_POST['dateto']:""; ?>"/>
  <input id="dp_select" type="hidden" value="<?= isset($_POST['dp_rec_rep'])?$_POST['dp_rec_rep']:"--"; ?>"/>
  <input id="depart" type="hidden" value="<?= isset($_POST['dpsel_rec_rep'])?$_POST['dpsel_rec_rep']:""; ?>"/>
  <input id="search" type="hidden" value="<?= isset($_POST['search'])?$_POST['search']:"--"; ?>"/>

  <div class="my_table" align="center"><br><i class="fas fa-spinner fa-pulse fa-2x"></i>&nbsp;&nbsp; กำลังโหลดข้อมูล.....</div> 

<script> 
  window.onload = function(){
    var datefm = to_Ymd($("#date_fm").val());
    var dateto = to_Ymd($("#date_to").val());
    var dpsel = $("#dp_select").val();
    var depart = $("#depart").val();
    var sh = $("#search").val();

    var jwt = getCookie("jwt");
    
    var x = 1; //=== ลำดับหน้า
    var y = 0; //=== ลำดับบรรทัด
    var yy = 0; //=== เลขลำดับ
    $.ajax({
      type: "POST", 
      url: "../api/data_pack_rep.php",
      data: {search:sh,datefm:datefm,dateto:dateto,dpsel:dpsel,jwt:jwt},
      success: function(result){
        var str=`
        <table width="${w_page}" border="0" align="center" cellpadding="0" cellspacing="0" id="data_title">
          <tr>
            <td align="center" width="120"><button id="printPageButton" title="พิมพ์รายงาน" onClick="printpage();" style="width:40px; height:40px; margin-top:20px;"><i class="fas fa-print fa-2x"></i></button></td>
            <td align="center" class="headTitle" id="head_rep">รายงาน (------)</td>
            <td align="center" width="120"><button id="closePageButton" title="ปิดหน้ารายงาน" onClick="close_window(); return false;" style="width:40px; height:40px; margin-top:20px;"><i class="far fa-times-circle fa-2x"></i></button></td>
          </tr>
          <tr>
            <td colspan="3" align="center" class="headTitle2" id="date_rep">วันที่ : --- &nbsp;&nbsp;ถึง&nbsp;&nbsp; ---</td>
          </tr>
          <tr>
            <td colspan="3" align="center" class="headTitle2" id="head2_rep">หน่วยงาน :----&nbsp;&nbsp;&nbsp;&nbsp;คำค้นหา : ( &nbsp;---&nbsp; )</td>
          </tr>       
        </table>
        <br>
        `;              
        $(".my_table").html(str);      

        $("#head_rep").html("รายงาน การบรรจุ");
        $("#date_rep").html("วันที่&nbsp;&nbsp;"+to_dmY(datefm)+"&nbsp;&nbsp;ถึง&nbsp;&nbsp;"+to_dmY(dateto));
        var dpseltxt = (depart!="")?"หน่วยงาน :&nbsp;"+depart+"&nbsp;&nbsp;&nbsp;&nbsp;":"";
        $("#head2_rep").html(dpseltxt+"คำค้นหา :(&nbsp;"+sh+"&nbsp;)");

         all_data = result.data.length;       
         n_page = Math.ceil(all_data/dataPerpage); //=== จำนวนหน้าทั้งหมด
         console.log(result.data.length);
         console.log(result.data);
       $.each(result.data, function (key, entry) {          
          y++;
          yy++;
          if(x==1 && y==1){ //=== หน้าแรกให้สร้างหัวตาราง
            let data_t = document.createElement('table'); //=== สร้างตารางใหม่
            data_t.id = 'data_table'+x;
            data_t.setAttribute('width',w_page);
            data_t.setAttribute('border','0');
            data_t.setAttribute('align','center'); 
            data_t.setAttribute('cellpadding','0');
            data_t.setAttribute('cellspacing','0'); 
            let parentDiv = document.getElementById('data_title').parentNode
            parentDiv.appendChild(data_t);
            show_hTable(x);  //==== แสดงหัวตาราง
          }
          yy = (entry.key_p != 'data')?yy-1:yy; //=== กำหนดลำดับที่แสดง
          show_dataTable(x,yy,entry); //=== แสดงข้อมูลในตาราง
          if(x==n_page && y == all_data){ //=== หน้าสุดท้าย แสดงท้ายตาราง
            show_footrep();
          }          
          if(y%dataPerpage == 0){ //=== ทุกๆหน้า สร้างหัวตาราง
            x++;
            if(x<=n_page){
              let data_t = document.createElement('table'); //=== สร้างตารางใหม่
              data_t.id = 'data_table'+x;
              data_t.setAttribute('width',w_page);
              data_t.setAttribute('border','0');
              data_t.setAttribute('align','center'); 
              data_t.setAttribute('cellpadding','0');
              data_t.setAttribute('cellspacing','0'); 
              let parentDiv = document.getElementById('data_table'+(x-1)).parentNode
              let page_breake = document.createElement('div');
              page_breake.setAttribute('style','break-after:page');
              parentDiv.appendChild(page_breake); //=== ขึ้นหน้าใหม่
              parentDiv.appendChild(data_t);       //=== เพิ่มตารางที่สร้างใหม่เข้าไป
              show_hTable(x);  //==== แสดงหัวตาราง
            }
          }
            
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

  function show_hTable(x){  //========= ฟังก์ชั่นเพิ่ม หัวตาราง    
      var tableName = document.getElementById('data_table'+x);
      var prev = tableName.rows.length;           
      var row = tableName.insertRow(prev);
      row.style.verticalAlign = "top";    
      row.innerHTML = `
        <th class="text-center tlb_lite">ลำดับ</th> 
        <th class="text-center tlb_lite">เลขบิล</th>
        <th class="text-center tlb_lite">เลขบรรจุ</th>
        <th class="text-center tlb_lite">รหัส</th>
        <th class="text-center tlb_lite">รายละเอียด</th>
        <th class="text-center tlb_lite">รายการ</th>
        <th class="text-end tlb_lite">ผืน</th>
        <th class="text-end tlb_lite">ก.ก.</th>
        <th class="text-end tlbr_lite">ก.ก.(มตฐ.)</th>      
      `;
  }


  function show_dataTable(x,yy,ob){  //========= ฟังก์ชั่นเพิ่ม Row ตาราง    
    var tableName = document.getElementById('data_table'+x);
    var prev = tableName.rows.length;           
    var row = tableName.insertRow(prev);    
    row.style.verticalAlign = "center";
    let n_col = 9; //จำนวน Column ทั้งหมดในรายงาน
    let nn = 0;  
    let col = [];
    let line_t = '';
    
    for(let i=0; i<n_col; i++){
      col[i] = row.insertCell(i);
    }
    if(ob.key_p === 'data'){
      col[nn++].innerHTML = `<div class="lb_lite" align="center">${yy}</div>`;
      col[nn++].innerHTML = `<div class="lb_lite" align="center">${ob.pack_bill}</div>`;
      col[nn++].innerHTML = `<div class="lb_lite" align="center">${ob.bag_no}</div>`;
      col[nn++].innerHTML = `<div class="lb_lite" align="center">${ob.bag_name}</div>`;
      col[nn++].innerHTML = `<div class="lb_lite" align="center">${ob.bag_desc}</div>`;
    }else{     
      col[nn].setAttribute('colspan','5'); //ผสาน Column สำหรับบรรทัดรวม
      if(ob.key_p == 'gr1'){
        line_t = "t";
        //col[nn++].innerHTML = = `<div class="tlb_lite" align="center">รวมกลุ่ม ${ob.group_name}</div>`;
      }else if(ob.key_p === 'gr2'){
        line_t = "dt";
        //row.setAttribute('style','margin-bottom:5px;');
        //col[nn++].innerHTML = `<div class="dtlb_lite" align="center">รวม ${ob.bd_name}</div>`;
      }else if(ob.key_p === 'all'){
        line_t = "tt";
        col[nn++].innerHTML = `<div class="ttlb_lite" align="center" style="background-color: #d7d7d7;">รวมทั้งหมด</div>`;        
      }    
    }     
      col[nn++].innerHTML = `<div class="${line_t}lb_lite" align="right">`+addCommas((+ob.bag_amount).toFixed(0))+`</div>`;
      col[nn++].innerHTML = `<div class="${line_t}lb_lite" align="right">`+addCommas((+ob.bag_pcs).toFixed(0))+`</div>`;
      col[nn++].innerHTML = `<div class="${line_t}lb_lite" align="right">`+addCommas((+ob.bag_kg).toFixed(2))+`</div>`;
      col[nn].innerHTML = `<div class="${line_t}lbr_lite" align="right">`+addCommas((+ob.bag_std_kg).toFixed(2))+`</div>`;
       
    // Calculate variable for report

    
   /* */
}


function addCommas(nStr){ // ใส่คอมม่าให้ตัวเลข
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}
	return x1 + x2;
}

function show_footrep(){
  let parentDiv = document.getElementById('data_title').parentNode //=== สร้างตารางท้ายรายงาน
  let data_f = document.createElement('table');
  data_f.id = 'foot_table';
  data_f.setAttribute('width',w_page);
  data_f.setAttribute('border','0');
  data_f.setAttribute('align','center'); 
  data_f.setAttribute('cellpadding','0');
  data_f.setAttribute('cellspacing','0'); 
  data_f.innerHTML = ` 
      <tr>
        <td colspan="6" align="left">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="6" align="left">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="6" align="left">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2" align="center" class="FooterR">ผู้จัดทำ...............................</td>
        <td colspan="2" align="center" class="FooterR">ผู้รัตรวจสอบ...........................</td>
        <td colspan="2" align="center" class="FooterR">ผู้อนุมัติ...............................</td>
      </tr>
      <tr>
        <td colspan="6" align="left">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2" align="center" class="FooterR">( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</td>
        <td colspan="2" align="center" class="FooterR">( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</td>
        <td colspan="2" align="center" class="FooterR">( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</td>
      </tr>
      <tr>
        <td colspan="6" align="left">&nbsp;</td>
      </tr>`;
  parentDiv.appendChild(data_f);
}



function printpage() {
  var printButton = document.getElementById("printPageButton");
  var closeButton = document.getElementById("closePageButton");
  printButton.style.visibility = 'hidden';
  closeButton.style.visibility = 'hidden';
  window.print()
  printButton.style.visibility = 'visible';
  closeButton.style.visibility = 'visible';
}

function close_window() {
  //if (confirm("Close Report?")) {
    close();
  //}
}

</script>
  </body>

  </html>
