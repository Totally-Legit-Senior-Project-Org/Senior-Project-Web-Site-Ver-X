<?php $this->load->view("template_header"); ?>

<!-- added scripts for the date picker-->
<script src="<?php echo base_url() ?>js/jquery-1.9.1.js"></script>
<script src="<?php echo base_url() ?>js/jquery-ui.js"></script>
<script src="<?php echo base_url() ?>js/jquery-accordion.js"></script>
<script>
	function delete_alert( )
	{
		var r = confirm( "Are you sure you want to delete this user?" );
		
		return r;
	}
	
	function bypass_alert( )
	{
		var r = confirm( "Are you sure you want to bypass user activation?" );
		
		return r;
	}
</script>
  
<style>
.subsection {
  padding: 19px;
  margin-bottom: 20px;
  width: 200px;
  display:inline-block;
  float: right;
  background-color: #f5f5f5;
  border: 1px solid #e3e3e3;
  -webkit-border-radius: 4px;
     -moz-border-radius: 4px;
          border-radius: 4px;
  -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
     -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
          box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
}

.frame {
  padding: 8px;
  margin-top: 240px;
  margin-left: 4px;
  width: 80px;
  display:inline-block;
  float: left;
  background-color: #DCDCDC;
  border: 1px solid #e3e3e3;
  -webkit-border-radius: 4px;
     -moz-border-radius: 4px;
          border-radius: 4px;
  -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
     -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
          box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
}

.subsection blockquote {
  border-color: #ddd;
  border-color: rgba(0, 0, 0, 0.15);
}

.subsection-large {
  padding: 24px;
  -webkit-border-radius: 6px;
     -moz-border-radius: 6px;
          border-radius: 6px;
}

.subsection-small {
  padding: 9px;
  -webkit-border-radius: 3px;
     -moz-border-radius: 3px;
          border-radius: 3px;
}
</style>
<?php
    echo form_open('user/search_users', array(
        'class' => 'form-inline',
        'id' => 'search_users_form'
    ));
?>
<h2>User Management</h2>
<!-- Start Well for Head Professor Dashboard -->
 <div class="well" style="min-height: 270px;">
<div class="subsection">
<div style="margin-top: -10px"> <h3> Filter Users </h3> </div>
<?php
	echo( "<div style='margin-left: -10px'>" );
	echo form_checkbox( array(
									'name' => 'all_users',
									'id' => 'all_users',
									'value' => 'all_users',
									'checked' => FALSE,
									'style' => 'margin: 10px' ) );
	echo( "All Users" );
	echo( "</div>" );
	echo( "<div style='margin: -10px'>" );
	echo form_checkbox( array( 
									'name' => 'active',
									'id' => 'status_active',
									'value' => 'active',
									'checked' => FALSE,
									'style' => 'margin: 10px' ) );
	echo( "Status: active" );
	echo( "</div>" );
	echo( "<div style='margin: -10px'>" );
	echo form_checkbox( array( 
									'name' => 'inactive',
									'id' => 'status_inactive',
									'value' => 'inactive',
									'checked' => FALSE,
									'style' => 'margin: 10px' ) );
	echo( "Status: inactive" );
	echo( "</div>" );
	echo( "<div style='margin: -10px'>" );
	echo form_checkbox( array( 
									'name' => 'pending',
									'id' => 'status_pending',
									'value' => 'pending',
									'checked' => FALSE,
									'style' => 'margin: 10px' ) );
	echo( "Status: Pending" );
	echo( "</div>" );
	echo( "<div style='margin: -10px'>" );
	echo form_checkbox( array( 
									'name' => 'student',
									'id' => 'role_student',
									'value' => 'student',
									'checked' => FALSE,
									'style' => 'margin: 10px' ) );
	echo( "Role: Student" );
	echo( "</div>" );
	echo( "<div style='margin-left: -10px'>" );
	echo form_checkbox( array(
									'name' => 'professor',
									'id' => 'role_professor',
									'value' => 'professor',
									'checked' => FALSE,
									'style' => 'margin: 10px' ) );
	echo( "Role: Professor" );
	echo( "</div>" );
	
	echo form_submit(array(
           'id' => 'btn',
           'name' => 'search',
           'type' => 'Submit',
           'class' => 'btn btn-info',
           'value' => 'Search Filtered Users'
       ));
	   
	echo form_close( );
