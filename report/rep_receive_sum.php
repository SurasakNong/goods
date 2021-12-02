<!doctype html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>รายงานรับเข้า</title>

  <link rel="shortcut icon" href="../image/Report.ico">
  <link rel="stylesheet" type="text/css" href="../css/report.css">
  <link rel="stylesheet" type="text/css" href="../css/printMe.css" media="print">
  <!-- Fonts awesome icons -->
  <link rel="stylesheet" href="../css/all.min.css" />
  <script src="../node_modules/jquery/dist/jquery.min.js"></script>
  <script src="../js/const.js"></script>
  <script>
    var all_data =0;
    var dataPerpage = 45;
    var n_page = 0;
    
  </script>

</head>

<body>
  <input id="date_fm" type="hidden" value="<?= isset($_POST['datefm'])?$_POST['datefm']:""; ?>"/>
  <input id="date_to" type="hidden" value="<?= isset($_POST['dateto'])?$_POST['dateto']:""; ?>"/>
  <input id="dp_rec_sel" type="hidden" value="<?= isset($_POST['dpsel_rec_rep'])?$_POST['dpsel_rec_rep']:"--"; ?>"/>
  <input id="dp_post_sel" type="hidden" value="<?= isset($_POST['dpsel_post_rep'])?$_POST['dpsel_post_rep']:"--"; ?>"/>
  <input id="depart_rec" type="hidden" value="<?= isset($_POST['dp_rec_rep'])?$_POST['dp_rec_rep']:""; ?>"/>
  <input id="depart_post" type="hidden" value="<?= isset($_POST['dp_post_rep'])?$_POST['dp_post_rep']:""; ?>"/>
  <input id="search" type="hidden" value="<?= isset($_POST['search'])?$_POST['search']:"--"; ?>"/>

  <div class="my_table" align="center"><br><i class="fas fa-spinner fa-pulse fa-2x"></i>&nbsp;&nbsp; กำลังโหลดข้อมูล.....</div> 

