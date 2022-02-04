
function show_frmRep(){ //==================== เลือกรายงานที่ต้องการ
    var html = `
  <div class="container animate__animated animate__fadeIn">
    <div class="row">
        <div class="col-md-8 mx-auto mb-3" style="font-size:20px;"> 
            <span class="d-block p-2 bg-primary text-white rounded-lg" style="border-radius:25px 25px 0 0;"  align="center"><i class="far fa-newspaper fa-lg" aria-hidden="true"></i> รายงาน</span>
        </div>
        <div class="col-md-8 mx-auto">
            <form name="frmrep" id="frmrep" method="post" action='' target="_blank" role='report'>
                <div class="row mb-2">    
                    <div class="col-md-6 mb-2">
                        <div class="input-group">
                            <div class="input-group-text" style="width: 60px; background-color: aquamarine;">วันที่</div>
                            <input type="text" class="form-control" name="datefm" id="picker_rep">
                        </div>
                    </div>   
                    <div class="col-md-6 mb-2">
                        <div class="input-group">
                            <div class="input-group-text" style="width: 60px; background-color: aquamarine;">ถึง</div>
                            <input type="text" class="form-control" name="dateto" id="picker2_rep">      
                        </div>     
                    </div>  
                </div>

                <div class="row mb-2">   
                    <div class="col-md-6 mb-2">
                        <div class="input-group">
                            <div class="input-group-text" style="width: 60px; background-color: aquamarine;">ผู้รับ</div>
                            <select class="form-select" name="dp_rec_rep" id="dp_rec_rep"></select>   
                        </div>     
                        <input id="dpsel_rec_rep" name="dpsel_rec_rep" type="hidden" value=""/>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="input-group">
                            <div class="input-group-text" style="width: 60px; background-color: aquamarine;">ผู้ส่ง</div>
                            <select class="form-select" name="dp_post_rep" id="dp_post_rep"></select>   
                        </div>     
                        <input id="dpsel_post_rep" name="dpsel_post_rep" type="hidden" value=""/>
                    </div>
                </div>

                <div class="row">
                    <div class="input-group mb-2 col-md-6">
                        <input name='search' type="text" class="form-control" placeholder="กรองข้อมูล..." value='' onFocus="this.value ='';">
                    </div>						

                    <div class="input-group mb-2 col-md-12">
                        <label for="selrep">เลือก :&nbsp;&nbsp;</label>
                        <select class="fmsel form-control mb-2" size="4" id="selrep" name="selrep">
                            <option value="report/rep_receive_data">1.) การรับเข้า</option>
                            <option value="report/rep_receive_sum">2.) สรุปการรับเข้า</option>     
                            <option value="report/rep_pack_data">3.) การบรรจุ</option>   
                                                 
                        </select>
                    </div>

                </div>
                
                <div class="row">
                    <div class='col-md-12' align="center">
                        <button id="show_rep" type='button' onclick="submitRep()" title="แสดงรายงาน" class='btn btn-primary me-2'>แสดงรายงาน</button>
                        <button id="show_export" type='button' onclick="submitExport()" title="ส่งออก Excel" class='btn btn-success me-2'>to Excel</button>
                        <button id="bt_back" type='button' title="กลับหน้าหลัก"  class='btn btn-warning ms-2' >กลับ</button>
                    </div>
                </div>
            </form>
        </div>
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

    $( '#picker_rep' ).datetimepicker( {
        timepicker: false,
        datepicker: true,
        format: 'd/m/Y',
        value:today,
        mask: true
    } );

    $( '#picker2_rep' ).datetimepicker( {
        timepicker: false,
        datepicker: true,
        format: 'd/m/Y',
        value:today,
        mask: true
    } );

    let dropdown = $('#dp_rec_rep');
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
                $("#dp_rec_rep option[value='"+u_depart+"']").attr("selected","selected");  
                document.getElementById('dpsel_rec_rep').value = $("#dp_rec_rep option:selected").text();           
                }
            });  

    let dropdown2 = $('#dp_post_rep');
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
}

//=========================== Event ======================================


$(document).on("change", "#picker_rep", function () { 
    $('#picker2_rep').datetimepicker( {
        value:this.value
    });
});
$(document).on("change", "#dp_rec_rep", function () {   
    document.getElementById('dpsel_rec_rep').value = this.options[this.selectedIndex].text;
});
$(document).on("change", "#dp_post_rep", function () {   
    document.getElementById('dpsel_post_rep').value = this.options[this.selectedIndex].text;
});

function submitRep() {
	var s1 = document.getElementsByName('selrep');
		s1 = s1.item(0).value;
		if(s1 == ""){
			Signed('warning','กรุณาเลือกรายงานที่ต้องการก่อน ')
		} else{
			document.getElementById('frmrep').action = s1;
			$('#frmrep').attr('target', '_blank');
			$( '#frmrep' ).submit();
		}
}

function submitExport() {
	var sel = document.getElementsByName('selrep');    
    var sel_ind = $("select[name='selrep'] option:selected").index();
		if(sel.item(0).value == ""){
			Signed('warning','กรุณาเลือกรายงานที่ต้องการก่อน ')
		} else{
            if(sel_ind >= 0){
			    document.getElementById('frmrep').action = sel.item(0).value+"_export";
                $('#frmrep').attr('target', '_blank');
                $( '#frmrep' ).submit();
            }
		}
}
	