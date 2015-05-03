<?php $this->load->view("template_header"); ?>

<!-- added scripts for the date picker-->
<script src="<?php echo base_url() ?>js/jquery-1.9.1.js"></script>
<script src="<?php echo base_url() ?>js/jquery-ui.js"></script>

<!-- added scripts for JQuery Accordion -->
<script src="<?php echo base_url() ?>js/jquery-accordion.js"></script>
<script src="http://js.nicedit.com/nicEdit-latest.js" type="text/javascript"></script>
<script type="text/javascript">bkLib.onDomLoaded(nicEditors.allTextAreas);</script>

<script type="text/javascript" charset="utf-8">
$(function( )
	{
  $('#from-pick').datepicker();
	});

$(function()
	{
  $('#to-pick').datepicker();
	});

$( function( )
	{
  $( "#accordion" ).accordion( {
                              heightStyle: "content"
                              } );
	} );

$( function( )
	{
  $( "#accordion" ).accordion( {
                              collapsible: true
                              } );
	} );

function pwd_should_match()
{
  var password_confirm = document.getElementById('password_2');
  if (password_confirm.value != document.getElementById('password_1').value)
  {
		password_confirm.setCustomValidity('Passwords do not match');
  }
  else if (password_confirm.value.length < 6 || document.getElementById('password_1').value.length < 6)
  {
		password_confirm.setCustomValidity('Passwords are too short (min 6 characters)');
  }
  else
  {
		password_confirm.setCustomValidity('');
  }
}
</script>

<!-- CSS to create Accordion -->
<STYLE type="text/css">
.ui-accordion .ui-accordion-header
{
display: block;
cursor: pointer;
position: relative;
  margin-top: 2px;
padding: .5em .5em .5em .7em;
  min-height: 0;
}

.ui-accordion .ui-accordion-icons
{
  padding-left: 2.2em;
}

.ui-accordion .ui-accordion-noicons
{
  padding-left: .7em;
}

.ui-accordion .ui-accordion-icons .ui-accordion-icons
{
  padding-left: 2.2em;
}

.ui-accordion .ui-accordion-header .ui-accordion-header-icon
{
position: absolute;
left: .5em;
top: 50%;
  margin-top: -8px;
}

.ui-accordion .ui-accordion-content
{
padding: 1em 2.2em;
  border-top: 0;
overflow: scroll;
}
</STYLE>




<div>
    <?php echo anchor('files/project_files', 'Go to Files Repository', array('class' => 'btn btn-primary btn-small pull-right')) ?>
</div>
<div>
    <?php echo anchor('admin/refresh_api', 'Refresh from API', array('style' => 'margin-right: 8px', 'class' => 'btn btn-primary btn-small pull-right')) ?>
</div>
<div>
    <?php echo anchor('admin/filters', 'View All Users', array('style' => 'margin-right: 8px', 'class' => 'btn btn-primary btn-small pull-right')) ?>
</div>
<div>
    <?php echo anchor('vm-images', 'VM Images', array('style' => 'margin-right: 8px', 'class' => 'btn btn-primary btn-small pull-right')) ?>
</div>
<div>
    <?php echo anchor('vm-requests', 'VM Requests', array('style' => 'margin-right: 8px', 'class' => 'btn btn-primary btn-small pull-right')) ?>
</div>
<div>
    <?php echo anchor('userManagement', 'All Users', array('style' => 'margin-right: 8px', 'class' => 'btn btn-primary btn-small pull-right')) ?>
</div>
<br><br>

<!-- Start Well for Head Professor Dashboard -->

<?php
  echo form_open('admin/register_new_user', array(
                                                  'class' => '',
                                                  'id' => 'registration_form'
                                                  ));
  ?>
