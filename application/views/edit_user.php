<?php $this->load->view("template_header"); ?>

<!-- added scripts for the date picker-->
<script src="<?php echo base_url() ?>js/jquery-1.9.1.js"></script>
<script src="<?php echo base_url() ?>js/jquery-ui.js"></script>
<script src="<?php echo base_url() ?>js/jquery-accordion.js"></script>
 
<h2>Edit User</h2>
<!-- Start Well for Head Professor Dashboard -->
<div class="well">
	<?php
		if( isset( $edit_user ) && isset( $first_name ) && isset( $last_name ) 
			&& isset( $email_address ) && isset( $role ) && isset( $id ) )
		{
			echo( "<table class=\"well\" style=\"width: 99%;\">" );
				echo( "<tr>" );
				
					echo( "<td style=\"padding: 15px;\">" );
						if( $picture == NULL )
							echo( "<img src=\"" . base_url( '/img/no-photo.jpeg' ) . "\" >" );
						else
							echo( "<img src=\"" . $picture . "\" >" );
					echo( "</td>" );
					
					echo( "<td style=\"padding: 15px;\"><center>" );
						echo form_open( 'admin/edit_user', array( 'id' => 'edit_user_form' ) );
						
						echo("<div>");
					  		echo form_input(array(
											'id' => 'first_name',
											'name' => 'first_name',
											'type' => 'text',
											'value' => $first_name,
											'required' => '',
											'title' => 'First Name'
											));
             			echo("</div>");
              			echo("<div>");
              				echo form_input(array(
                                    'id' => 'last_name',
                                    'name' => 'last_name',
                                    'type' => 'text',
                                    'value' => $last_name,
                                    'required' => '',
                                    'title' => 'Last Name'
                                    ));
              			echo("</div>");
              			echo("<div>");
              				echo form_input(array(
                                    'id' => 'email_address',
                                    'name' => 'email_address',
                                    'type' => 'email',
                                    'value' => $email_address,
                                    'required' => '',
                                    'title' => 'Email address'
                                    ));
              			echo("</div>");
			   			echo( "<div style=\"padding-bottom: 10px;\">" );
			   				echo form_dropdown( 'role', array( 'STUDENT' => 'STUDENT', 'PROFESSOR' => 'PROFESSOR' ), $role );
              			echo( "</div>" ); 
              
              			echo("<div>");
							echo form_hidden( 'id', $id );
              				echo form_submit(array(
                                     'id' => 'btn',
                                     'name' => 'accounts',
                                     'type' => 'Submit',
                                     'class' => 'btn btn-info',
                                     'value' => 'Edit User'
                                     ));
							
							echo form_close( );
              			echo("</div>");
						
					echo( "</center></td>" );
				echo( "</tr>" );
			echo( "</table>" );
		}
		else if( isset( $change_status ) && isset( $email_address ) && isset( $id ) )
		{
			echo( "<table class=\"well\" style=\"width: 99%;\">" );
				echo( "<tr>" );
				
					echo( "<td style=\"padding: 15px;\">" );
						if( $picture == NULL )
							echo( "<img src=\"" . base_url( '/img/no-photo.jpeg' ) . "\" >" );
						else
							echo( "<img src=\"" . $picture . "\" >" );
					echo( "</td>" );
					
					echo( "<td style=\"padding: 15px;\"><center>" );
						echo form_open( 'admin/change_status', array( 'id' => 'edit_user_form' ) );
						
						echo("<div>");
							echo $first_name;
             			echo("</div>");
						
              			echo("<div>");
              				echo $last_name;
              			echo("</div>");
						
              			echo("<div>");
              				echo $email_address;
              			echo("</div>");
						
			   			echo( "<div>" );
			   				echo $role;
              			echo( "</div>" ); 
						
						echo( "<div style=\"padding-bottom: 10px;\">" );
			   				echo form_dropdown( 'status', array( 'ACTIVE' => 'ACTIVE', 
																	'INACTIVE' => 'INACTIVE',
																	'PENDING' => 'PENDING' ), 'ACTIVE' );
              			echo( "</div>" );
              
              			echo("<div>");
							echo form_hidden( 'id', $id );
							echo form_hidden( 'email_address', $email_address );
              				echo form_submit(array(
                                     'id' => 'status_btn',
                                     'name' => 'accounts',
                                     'type' => 'Submit',
                                     'class' => 'btn btn-info',
                                     'value' => 'Change User Status'
                                     ) );
							
							echo form_close( );
              			echo("</div>");
						
					echo( "</center></td>" );
				echo( "</tr>" );
			echo( "</table>" );
		}
		else
		{
			$msg = 'Cannot edit user: 
				   ' . $email_address . '<br>There is missing information!';
                setErrorFlashMessage( $this, $msg );
				
			redirect( '' );
		}
    ?>
</div>
<br>

<!-- End Well for Head Professor Dashboard -->
 
 
<?php $this->load->view("template_footer"); ?>
