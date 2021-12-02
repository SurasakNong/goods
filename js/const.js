//===== my homepage
var home = "http://192.168.50.230:8092/goods";
//=== ฟังก์ชัน GET()
var _get = function(val){
    var result = null; // กำหนดค่าเริ่มต้นผลลัพธ์
        tmp = []; // กำหนดตัวแปรเก็บค่า เป็น array
        // เก็บค่า url โดยตัด ? อันแรกออก แล้วแยกโดยตัวแบ่ง &
    var items = location.search.substr(1).split("&"); 
    for(var index = 0; index < items.length; index++) { // วนลูป
        tmp = items[index].split("="); // แยกระหว่างชื่อตัวแปร และค่าของตัวแปร
        // ถ้าค่าที่ส่งมาตรวจสอบชื่อตัวแปรตรง ให้เก็บค่าผลัพธ์เป็นค่าของตัวแปรนั้นๆ
        if(tmp[0] === val) result = decodeURIComponent(tmp[1]);
    }
    return result;  // คืนค่าของตัวแปรต้องการ ถ้าไม่มีจะเป็น null
}
 
 // function to make form values to json format
 $.fn.serializeObject = function () {
    var o = {};
    var a = this.serializeArray();
    $.each(a, function () {
      if (o[this.name] !== undefined) {
        if (!o[this.name].push) {
          o[this.name] = [o[this.name]];
        }
        o[this.name].push(this.value || "");
      } else {
        o[this.name] = this.value || "";
      }
    });
    return o;
  };

// function to set cookie
function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + exdays * 24 * 60 * 60 * 1000);
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
  }


  // get or read cookie
  function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(";");
    for (var i = 0; i < ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0) == " ") {
        c = c.substring(1);
      }

      if (c.indexOf(name) == 0) {
        return c.substring(name.length, c.length);
      }
    }
    return "";
  }

  // show alert mini with time 2.3 sec
  function Signed(icon,title) {
    const Toast = Swal.mixin({
        toast: true,
        position: 'bottom',
        width: '16rem',
        showConfirmButton: false,
        timer: 2300,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    })
    Toast.fire({
        icon: icon, //'success'
        title: title  //'Signed in successfully'
    })

}

// sho alert icon title desc and buttom Confirm
function swalertshow(icon,title,desc) {           
    Swal.fire({
      customClass: {
        confirmButton: 'btn btn-primary'
      },
      
  buttonsStyling: false,
    icon: icon,
    title: title,
    text: desc,
    showClass: {
        popup: 'animate__animated animate__fadeInDown'
    },
    hideClass: {
        popup: 'animate__animated animate__fadeOutUp'
    }
    })
}

function ckCode_alert(title,desc) {           
    Swal.fire({
    icon: 'error',
    title: title,
    text: desc,            
    showClass: {
        popup: 'animate__animated animate__fadeInDown'
    },
    hideClass: {
        popup: 'animate__animated animate__fadeOutUp'
    }
    }).then((result)=> {
        if(result.isConfirmed){
          console.log('Confirmed');
        }
        window.location.replace(home);
    })
}

function to_alert(icon,title,desc) {           
    Swal.fire({
    icon: icon,
    title: title,
    text: desc,            
    showClass: {
        popup: 'animate__animated animate__fadeInDown'
    },
    hideClass: {
        popup: 'animate__animated animate__fadeOutUp'
    }
    }).then((result)=> {
        if(result.isConfirmed){
          console.log('Confirmed');
        }
    })
}


function isNumber(n) { return /^-?[\d.]+(?:e-?\d+)?$/.test(n); } 