?>
</div>
<div class="frame">
 <input type="checkbox" id="selectall"/> Select All
</div>
<br>
<?php echo form_open('admin/activate_deactive_users', array(
        'class' => '',
        'id' => 'act_deact_form'
    ));
?>
<center><table>
<?php
	$where = "";
	if( isset( $inactive ) )
		$where .= "&& status = \"" . $inactive . "\" ";
	if( isset( $active ) )
		$where .= "&& status = \"" .$active . "\" ";
	if( isset( $pending ) )
		$where .= "&& status = \"" .$pending . "\" ";
	if( isset( $student ) )
		$where .= "&& role = \"" .$student . "\" ";
	if( isset( $professor ) )
		$where .= "&& role = \"" .$professor . "\" ";
		
	if( isset( $all_users ) || strlen( $where ) <= 3 )
	{
		$query = $this->db->query( 'SELECT id, first_name, last_name, email, picture, status, role, positions_linkedIn, hash_pwd
								  FROM spw_user
								  WHERE role != "HEAD"
								  ORDER BY role, last_name' );
	}
	else if( strlen( $where ) > 3 )
	{
		$query = $this->db->query( 'SELECT id, first_name, last_name, email, picture, status, role, positions_linkedIn, hash_pwd
								  FROM spw_user
								  WHERE role != "HEAD" ' . $where . '
								  ORDER BY role, last_name' );
	}
	
	foreach( $query->result_array( ) as $row )
	{ 
            //temporary fix for nested forms problem.
            echo form_open();
            echo form_close();
            
		echo( "<tr>" );
			echo( "<table class=\"well\" style=\"width:99%;\">" );
				echo( "<tr>" );
					echo( "<td style=\"padding: 15px;\">" );
						if( $row['picture'] == NULL )
							echo( "<img src=\"" . base_url( '' . getImage($row['id'])) . "\" height=\"80\" width=\"80\">" );
                                                else
							echo( "<img src=\"" . $row['picture'] . "\" height=\"80\" width=\"80\">" );
					echo( "<br /><br />" );
					echo form_open( 'admin/impersonate', array( 'id' => 'impersonate_form' ) );
					echo form_hidden( array( 
							  'email_address' => $row[ 'email' ],
							  'password' => $row[ 'hash_pwd' ],
							  'role' => $row[ 'role' ],
							  'id' => $row[ 'id' ]
							  ) );
						echo( "<a href=\"javascript:void(0);\" onClick=\"$(this).closest('form').submit();\">" );
							echo( "Act As User" );
						echo( "</a>" );
					echo form_close( );
					echo( "</td>" );
					echo( "<td style=\"padding: 15px; width: 50%; text-align: center;\">" );
              	    	echo( "<p>" );
                  		echo( $row['first_name'] . " " . $row['last_name'] ); echo( "<br />" );
                         echo( $row['email'] ); echo( "<br />" );
						 	if( $row['positions_linkedIn'] != NULL)
                         	{
								echo( $row['positions_linkedIn'] ); 
								echo( "<br />" );
							}
                         echo( $row['role'] ); echo( "<br />" );
						 	echo( $row['status'] );
                    	echo( "</p>" );
                  echo( "</td>" );
                  echo( "<td style=\"padding: 15px;\">" );
                  	echo( "<p>" );
							
							/* Edit User functionality */
								echo form_open( 'user/edit_user', array( 'id' => 'edit_form' ) );
									echo form_hidden( array( 
										 'first_name' => $row[ 'first_name' ],
										 'last_name' => $row[ 'last_name' ],
										 'role' => $row[ 'role' ],
										 'email_address' => $row[ 'email' ], 
										 'picture' => $row[ 'picture' ], 
										 'userID' => $row[ 'id' ],
										 'edit_user' => TRUE
										 ) );
									echo form_submit(array(
                                     'name' => 'accounts',
                                     'type' => 'Submit',
                                     'class' => 'btn btn-info',
                                     'value' => 'Edit User'
                                     )); 
								echo form_close( );
								
							/* Delete User Functionality */
							   echo form_open( 'admin/delete_user', array( 'id' => 'delete_form' ) );
									echo form_hidden( array( 
										 'email_address' => $row[ 'email' ], 
										 'userID' => $row[ 'id' ],
										 ) );
									echo form_submit(array(
                                     'name' => 'accounts',
                                     'type' => 'Submit',
                                     'class' => 'btn btn-info',
                                     'value' => 'Delete User',
									 	 'onClick' => 'return delete_alert( )'
                                     ) ); 
								echo form_close( );

							   echo form_open( 'user/change_status', array( 'id' => 'change_status_form' ) );
									echo form_hidden( array( 
										 'first_name' => $row[ 'first_name' ],
										 'last_name' => $row[ 'last_name' ],
										 'role' => $row[ 'role' ],
										 'email_address' => $row[ 'email' ], 
										 'userID' => $row[ 'id' ],
										 'picture' => $row[ 'picture' ],
										 'change_status' => TRUE
										 ) );
									echo form_submit(array(
                                     'name' => 'accounts',
                                     'type' => 'Submit',
                                     'class' => 'btn btn-info',
                                     'value' => 'Change User Status',
                                     ) ); 
								echo form_close( );
							
							if( $row[ 'status' ] === "PENDING" )
							{ 
								echo form_open( 'admin/bypass_activation', array( 'id' => 'change_status_form' ) );
									echo form_hidden( array( 
										 'email_address' => $row[ 'email' ], 
										 'userID' => $row[ 'id' ]
										 ) );
									echo form_submit( array(
                                     'name' => 'accounts',
                                     'type' => 'Submit',
                                     'class' => 'btn btn-info',
									 	 'onClick' => 'return bypass_alert( )',
                                     'value' => 'Bypass Activation',
                                     ) ); 
								echo form_close( );
							}
                      echo( "</p>" );
                  echo( "</td>" );
		   if( $row[ 'status' ] === "ACTIVE" )
                                        {
                                                echo( "<td style=\"background-color: green;\"><center>" );
                                                        echo( "<label>Deactivate</label><br><br>" );
                                                        echo( "<input class=\"checkbox\" type=\"checkbox\" name=\"users[]\" value=\"" . $row[ 'id' ] . "\">" );
                                                echo( "</center></td>" );
                                        }
                                        else if( $row[ 'status' ] === "INACTIVE" )
                                        {
                                                echo( "<td style=\"background-color: red;\"><center>" );
                                                        echo( "<label>Activate&nbsp;&nbsp;&nbsp;&nbsp;</label><br><br>" );
                                                        echo( "<input class=\"checkbox\" type=\"checkbox\" name=\"users[]\" value=\"" . $row[ 'id' ] . "\">" );
                                                echo( "</center></td>" );
                                        }
              echo( "</tr>" );
           echo( "</table>" );
       echo( "</tr>" ); 
	} 
?>
</table>
<br><br>
<?php echo 'Choose an action to apply:'; ?>
<div text-align: center>
        <label class="radio">
        <input type="radio" name="action" id="act" value="Activate" checked>
                Activate the selected user(s)
        </label>
        <label class="radio">
        <input type="radio" name="action" id="deact" value="Deactivate">
                Deactivate the selected user(s)
        </label>
    <br><br>
    <?php
         echo form_submit(array(
            'id' => 'btn-act-deact',
            'name' => 'activate',
            'type' => 'Submit',
            'class' => 'btn btn-info',
            'value' => 'Execute Changes'
        ));

        echo form_close( );
        ?>
</div></center>
</div>
<!-- End Well for Head Professor Dashboard -->
 
 
<?php $this->load->view("template_footer"); ?>

<script> 
    $(document).ready(function() {
    $('#selectall').click(function(event) {
        if(this.checked) {
            $('.checkbox').each(function() {
                this.checked = true;              
            });
        }else{
            $('.checkbox').each(function() {
                this.checked = false;                     
            });         
        }
    });
    
    });
</script>
