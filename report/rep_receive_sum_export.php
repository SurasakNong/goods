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
  <!-- Export to Excel 
  <script type="text/javascript" src="https://unpkg.com/xlsx@0.15.1/dist/xlsx.full.min.js"></script>  -->
  <script lang="javascript" src="../node_modules/xlsx/dist/xlsx.full.min.js"></script> 

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
    var h_str1 = "วันที่&nbsp;&nbsp;"+to_dmY(datefm)+"&nbsp;&nbsp;ถึง&nbsp;&nbsp;"+to_dmY(dateto);
    var h_str2 = "ผู้รับ :&nbsp;"+dpsel_rec+"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+"ผู้ส่ง :&nbsp;"+dpsel_post+"&nbsp;&nbsp;&nbsp;&nbsp;";
    var h_str3 = "&nbsp;&nbsp;กรองข้อมูล :(&nbsp;"+sh+"&nbsp;)";

    var jwt = getCookie("jwt");
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
            <td align="center" width="120"><button id="printPageButton" title="ส่งออก Excel" onClick="ExportToExcel('xlsx');" style="width:40px; height:40px; margin-top:20px;"><i class="far fa-file-excel fa-2x"></i></button></td>
            <td align="center" width="120"><button id="closePageButton" title="ปิดหน้ารายงาน" onClick="close_window(); return false;" style="width:40px; height:40px; margin-top:20px;"><i class="fas fa-reply fa-2x"></i></button></td>
          </tr>    
        </table>
        <br>
        `;              
        $(".my_table").html(str);   
        
       $.each(result.data, function (key, entry) {          
          y++;
          yy++;
          if(y==1){ //=== หน้าแรก สร้างหัวตาราง
            let data_t = document.createElement('table'); //=== สร้างตารางใหม่
            data_t.id = 'data_table';
            data_t.setAttribute('width','700');
            data_t.setAttribute('border','0');
            data_t.setAttribute('align','center'); 
            data_t.setAttribute('cellpadding','0');
            data_t.setAttribute('cellspacing','0'); 
            data_t.setAttribute('style','margin-bottom : 50px;'); 
            let parentDiv = document.getElementById('data_title').parentNode
            parentDiv.appendChild(data_t);       //=== เพิ่มตารางที่สร้างใหม่เข้าไป
            show_head(h_str1,h_str2,h_str3);
            show_hTable();  //==== แสดงหัวตาราง
          }
          yy = (entry.key_p != 'data')?yy-1:yy; //=== กำหนดลำดับที่แสดง
          if(entry.key_p == 'data'){
            show_dataTable(yy,entry); //=== แสดงข้อมูลในตาราง     
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

  function show_head(str1,str2,str3){  //========= ฟังก์ชั่นเพิ่ม    
      var tableName = document.getElementById('data_table');
      var prev = tableName.rows.length; 
      var row1 = tableName.insertRow(prev);
      var row2 = tableName.insertRow(prev+1);
      var row3 = tableName.insertRow(prev+2);
      row1.style.verticalAlign = "top";
      row2.style.verticalAlign = "top"; 
      row3.style.verticalAlign = "top";
      row1.innerHTML = `<th align="center" class="headTitle" colspan="5">รายงานสรุปการรับเข้า</th>`;
      row2.innerHTML = `<th colspan="5" align="center" class="headTitle2">${str1}</th>`;
      row3.innerHTML = `<th colspan="5" align="center" class="headTitle2">${str2} ${str3}</th>`;      
  }

  function show_hTable(){  //========= ฟังก์ชั่นเพิ่ม หัวตาราง    
      var tableName = document.getElementById('data_table');
      var prev = tableName.rows.length;           
      var row = tableName.insertRow(prev);
      row.style.verticalAlign = "top";    
      row.innerHTML = `
        <th class="text-center tb_bold">ลำดับ</th> 
        <th class="text-center tb_bold">Code</th>
        <th class="text-center tb_bold">สเปค</th>
        <th class="text-center tb_bold">ประเภท</th>
        <th class="text-center tb_bold">รายการ</th>
        <th class="text-end tb_bold">จำนวน(ผืน)</th>  
      `;
      
  }

  function show_dataTable(yy,ob){  //========= ฟังก์ชั่นเพิ่ม Row ตาราง    
    var tableName = document.getElementById('data_table');
    var prev = tableName.rows.length;           
    var row = tableName.insertRow(prev);    
    row.style.verticalAlign = "center";
    let n_col = 6;
    let col = [];
    for(let i=0; i<n_col; i++){
      col[i] = row.insertCell(i);
    }
    if(ob.key_p == 'data'){ //=========== แสดงข้อมูลในตาราง ==================================
      col[0].innerHTML = `<div class="nm" align="center">${yy}</div>`;
      col[1].innerHTML = `<div class="nm" align="center">${ob.code}</div>`;
      col[2].innerHTML = `<div class="nm" align="left">`+ob.spec+`</div>`;
      col[3].innerHTML = `<div class="nm" align="left">`+ob.search+`</div>`;
      col[4].innerHTML = `<div class="nm" align="center">`+(ob.nn*1).toFixed(0)+`</div>`;   
      col[5].innerHTML = `<div class="nm" align="center">`+(ob.pcs*1).toFixed(0)+`</div>`;  
           
    } 
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



function close_window() {
  //if (confirm("Close Report?")) {
    close();
  //}
}

function ExportToExcel(type, fn, dl) {
    var dd = Date.now();
    var elt = document.getElementById('data_table');
    var wb = XLSX.utils.table_to_book(elt, { sheet: "sheet1",raw:true });
    return dl ?
        XLSX.write(wb, { bookType: type, bookSST: true, type: 'base64' }) :
        XLSX.writeFile(wb, fn || ('ReceiveSumData_'+dd+'.' + (type || 'xlsx')));
}
</script>
  </body>

  </html>
