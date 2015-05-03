<?php $this->load->view("template_header"); ?>
<h3>Virtual Machine Request Page</h3>
<?php
    $oses = array();
    foreach($active_images as $r){
    $oses[] = $r->image_name;}
?>
<br>

<div id="machines">
<div class="machine col-md-12">

<table class="table table-bordered" id="machines_table">
    <thead>
        <tr>
            <th>
                <input id="image" class="input-medium text-filter" type="text" value="<?php echo $image ?>">
            </th>
            <th>
                <input id="f_ram" class="input-mini text-filter" type="text" value="<?php echo $f_ram ?>">
            </th>
            <th>
                <input id="storage" class="input-mini text-filter" type="text" value="<?php echo $storage ?>">
            </th>
            <th>
                <input id="f_qty" class="input-mini text-filter" type="text" value="<?php echo $f_qty ?>">
            </th>
            <th>
                <select class="field-custom dropdown" id="status">
                  <?php echo '<option '.($status==='ALL STATUS'?'selected="selected"':'').' value="ALL STATUS">ALL STATUS</option>'; ?>
                  <?php echo '<option '.($status==='APPROVED'?'selected="selected"':'').' value="APPROVED">APPROVED</option>'; ?>
                  <?php echo '<option '.($status==='PENDING'?'selected="selected"':'').'value="PENDING">PENDING</option>'; ?>
                  <?php echo '<option '.($status==='DENIED'?'selected="selected"':'').' value="DENIED">DENIED</option>'; ?>
                </select>
            </th>
            <th>
                <input id="name" class="input-small text-filter" type="text" value="<?php echo $name ?>">
            </th>
            <th>
                <input id="term" class="input-small text-filter" type="text" value="<?php echo $term ?>">
            </th>
            <th></th>
        </tr>


        <tr>
            <th style="display:none;">Request No.</th>
            <th>Image Name</th>
            <th>RAM (mb)</th>
            <th>Storage (mb)</th>
            <th>Number of VM</th>
            <th>Status</th>
            <th>Full Name</th>
            <th>Term</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($requests as $request):?>
        <tr>
            <td style="display:none;">
                <select class="field-custom" name="key">
                <?php 
                   echo '<option>'.$request->id.'</option>'
                ?>
                </select>
            </td>
            <td>
                <select class="field-custom" name="os">
                <?php 
                        foreach($oses as $os){
                            if($request->OS === $os)
                                echo'<option selected="selected">'.$os.'</option>';
                            else{
                             echo'<option>'.$os.'</option>';
                            }
                        }
                ?>
                </select>
            </td>
            <td>
                <input id="ram" name="ram" class="input-mini" type="text" value="<?php echo $request->RAM ?>">
            </td>
            <td>
                <input id="hdd" name="hdd" class="input-mini" type="text" value="<?php echo $request->storage ?>">
            </td>
            <td>
                <input id="qty" name="qty" class="input-mini" type="text" value="<?php echo $request->numb_vm ?>">
            </td>
            <td>
                <select class="field-custom" name="status">
                    <?php 
                    $sta = array(
                            'PENDING',
                            'APPROVED',
                            'DENIED'
                        );
                        
                            foreach($sta as $st){
                                if($request->status == $st)
                                    echo'<option value ='.$st.' selected="selected">'.$st.'</option>';
                                else{
                                 echo'<option value="'.$st.'">'.$st.'</option>';
                                }
                            }
                    ?>
                </select>
            </td>
            <!-- COLUMN #6 & #7 -->
            <td>
                <?php echo $request->student_name ?>
            </td>
            <td>
                <?php echo $request->term ?>
            </td>
            <td>
                <?php
                    $msg = "Are you sure you want to delete $request->OS VM request from $request->student_name ?";
                    $url = "id=$request->id&image=$image&f_ram=$f_ram&storage=$storage&f_qty=$f_qty&status=$status&name=$name&term=$term";
                    echo("<a data-toggle=\"tooltip\" title=\"Delete Request\" href=".base_url('./vm-requests?'.$url).
                            " onclick=\"return confirm('$msg')\"> <img id=\"\" src=".
                            base_url('img/deletered.png')." height=\"20\" width=\"20\" > </a>");
                ?>
            </td>
        </tr>
        <?php endforeach;?>
    </tbody>