<div class="well">
<h3>User Management System</h3>
<br />
<div id="accordion">
<h3>Manually Add New User </h3>
<div>
<?php
  echo("<div>");
  echo form_input(array(
                        'id' => 'first_name',
                        'name' => 'first_name',
                        'type' => 'text',
                        'placeholder' => 'First Name',
                        'required' => '',
                        'title' => 'First Name'
                        ));
  echo("</div>");
  echo("<div>");
  echo form_input(array(
                        'id' => 'last_name',
                        'name' => 'last_name',
                        'type' => 'text',
                        'placeholder' => 'Last Name',
                        'required' => '',
                        'title' => 'Last Name'
                        ));
  echo("</div>");
  echo("<div>");
  echo form_input(array(
                        'id' => 'email_address',
                        'name' => 'email_address',
                        'type' => 'email',
                        'placeholder' => 'email@example.com',
                        'value' => set_value('email_address'),
                        'required' => '',
                        'title' => 'Email address'
                        ));
  echo("</div>");
  
  echo( "<div style=\"padding-bottom: 10px;\">" );
  echo form_dropdown( 'role', array( 'STUDENT' => 'Student', 'PROFESSOR' => 'Professor' ), 'student' );
  echo( "</div>" );
  
  echo("<div>");
  echo form_submit(array(
                         'id' => 'btn',
                         'name' => 'accounts',
                         'type' => 'Submit',
                         'class' => 'btn btn-info',
                         'value' => 'Create New User'
                         ));
  echo("</div>");
  
  echo form_close();
  ?>
</div>
<h3>Email Templates</h3>
<?php
  $query_msg_prof = $this->db->query( 'SELECT intro FROM email_template WHERE id = "PROFESSOR"' );
  
  foreach( $query_msg_prof->result_array( ) as $row )
  {
	$msg_prof = $row[ 'intro' ];
  }
  			
  $query_msg_stud = $this->db->query( 'SELECT intro FROM email_template WHERE id = "STUDENT"' );
  foreach( $query_msg_stud->result_array( ) as $row )
  {
	$msg_stud = $row[ 'intro' ];
  }

  $unique_link = $this->input->post( 'unique_link' );
  $message =' <html>
  <head><title></title></head>
  <body>
  <h2>Welcome to the Senior Project Website !! </h2>
  <p>We have created an account for you to access.</p>
  <p> Please use the link below to complete your registration to the Senior Project Website</p>
  
  <p>Once you login, please update your profile and refer to the User Guide on the About page for help.
  </p>
  
  </body>
  </html>';
  ?>
<div><p>
<p>Please review the following email templates and make any necessary changes</p>
<p>Professor Invitation:</p>
<?php
  echo form_open( 'admin/change_invitation/PROFESSOR', array( 'class' => 'form-inline', 'id' => 'professor_invite' ) );
  echo( "<div style='padding-bottom: 10px;'>" );
  echo form_textarea( array(
                            'id' => 'professor_invitation_text',
                            'name' => 'professor_invitation_text',
                            'value' => $msg_prof,
                            'style' => "margin-left: 1px; margin-right: 7px; width: 600px;",
                            'title' => 'Professor Invitation' ) );
  echo( "</div>" );
  
  echo( "<div>" );
  echo form_hidden( array( 'role' => 'PROFESSOR' ) );
  echo form_submit( array(
                          'id' => 'professor-btn',
                          'name' => 'professor btn',
                          'type' => 'Submit',
                          'class' => 'btn btn-info',
                          'value' => 'Make Changes to Invitation' ) );
  echo( "</div>" );
  echo form_close( );
  ?>
<br /> <br />
<p>Student Invitation:</p>
<?php
  echo form_open( 'admin/change_invitation/STUDENT', array( 'class' => 'form-inline', 'id' => 'student_invite' ) );
  echo( "<div style='padding-bottom: 10px;'>" );
  echo form_textarea( array(
                            'id' => 'student_invitation_text',
                            'name' => 'student_invitation_text',
                            'value' => $msg_stud,
                            'style' => "margin-left: 1px; margin-right: 7px; width: 600px;",
                            'title' => 'Student Invitation' ) );
  echo( "</div>" );
  echo( "<div>" );
  echo form_hidden( array( 'role' => 'STUDENT' ) );
  echo form_submit( array(
                          'id' => 'student-btn',
                          'name' => 'student btn',
                          'type' => 'Submit',
                          'class' => 'btn btn-info',
                          'value' => 'Make Changes to Invitation' ) );
  echo( "</div>" );
  echo form_close( );
  ?>
