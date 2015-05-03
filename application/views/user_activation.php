<?php $this->load->view("template_header"); ?>

<!-- added scripts for the date picker-->
<script src="<?php echo base_url() ?>js/jquery-1.9.1.js"></script>
<script src="<?php echo base_url() ?>js/jquery-ui.js"></script>
<script src="<?php echo base_url() ?>js/jquery-accordion.js"></script>

<script type="text/javascript">
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
 
<h2>Edit User Information</h2>
<!-- Start Well for User Information -->
<div class="well">
	<?php
		if( isset( $first_name ) && isset( $last_name )  && isset( $email_address ) && isset( $role ) )
		{
			echo( "<table class=\"well\" style=\"width: 99%;\">" );
				echo( "<tr>" );
				
					echo( "<td style=\"padding: 15px;\">" );
						echo( "<img src=\"" . $picture . "\" >" );
					echo( "</td>" );
					
					echo( "<td style=\"padding: 15px;\">" );
						echo form_open( 'admin/activate_set_pwd', array( 'id' => 'activate_user' ) );
						
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
							echo form_hidden( 'id', $id );
							echo form_hidden( 'email_address', $email_address );
              				echo form_submit(array(
                                     'id' => 'status_btn',
                                     'name' => 'accounts',
                                     'type' => 'Submit',
                                     'class' => 'btn btn-info',
                                     'value' => 'Set Password'
                                     ) );
							
							echo form_close( );
              			echo("</div>");
						
					echo( "</td>" );
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