</table>
</div>
<label for="usr">Default email of <?php echo $name_default ?></label>
<input  type="text" id="email_address" value=<?php echo $email_address ?> class="form-control"/>
<button id="submitRequests" type="button" class="btn btn-primary pull-right">Submit</button>
</div>
<br>
<script>
function filterForm(){
   
    var f_ram = $("#f_ram").val();
    var storage = $('#storage').val();
    var f_qty = $('#f_qty').val();
    var status = $('#status').val();
    var image = $("#image").val();
    var name = $("#name").val();
    var term = $("#term").val();
    
    var whereto = "./vm-requests?";
    
    if(image!=='') whereto+="image="+image+"&";
    if(status!=='') whereto+="status="+status+"&";
    if(f_ram!=='') whereto+="f_ram="+f_ram+"&";
    if(storage!=='') whereto+="storage="+storage+"&";
    if(f_qty!=='') whereto+="f_qty="+f_qty+"&";
    if(name!=='') whereto+="name="+name+"&";
    if(term!=='') whereto+="term="+term+"&";
//    if(term!=='') whereto+="term="+term+"&";
    var lastChar = whereto.charAt(whereto.length-1);
    console.log("last char: "+lastChar);
    if(lastChar==='&'){
        console.log("in here");
        whereto = whereto.substring(0,whereto.length-1);
    }
    console.log(whereto);
    window.location.href = whereto;
}

$('.text-filter').keyup(function(e){
    if(e.keyCode == 13)
    {
        filterForm();
    }
});

$(".dropdown" ).change(function() {
    filterForm();
});

$('#submitRequests').click(function(){
    console.log("Clicked submit");
    var data = getTableContent();
    var validInput = isValidInput(data);
    console.log("machines: ");
    console.log(data);
    var john_email = $("#email_address").val();
    if(isEmail(john_email) && validInput){
        uploadMachines(data,john_email);
    }
    else if(!isEmail(john_email))  
        alert("Incorrect email");
});

function uploadMachines(machineList,email){
    var url = "./vm-requests?email_address="+email;
    console.log(url);
    console.log(JSON.stringify(machineList));
    $.ajax({
        type: "POST",
        url: url,
        data: JSON.stringify(machineList),
        dataType: "json",
        success: function(response){
            console.log("response");
            console.log(response);
            if(response.success){
                //do page reload when success
                location.reload();
            }else{
                //show meassage when not success
                alert("Upload Failed");
            }
        }
    });
}

function getTableContent() {
    var data = [];
    var table = $('#machines_table tbody tr');
    for(var i = 0 ; i< table.length;i++){
        var row = table.eq(i);
        var key = row.find('[name="key"]').val();
        var os = row.find('[name="os"]').val();
        var ram = row.find('[name="ram"]').val();
        var hdd = row.find('[name="hdd"]').val();
        var qty = row.find('[name="qty"]').val();
        var status = row.find('[name="status"]').val();

        var obj = {
            "key":key,
            "os":os,
            "ram":ram,
            "hdd":hdd,
            "qty":qty,
            "status":status
        };
        data.push(obj);
    }
    return data;
}

function isEmail(email) {
  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return regex.test(email);
}
function isNumber(n) {
  return !isNaN(parseFloat(n)) && isFinite(n) && n > 0;
}
function isValidInput(arr){
    for(var i in arr){
        var ram = arr[i].ram;
        var hdd = arr[i].hdd;
        var qty = arr[i].qty;
        if(!isNumber(ram)){
            alert("RAM value: "+ ram +" must be numeric and greater than zero");
            return false;
        }
        if(!isNumber(hdd)){
            alert("Storage value: "+ hdd +" must be numeric and greater than zero");
            return false;
        }
        if(!isNumber(qty)){
            alert("Number of Vms value: "+ qty +" must be numeric and greater than zero");
            return false;
        }
    }
    return true;
}
</script>
<?php $this->load->view("template_footer"); ?>




<!--
<?php 
    function generateSelect($name = '', $options = array(), $default = '') {
    $html = '<select name="'.$name.'">';
    foreach ($options as $option => $value) {
        if ($option == $default) {
            $html .= '<option value='.$value.' selected="selected">'.$option.'</option>';
        } else {
            $html .= '<option value='.$value.'>'.$option.'</option>';
        }
    }

    $html .= '</select>';
    return $html;
    $html = generateSelect('os', $oses, $request->OS); /*call statement*/
}
?>
<!--
function isNumber(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}
isNumeric: function( obj ) {
    return !jQuery.isArray( obj ) && (obj - parseFloat( obj ) + 1) >= 0;
}
-->