<script> 
  window.onload = function(){
    var datefm = to_Ymd($("#date_fm").val());
    var dateto = to_Ymd($("#date_to").val());
    var dpsel_rec = $("#dp_rec_sel").val();
    var dpsel_post = $("#dp_post_sel").val();
    var dp_rec = $("#depart_rec").val();
    var dp_post = $("#depart_post").val();
    var sh = $("#search").val();

    var jwt = getCookie("jwt");
    var x = 1; //=== ลำดับหน้า
    var y = 0; //=== ลำดับบรรทัด
    var yy = 0; //=== เลขลำดับ
    $.ajax({
      type: "POST", 
      url: "../api/data_receive_sum_rep.php",
      data: {search:sh,datefm:datefm,dateto:dateto,dp_rec:dp_rec,dp_post:dp_post,jwt:jwt},
      success: function(result){
        var str=`
        <table width="700" border="0" align="center" cellpadding="0" cellspacing="0" id="data_title">
          <tr>
            <td align="center" width="120"><button id="printPageButton" title="พิมพ์รายงาน" onClick="printpage();" style="width:40px; height:40px; margin-top:20px;"><i class="fas fa-print fa-2x"></i></button></td>
            <td align="center" class="headTitle" id="head_rep">รายงาน (------)</td>
            <td align="center" width="120"><button id="closePageButton" title="ปิดหน้ารายงาน" onClick="close_window(); return false;" style="width:40px; height:40px; margin-top:20px;"><i class="fas fa-reply fa-2x"></i></button></td>
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

        $("#head_rep").html("รายงานสรุปการรับเข้า");
        $("#date_rep").html("วันที่&nbsp;&nbsp;"+to_dmY(datefm)+"&nbsp;&nbsp;ถึง&nbsp;&nbsp;"+to_dmY(dateto));
        var dp_rec_seltxt = (dpsel_rec!="")?"ผู้รับ :&nbsp;"+dpsel_rec+"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;":"";
        var dp_post_seltxt = (dpsel_post!="")?"ผู้ส่ง :&nbsp;"+dpsel_post+"&nbsp;&nbsp;&nbsp;&nbsp;":"";
        $("#head2_rep").html(dp_rec_seltxt+dp_post_seltxt+"&nbsp;&nbsp;กรองข้อมูล :(&nbsp;"+sh+"&nbsp;)");

         all_data = result.data.length; //=== จำนวนข้อมูลทั้งหมด
         dataPerpage = 50; //=== จำนวนข้อมูลต่อหน้า
         n_page = Math.ceil(all_data/dataPerpage); //=== จำนวนหน้าทั้งหมด
       $.each(result.data, function (key, entry) {          
          y++;
          yy++;
          if(x==1 && y==1){ //=== หน้าแรก สร้างหัวตาราง
            let data_t = document.createElement('table'); //=== สร้างตารางใหม่
            data_t.id = 'data_table'+x;
            data_t.setAttribute('width','700');
            data_t.setAttribute('border','0');
            data_t.setAttribute('align','center'); 
            data_t.setAttribute('cellpadding','0');
            data_t.setAttribute('cellspacing','0'); 
            let parentDiv = document.getElementById('data_title').parentNode
            parentDiv.appendChild(data_t);       //=== เพิ่มตารางที่สร้างใหม่เข้าไป
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
              data_t.setAttribute('width','700');
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
            Signed("warning", "โปรดเข้าสู่ระบบก่อน !");  
            close_window();               
          }else{
            Signed("warning", "โปรดเข้าสู่ระบบก่อน !");  
            close_window();      
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
        <th class="text-center tb_bold">ลำดับ</th> 
        <th class="text-center tb_bold">Code</th>
        <th class="text-center tb_bold">สเปค</th>
        <th class="text-center tb_bold">รายการ</th>
        <th class="text-end tb_bold">จำนวน(ผืน)</th>  
      `;
      
  }


  function show_dataTable(x,yy,ob){  //========= ฟังก์ชั่นเพิ่ม Row ตาราง    
    var tableName = document.getElementById('data_table'+x);
    var prev = tableName.rows.length;           
    var row = tableName.insertRow(prev);    
    row.style.verticalAlign = "center";
    let n_col = 5;
    let col = [];
    let nn = 0;
    let grouprow = '';
    let line_t = '';
    if(ob.key_p == 'gr2'){
        row.setAttribute('style','margin-bottom:5px;')        
      }
    if(ob.key_p == 'gr1'){
      n_col--;
      grouprow = `<div class="tb_bold_2" align="center">รวม ${ob.search}</div>`;
    }else if(ob.key_p == 'gr2'){
      n_col--;
      grouprow = `<div class="tb_bold_2" align="center" style="background-color: #d7d7d7; font-weight: bold;">รวม ${ob.search}</div>`;
      
    }else if(ob.key_p == 'all'){
      n_col--;
      grouprow = `<div class="tb_bold_2" align="center" style="background-color: #d7d7d7; font-weight: bold;">รวมทั้งหมด</div>`;
    }    
    
    for(let i=0; i<n_col; i++){
      col[i] = row.insertCell(i);
    }
    if(ob.key_p == 'data'){ //=========== แสดงข้อมูลในตาราง ==================================
      line_t = ' class="nm" ';
      col[0].innerHTML = `<div class="nm" align="center">${yy}</div>`;
      col[1].innerHTML = `<div class="nm" align="center">${ob.code}</div>`;
      col[2].innerHTML = `<div class="nm" align="left">`+ob.spec+`</div>`;
      nn = 2;      
    }else{      
      if(ob.key_p == 'gr1'){
        line_t = ' class="tb_bold_2" ';
      }else if(ob.key_p == 'gr2'){
        line_t = ' class="tb_bold_2" style="background-color: #d7d7d7; font-weight: bold;" ';
      }else if(ob.key_p == 'all'){
        line_t = ' class="tb_bold_2" style="background-color: #d7d7d7; font-weight: bold;" ';
      }
      col[nn].setAttribute('colspan','3');
      col[nn].innerHTML = grouprow;   

    } 
      col[nn+1].innerHTML = `<div `+line_t+` align="center">`+addCommas((ob.nn*1).toFixed(0))+`</div>`;   
      col[nn+2].innerHTML = `<div `+line_t+` align="center">`+addCommas((ob.pcs*1).toFixed(0))+`</div>`;   
} //=========================================================================================

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
  data_f.setAttribute('width','700');
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
        <td colspan="2" align="center" class="FooterR">ผู้รับ...............................</td>
        <td colspan="2" align="center" class="FooterR">ผู้ส่ง...........................</td>
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