function pagination_show(page,pageall,per,fn){ //============== แสดงตัวจัดการหน้าข้อมูล Pagination      
  let max_p = parseInt(pageall);
  let p = parseInt(page);
  let p_prev = (p>1)?p-1:1;
  let p_next = (p<max_p)?p+1:max_p;
  let pag_h = `<div class="pagination justify-content-center">`;
  let pag_prev = `<a href="#" id="pag_prev" onclick=`+fn+`(`+per+`,`+p_prev+`)>&laquo;</a>`;
  let pag_in ="";
  let h2 = 0;
  let h1 = 0;
  page_sel = page;
  if (max_p <= 7 ){
    let act = "";
      for(var j=1; j <= max_p; j++){
        act = (p==j)?"class='active' ":"";
        pag_in += `<a href="#" `+act+` onclick=`+fn+`(`+per+`,`+j+`)>`+j+`</a> `;
      }
  }else {
    if(p<5){  //เลือกหน้าที่น้อยกว่าหน้าที่ 5
      for(var k=1; k <= p+2; k++){
        act = (p==k)?"class='active' ":"";
        pag_in += `<a href="#" `+act+` onclick=`+fn+`(`+per+`,`+k+`)>`+k+`</a> `;
      }
      h2 = Math.ceil((4+max_p-1)/2);
      pag_in += `<a href="#" onclick=`+fn+`(`+per+`,`+h2+`)>...</a> `;
      pag_in += `<a href="#" onclick=`+fn+`(`+per+`,`+(max_p-1)+`)>`+(max_p-1)+`</a> `;
      pag_in += `<a href="#" onclick=`+fn+`(`+per+`,`+(max_p)+`)>`+(max_p)+`</a> `;
      
    }else if(p >(max_p-4)){ //เลือกหน้าที่ก่อนถึงหน้าสุดท้าย อยู่ 4 หน้า
      h1 = Math.ceil((2+max_p-3)/2);
      pag_in += `<a href="#" onclick=`+fn+`(`+per+`,'1')>1</a> `;         
      pag_in += `<a href="#" onclick=`+fn+`(`+per+`,'2')>2</a> `;          
      pag_in += `<a href="#" onclick=`+fn+`(`+per+`,`+h1+`)>...</a> `;
      for(var m=(p-2); m <= max_p; m++){
        act = (p==m)?"class='active' ":"";
        pag_in += `<a href="#" `+act+` onclick=`+fn+`(`+per+`,`+m+`)>`+m+`</a> `;
      }
      
    }else { //เลือกหน้าที่อยู่ระหว่างหน้าที่ 5 และก่อนถึงหน้าสุดท้ายอยู่ 4 หน้า
      h1 = Math.ceil((p-2)/2);
      h2 = Math.ceil((p+2+max_p)/2);
      pag_in += `<a href="#" onclick=`+fn+`(`+per+`,'1')>1</a> `;
      pag_in += `<a href="#" onclick=`+fn+`(`+per+`,`+h1+`)>...</a> `;

      for(var k=(p-2); k <= p+2; k++){
        act = (p==k)?"class='active' ":"";
        pag_in += `<a href="#" `+act+` onclick=`+fn+`(`+per+`,`+k+`)>`+k+`</a> `;
      }
      pag_in += `<a href="#" onclick=`+fn+`(`+per+`,`+h2+`)>...</a> `;
      pag_in += `<a href="#" onclick=`+fn+`(`+per+`,`+(max_p)+`)>`+(max_p)+`</a> `;
    }
  }              
  let pag_next = `<a href="#" id="pag_next" onclick=`+fn+`(`+per+`,`+p_next+`)>&raquo;</a></div>`;              
  $("#pagination").html(pag_h+pag_prev+pag_in+pag_next);
}

function to_Ymd(dmY){ //เปลี่ยนรูปแบบวันที่จาก 31/04/2021 ==> 2021-04-31
  let myarr = dmY.split("/");
  return myarr[2]+"-"+myarr[1]+"-"+myarr[0];
}

function to_dmY(Ymd){ //เปลี่ยนรูปแบบวันที่จาก 2021-04-31 ==> 31/04/2021
  let myarr = Ymd.split("-");
  return myarr[2]+"/"+myarr[1]+"/"+myarr[0];
}