</p></div>
</div>

</div>
<!-- End Well for Head Professor Dashboard -->

<?php
echo form_open('admin/set_deadline', array(
    'class' => 'form-inline',
    'id' => 'set_deadline_form'
));
?>

<div class="well">
    <h4>Set Deadline for Students to Choose a Project</h4>
    <br>

    <?php
    echo form_label('Start Date:', 'from-pick');
    echo form_input(array(
        'id' => 'from-pick',
        'name' => 'from-pick',
        'class' => 'input-small',
        'placeholder' => 'mm/dd/yyyy',
        'style' => "margin-left: 1px; margin-right: 5px",
        'required' => '',
        'title' => 'Start Deadline'
    ));

    echo form_label('End Date:', 'to-pick');
    echo form_input(array(
        'id' => 'to-pick',
        'name' => 'to-pick',
        'class' => 'input-small',
        'placeholder' => 'mm/dd/yyyy',
        'style' => "margin-left: 1px; margin-right: 7px",
        'required' => '',
        'title' => 'End Deadline'
    ));

    echo form_submit(array(
        'id' => 'btn',
        'name' => 'deadline',
        'type' => 'Submit',
        'class' => 'btn btn-info',
        'value' => 'Set Deadline'
    ));

    //$currentTerm = $this->spw_term_model->getCurrentTermInfo();
    $term = SPW_Term_Model::getInstance();
    $currentTerm = $term->getCurrentTermInfo();
    echo form_close()
    ?>
    <br>
    <br>
    <table class="table" >
        <thead>
            <tr>
                <th>Join/Leave Period Begins</th>
                <th>Join/Leave Deadline</th>
            </tr>
        </thead>
        <tr class="info">
            <?php
            //display the deadline information for the current term in a single-rowed column
            echo "<td>" . date("m-d-Y", strtotime($currentTerm->start_date)) . "</td>";
            echo "<td>" . date("m-d-Y", strtotime($currentTerm->end_date)) . "</td>";
            ?>
        </tr>
    </table>
</div><!--end well -->

<?php
/* echo form_open('admin/register_professor', array(
    'class' => '',
    'id' => 'registration_form' 
));*/
?>
<!--
<div class="well">
    <div class="text-center">
        <h4>Create a New Professor User</h4>
    </div>

    <?php
      
    echo("<div>");
    echo form_input(array(
        'id' => 'first_name',
        'name' => 'first_name',
        'type' => 'text',
        'placeholder' => 'First Name',
        'required' => '',
        'title' => 'First Name'
    ));
    echo("</div>");
    echo("<div>");
    echo form_input(array(
        'id' => 'last_name',
        'name' => 'last_name',
        'type' => 'text',
        'placeholder' => 'Last Name',
        'required' => '',
        'title' => 'Last Name'
    ));
    echo("</div>");
    echo("<div>");
    echo form_input(array(
        'id' => 'email_address',
        'name' => 'email_address',
        'type' => 'email',
        'placeholder' => 'email@example.com',
        'value' => set_value('email_address'),
        'required' => '',
        'title' => 'Email address'
    ));
    echo("</div>");

    echo("<div>");
    echo form_password(array(
        'id' => 'password_1',
        'name' => 'password_1',
        'placeholder' => 'Password',
        'required' => '',
        'title' => 'Password'
    ));
    echo("</div>");

    echo("<div>");
    echo form_password(array(
        'id' => 'password_2',
        'name' => 'password_2',
        'placeholder' => 'Confirm Password',
        'required' => '',
        'title' => 'Password Confirmation',
        'oninput' => 'pwd_should_match()'
    ));
    echo("</div>");

    echo("<div>");
    echo form_submit(array(
        'id' => 'btn',
        'name' => 'accounts',
        'type' => 'Submit',
        'class' => 'btn btn-info',
        'value' => 'Create Professor User'
    ));
    echo("</div>");

    echo form_close()
    ?>
</div>
-->

<?php
echo form_open('match/saveMinimum', array(
    'class' => '',
));
?>
<div class="well">
<div class="text-center">
    <h4>Update rank minimum</h4>
