<?php $this->load->view("template_header"); ?>



<?php
  echo form_open('projectcontroller/addImages', array(
                                                  'class' => '',
                                                  'id' => 'registration_form'
                                                  ));
  ?>
<div class="well" id="image_div">
<div>
    <h4> Add New Image Name </h4>
</div>
<?php
    echo("<div>");
    echo form_label('Image Name:');
    echo form_input(array(
                        'id' => 'image_name',
                        'name' => 'image_name',
                        'type' => 'text',
                        'placeholder' => 'Image Name',
                        'required' => '',
                        'title' => 'Image Name'
                        ));
    echo("</div>");
    echo("<div>");
    echo form_submit(array(
        'id' => 'btn',
        'name' => 'min',
        'type' => 'Submit',
        'class' => 'btn btn-info',
        'value' => 'Add Image Name'
    ));
    echo("</div>");

echo form_close();
?>
</div>



<div class="well images"> 
<br>
    <table class="table table-bordered vm-img-table" id="image_table">
        <thead>
        <tr>
            <th>      
                <input id="image" class="input-large text-filter" type="text" value="<?php echo $image ?>">
            </th>
            <th>  
               <select class="field-custom dropdown" id="status">
                  <?php echo '<option '.($status==='ALL STATUS'?'selected="selected"':'').' value="ALL STATUS">ALL STATUS</option>'; ?>
                  <?php echo '<option '.($status==='ACTIVE'?'selected="selected"':'').' value="ACTIVE">ACTIVE</option>'; ?>
                  <?php echo '<option '.($status==='INACTIVE'?'selected="selected"':'').'value="INACTIVE">INACTIVE</option>'; ?>
                </select>
            </th>
            <th></th>
            <th></th>
        </tr>
        <tr>
            <th style="display:none;">key</th>
            <th > IMAGE NAME </th>
            <th> IMAGE STATUS </th>
            <th> CHANGE STATUS </th>
            <th> DELETE IMAGE </th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($results as $row):?>
        <tr>
            <td style="display:none;">
                <select class="field-custom" name="key">
                <?php echo '<option>'.$row->id.'</option>'?>
                </select>
            </td>
            <td>
                <input id="image" name="image" class="input" type="text" value="<?php echo $row->image_name; ?>">
            </td>
            <td>
                    <?php echo $row->status; ?>
            </td>
            <td>
            <?php
                if($row->status == 'ACTIVE'){
                    echo("<a href=".base_url('./vm-images?status='.$status.'&image='.$image.'&change_status='.$row->status.
                            "&image_name=".urlencode($row->image_name)).
                            "> <img id=\"\" src=".base_url('img/green_light.png')." height=\"20\" width=\"20\" > </a>");
                }else{
                    echo("<a href=".base_url('./vm-images?status='.$status.'&image='.$image.'&change_status='.$row->status.
                            "&image_name=".urlencode($row->image_name)).
                            "> <img id=\"\" src=".base_url('img/red_light.png')." height=\"20\" width=\"20\" > </a>");
                }
            ?>  
            </td>
            <td>
            <?php
                    $msg = "Are you sure you want to delete image $row->image_name ?";
                    echo("<a href=".base_url('./vm-images?status='.$status.'&image='.$image.'&delete_image_name='.
                            urlencode($row->image_name))." onclick=\"return confirm('$msg')\"> <img id=\"\" src=".
                            base_url('img/deletered.png')." height=\"20\" width=\"20\" > </a>");
            ?>
            </td>
        </tr>
        <?php endforeach;?>
        </tbody>
        </table>
<button id="submitRequests" type="button" class="btn btn-primary">Submit</button>
</div>    
<script>
$('#submitRequests').click(function(){
    console.log("Clicked submit");
    var data = getTableContent();
    console.log("machines: ");
    console.log(data);
    if(isValidInput(data))
        uploadMachines(data);
});

function uploadMachines(machineList){
    var url = "./vm-images";
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
        },error: function (xhr, ajaxOptions, thrownError) {
            console.log(xhr.status);
            console.log(thrownError);
        }
    });
}

function getTableContent() {
    var data = [];
    var table = $('#image_table tbody tr');
    for(var i = 0 ; i< table.length;i++){
        var row = table.eq(i);
        var key = row.find('[name="key"]').val();
        var image = row.find('[name="image"]').val();
        var obj = {
            "key":key,
            "image":image
        };
        data.push(obj);
    }
    return data;
}


function filterForm(){

    var status = $('#status').val();
    var image = $("#image").val();
    
    var whereto = "./vm-images?";
    
    if(image!=='') whereto+="image="+image+"&";
    if(status!=='') whereto+="status="+status+"&";

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

function isValidInput(arr){
    for(var i in arr){
        var image = arr[i].image;

        if(image == ""){
            alert("ALERT: image field cannot be enpty");
            return false;
        }
    }
    return true;
}

</script>   

<?php $this->load->view("template_footer"); ?>