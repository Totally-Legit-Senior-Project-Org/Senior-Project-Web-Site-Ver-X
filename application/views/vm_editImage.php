<?php $this->load->view("template_header"); ?>
<?php
        
    echo("<h4> Edit Image </h4>");    
    echo("<div class=\"well\">");
    echo("<div class=\"text-center\">");
    
        echo form_open('projectcontroller/editImage', array(
                                                                    'class' => '',
                                                                    'id' => ''
                                                                    ));
                $data = array( 
                    'old_image_name' => $image_name
                    );
                echo form_hidden($data);
                echo('<h4> Image Name: '.$image_name.' </h4>');
                if($status === 'INACTIVE'){
                    echo('<h4>Image Status: <span class="label label-warning">'."$status".'</span></h4>');
                }
                if($status === 'ACTIVE'){
                     echo('<h4>Image Status: <span class="label label-success">'."$status".'</span></h4>');
                }
                      echo form_input(array(
                        'id' => 'image_name',
                        'name' => 'image_name',
                        'type' => 'text',
                        'placeholder' => $image_name,
                        'required' => '',
                        'title' => 'Image Name'
                        ));
                echo( "<br>" );
                echo( "<br>" );
                echo form_submit(array(
                             'id' => 'btn',
                             'name' => 'accounts',
                             'type' => 'Submit',
                             'class' => 'btn btn-info',
                             'value' => 'Submit'
                             ));
        echo form_close( );
    
    echo("</div>");
    echo("</div>");
    
    
     
    
?>
<?php $this->load->view("template_footer"); ?>