</div>
<?php
echo("<div>");
echo form_input(array(
    'id' => 'rank',
    'name' => 'rank',
    'value' => $minimum,
    'required' => '',
));
echo("</div>");

echo("<div>");
echo form_submit(array(
    'id' => 'btn',
    'name' => 'min',
    'type' => 'Submit',
    'class' => 'btn btn-info',
    'value' => 'Save Minimum'
));
echo("</div>");

echo form_close()
?>
</div>

    <!--added on SPW v5 to set default email notification-->
    <?php
        echo form_open('admin/setDefafultEmail', array(
            'class' => '',
        ));
    ?>
    <div class="well">
    <div class="text-center">
        <h4>Set Default Email For VM Creation</h4>
    </div>
    <?php 
    
    echo("<div>");
    echo form_label('Name:');
    echo form_input(array(
                        'id' => 'full_name',
                        'name' => 'full_name',
                        'type' => 'text',
                        'placeholder' => 'Full Name',
                        'required' => '',
                        'title' => 'Full Name'
                        ));
    echo("</div>");
    
    echo("<div>");
    echo form_label('Email:');
    echo form_input(array(
                        'id' => 'email_address',
                        'name' => 'email_address',
                        'type' => 'email',
                        'placeholder' => 'email@example.com',
                        'value' => set_value('email_address'),
                        'required' => '',
                        'title' => 'Email address'
                        ));
    echo("</div>");

    echo("<div>");
    echo form_submit(array(
        'id' => 'btn',
        'name' => 'min',
        'type' => 'Submit',
        'class' => 'btn btn-info',
        'value' => 'Set Default Email'
    ));
    echo("</div>");
    $default_info = SPW_Term_Model::getInstance();
    $info = $default_info->getDefaultEmailAndName();
    echo form_close()
    ?>
    <br>
    <br>
    <table class="table" >
        <thead>
            <tr>
                <th>Full Name</th>
                <th>Default Email</th>
            </tr>
        </thead>
        <tr class="info">
            <!--display current default name and email--> 
           <?php foreach($info as $row):?>
            <td> <?php echo $row->full_name ?> </td>
            <td> <?php echo $row->email ?> </td>
            <?php endforeach;?>
        </tr>
    </table>        
</div>


<!--
<?php
echo form_open('admin/activate_deactive_users', array(
    'class' => '',
    'id' => 'act_deact_form'
));
?>

<div class="well">
    <div class="text-center">
        <h4>Active and Deactivate Users</h4>
    </div>
    <table class = "table table-striped">
        <thead>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
            </tr>
        </thead>
<?php
//select all the users that are in the system (except the head professor).
$query = $this->db->query('SELECT id, first_name, last_name, email, role, status FROM spw_user
                                    WHERE role != "HEAD"
                                    ORDER BY role, last_name');

foreach ($query->result_array() as $row) {
    if ($row['status'] == 'ACTIVE')
        echo "<tr class=\"success\">";
    else
        echo "<tr class=\"error\">";
    echo "<td>" . $row['first_name'] . "</td>";
    echo "<td>" . $row['last_name'] . "</td>";
    echo "<td>" . $row['email'] . "</td>";
    echo "<td>" . $row['role'] . "</td>";
    echo "<td>" . $row['status'] . "</td>";
    echo "</tr>";
}
?>
    </table>

    <?php echo 'Choose an action to apply:' ?>
    <br>
    <br>

    <div text-align: center>
        <label class="radio">
            <input type="radio" name="action" id="act" value="Activate" checked>
            Activate the selected user(s)
        </label>
        <label class="radio">
            <input type="radio" name="action" id="deact" value="Deactivate">
            Deactivate the selected user(s)
        </label>
    </div>

    <br>

    <?php
    echo form_submit(array(
        'id' => 'btn-act-deact',
        'name' => 'activate',
        'type' => 'Submit',
        'class' => 'btn btn-info',
        'value' => 'Execute Changes'
    ));

    echo form_close()
    ?>
</div>
-->
<?php $this->load->view("template_footer"); ?>
