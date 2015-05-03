<head>
<?php 
    $this->load->view("template_header");
    $this->load->helper("user_image"); 
    $this->load->helper("current_user"); 
    $this->load->helper("url");
    $this->load->helper("show_hide");    

    
    $milestones = $this->db->query('SELECT milestone_id 
                                    FROM spw_milestones
                                    WHERE deleted = "true"
                                    ORDER BY due_date');

    if(isset($_POST['showThem']))
    {
        $s_h = $_POST['showThem'];
        $_SESSION['session_sh'] = $s_h;
        $deleted = show_hide_deleted($s_h);
    }    
    else 
    {
        $s_h = 'Hide Deleted';
        $deleted = show_hide_deleted($s_h);
    }    
?>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/jquery-1.9.1.js"></script>
    <script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
    <link rel="stylesheet" href="/resources/demos/style.css">
</head>
<body>
        <div>
           <h3>Manage Files Repository</h3>
           <p>Here you can add, edit or delete the milestones used during this semester. 
              To restore deleted milestones, select the "restore checkbox" next to its 
              name. To permanently delete milestones select the 
              "destroy checkbox" next to its name then save.</p>           
           <br>
           <?php                     
             $data = array(
                        'type'      => 'Submit',
                        'name'      => 'addNew',
                        'value'     => 'Add New',
                        'id'        => 'addNew',
                        'style'     => 'margin-left:8px;',
                        'class'     => 'btn btn-primary',
                        'onclick'   => 'addNewRow()',                   
                        );
            echo form_input($data);
            ?>
           <br><br>
        </div>
        <div class ="well">  
            <?php
            
            echo form_open('milestonescontroller/milestones_view', array(
                            'action' => $_SERVER['PHP_SELF'],
                            'method' => 'POST',
                            'name'   => 'showThem',
                            'id'     => 'showThem',
            ));       

            if ($s_h == 'Hide Deleted')
            {
                if($milestones->num_rows() > 0)
                {
                    echo form_submit(array(
                                'name'  => 'showThem',
                                'type'  => 'checkbox',
                                'class' => 'btn btn-primary pull-left',
                                'value' => 'Show Deleted',
                                'style' => 'margin-left:30px;margin-top:25px;',
                                'onClick' => 'submit();',
                                
                    ));  
                    echo form_hidden('showThem', 'Show Deleted');   
                    $showDeleted = '&nbsp&nbspShow Deleted';
                } 
                else 
                { 
                    echo form_submit(array(
                                'name'  => 'showThem',
                                'type'  => 'checkbox',
                                'class' => 'btn btn-primary.disabled pull-left ',
                                'value' => 'Show Deleted',
                                'style' => 'margin-left:30px;margin-top:25px;',
                                'disabled' => 'disabled'
                    )); 
                    $showDeleted = '&nbsp&nbsp*There is no deleted milestones to show.';
                }
               
            } else 
            {
                echo form_submit(array(
                            'name'  => 'showThem',
                            'type'  => 'checkbox',
                            'class' => 'btn btn-primary pull-left',
                            'value' => 'Hide Deleted',
                            'style' => 'margin-left:30px;margin-top:25px;', 
                            'onClick' => 'submit();',
                            'checked' => TRUE,
                ));  
                echo form_hidden('showThem', 'Hide Deleted'); 
                $showDeleted = '&nbsp&nbspShow Deleted';
            }
            echo form_close();  
            echo form_label($showDeleted);

            echo form_open('milestonescontroller/requestupdate', array(
                        'class' => '',
                        'name' => 'picker_form',
                        'id' => 'picker_form'            
            ));                             
 
            echo form_submit(array(
                        'id'    => 'btn-act-deact',
                        'name'  => 'action',
                        'type'  => 'Submit',
                        'class' => 'btn btn-primary',
                        'value' => 'Delete',
                        'style' => 'float:right;margin-right:25px'
        ));
            ?>
            <br><br>
            <table id="milestone_list" class="table table-bordered">
                <thead>
                    <tr>     
                        <th></th>
                        <th>Milestone Name</th>                        
                        <th>Due Date</th>                                                
                        <th>Delete</th>
                    </tr>
                </thead>
                
            <?php       

            if(mysql_num_rows(mysql_query("SHOW TABLES LIKE 'spw_milestones'")) == 1) 
            {
                $sql = 'SELECT milestone_id, milestone_name, due_date, deleted
                            FROM spw_milestones '
                            . $deleted .
                            ' ORDER BY due_date';

                $query = $this->db->query($sql);
                $count = 0;
                $num_rows = $query->num_rows;
                if ($num_rows > 0)
                {
                    foreach ($query->result_array() as $row)
                    {                                          
                        echo "<tr bgcolor=\"#F4E8D4\">";      
                        echo "<td><input type=\"text\" name=\"milestone[".$count."][id]\" value=\""
                                    . $row['milestone_id']."\" style=\"display:none;\" readonly></td>";
                        if($row['deleted'] == 'true')
                        {
                            echo "<td>";
                            echo "<input type=\"text\" name=\"milestone[".$count."][name]\" value=\"".$row['milestone_name']."\" style=\"color:red;\" readonly>&nbsp&nbsp&nbsp";

                            echo "<input class=\"testing\" id=\"restore\" type=\"checkbox\" name=\"milestone[".$count."][restore]\" value=\"".$row['milestone_id']."\" style=\"margin-bottom:25px\" onClick=\"toggle(this);\">&nbspRestore&nbsp&nbsp&nbsp&nbsp";
                            echo "<input class=\"testing\" id=\"destroy\" type=\"checkbox\" name=\"milestone[".$count."][destroy]\" value=\"".$row['milestone_id']."\" style=\"margin-bottom:25px\"  onClick=\"toggle(this);\">&nbspDestroy";

                            
                            echo "</td>";
                            echo "<td><input type=\"text\" name=\"milestone[".$count."][due]\" value=\"".$row['due_date']."\" style=\"color:red;\" class=\"input_date\" readonly></td>";        
                            echo "<td><input type=\"checkbox\" name=\"delete_milestones[]\" DISABLED value=\"" . $row['milestone_id']."\"></td>";
                        } 
                        else 
                        {
                            echo "<td>";
                            echo "<input type=\"text\" name=\"milestone[".$count."][name]\" value=\"".$row['milestone_name']."\">";

                            echo "</td>";
                            echo "<td><input type=\"text\" name=\"milestone[".$count."][due]\" value=\"".$row['due_date']."\" class=\"input_date\"></td>";        
                            echo "<td><input type=\"checkbox\" name=\"delete_milestones[]\" value=\"" . $row['milestone_id']."\"></td>";
                        }        
                        echo "</tr>"; 
                        $count++;
                    }          
                }
            }
            ?>                
            </table>    
           <?php 
                echo anchor('files/project_files', 
                                    'Back', array(
                                    'style'   => 'margin-left: 260px; width: 120px;',
                                    'class'   => 'btn btn-primary'
                                                 ));
                echo form_submit(array(
                                    'id'    => 'btn-save',
                                    'name'  => 'action',
                                    'type'  => 'submit',
                                    'style' => 'margin-left: 50px; width: 150px;',
                                    'class' => 'btn btn-primary',
                                    'value' => 'Save Milestones'
                ));
                
            ?>
        </div>
<?php
echo form_close();
?>

<?php $this->load->view("template_footer"); 

?>

<script>           
    function addNewRow()
    {        
        var table = document.getElementById("milestone_list");
        var rowNumber = table.rows.length;

        var row = table.insertRow(rowNumber++);
        var cell1 = row.insertCell(0);
        var cell2 = row.insertCell(1);
        var cell3 = row.insertCell(2);
        var cell4 = row.insertCell(3);
        
        var col1 = "<td><input type=\"text\" name=\"milestone["+rowNumber+"][id]\" style=\"display:none;\" readonly></td>";
        var col2 = "<td><input type=\"text\" name=\"milestone["+rowNumber+"][name]\" placeholder=\"Enter milestone name\"></td>";
        var col3 = "<td><input type=\"text\" name=\"milestone["+rowNumber+"][due]\" class=\"input_date\"></td>"
        var col4 = "<td><input type=\"checkbox\" name=\"delete_milestones[]\"></td>";        
            
        cell1.innerHTML = col1.replace();
        cell2.innerHTML = col2.replace();
        cell3.innerHTML = col3.replace();
        cell4.innerHTML = col4.replace();
    }

    function toggle(chkBox)
    {        
        checkboxes = $(chkBox).parent().find('.testing');
        var isChecked = chkBox.checked; //check or uncheck box
        for (var i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = false;  //clear all of them
        }
        if (isChecked) {
            chkBox.checked = true; //if the original one wasn't checked, check it
        }   
    }
    
    $(document).on('focusin', '.input_date', function(){
        $(this).datepicker();
    });
    
</script>   
</body>
