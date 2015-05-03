
<?php $this->load->view("template_header"); ?>

<h3> Virtual Machine Request </h3>
<div>
    <img onError="this.onerror=null;this.src='img/no-photo.jpeg';" src="<?php if($picture) echo $picture; else echo base_url( getImage($user_id) ); ?>" alt="" class="img-polaroid" height="80" width="80">
    <p><?php echo $full_name; ?></p>
</div>
<br>
<?php echo form_open('vm_request'); ?>
<div id="machines">
<div class="machine col-md-12">
    <table class="auto" id="machines_table" >
    <thead>
      <tr>
         <th>Image Name</th>
         <th>Memory RAM (mb)</th>
         <th>Storage (mb)</th>
         <th>Number of VM</th>
      </tr>
   </thead>
   <br>
    <tbody>
        <tr>
            <td>
                <select name="os">
                        <?php 
                            
                            foreach($active_images as $os)
                                echo '<option>'.$os->image_name.'</option>';
                        ?>
                </select>
            </td>
            <td>
                <input id="ram" name="ram" placeholder="Enter memory RAM" type="text" value="">
            </td>
           <td>
               <input id="hdd" name="hdd" placeholder="Enter storage amount" type="text" value="">
           </td>
           <td>
               <input id="qty" name="qty" placeholder="Enter number of VMs" type="text" value="">
           </td>
        </tr>
    </tbody>
</table>
</div>
</div>
<br>
<button id="addRequest" type="button" class="btn btn-info">Add Virtual Machine</button>
<button id="submitRequests" type="button" class="btn btn-primary pull-right">Submit</button>

<h4>Previous Requests</h4>
<table class="auto table">
    <thead>
        <tr>
            <th>Image Name</th>
            <th>Memory RAM</th>
            <th>Storage</th>
            <th>Number of VM</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($requests as $request):?>
        <tr>
            <td><?php echo $request->OS;?></td>
            <td><?php echo $request->RAM;?></td>
            <td><?php echo $request->storage;?></td>
            <td><?php echo $request->numb_vm;?></td>
            <td><?php echo $request->status;?></td>
        </tr>
        <?php endforeach;?>
    </tbody>
</table>

<script>
$('#addRequest').click(function(){
    console.log("Clicked add request");
    var machines = $("#machines");
    var lastMachine = $("#machines .machine:last-child");
    machines.append($('<br>'));
    machines.append(lastMachine.clone());
});

$('#submitRequests').click(function(){
    console.log("Clicked submit");
    var data = getTableContent();
    var validInput = isValidInput(data);
    if(validInput){
        console.log("machines: ");
        console.log(data);
        uploadMachines(data);
    }
});

function uploadMachines(machineList){
    $.ajax({
        type: "POST",
        url: "./vm-request",
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
        var os = row.find('[name="os"]').val();
        var ram = row.find('[name="ram"]').val();
        var hdd = row.find('[name="hdd"]').val();
        var qty = row.find('[name="qty"]').val();
        var obj = {
            "os":os,
            "ram":ram,
            "hdd":hdd,
            "qty":qty
        };
        data.push(obj);
    }
    return data;
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
<?php echo form_close(); ?>
<?php $this->load->view("template_footer"); ?>