
<?php $this->load->view("template_header"); ?>
<?php $this->load->helper("user_image"); ?>



<?php 
    if ($no_results) 
    {
?>
        <p>No data for the specified user</p>
<?php 
    } 
    else 
    {
$spw_id = getCurrentUserId($this);
        
//check if the user has uploaded a picture already
$file = checkUserUploadedPic($this, $spw_id);

if ($file != null)
{
        ?>
        <button type="button" class="btn btn-large btn-primary pull-right" data-toggle="modal" data-target="#LinkedInModal">Sync with LinkedIn</button>
                <!-- Modal -->
                <div class="modal modal-narrow fade span4 center-text row-fluid" id="LinkedInModal" tabindex="-1" role="dialog" aria-labelledby="LinkedInModalLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h3 class="modal-title" id="LinkedInModalLabel">Replacing picture</h3>
                      </div>
                      <div class="modal-body">
                          <p>Synchronization with LinkedIn will replace your current picture with LinkedIn picture.</p>
                          <p>Would you like to proceed?</p>
                      </div>
                      <div class="modal-footer">
                        <?php echo form_open('/user/linkedIn_sync')?>
                        <button class="btn btn-primary span6" type="submit">Yes</button>
                        <button class="btn btn-default span6" data-dismiss="modal">No</button>
                        <?php echo form_close()?>
                      </div>
                    </div>
                  </div>
                </div>
                
<?php }  else { echo anchor('/user/linkedIn_sync', 'Sync with LinkedIn', array('class' => 'btn btn-primary btn-large pull-right')); }?>

        <?php if(strcmp($userDetails->user->role ,"HEAD") == 0){ ?>
            <h2>Head Professor Profile</h2>
        <?php } else if(strcmp($userDetails->user->role ,"STUDENT") == 0){ ?>
            <h2>Student Profile</h2>
        <?php } else if (strcmp($userDetails->user->role ,"PROFESSOR") == 0){ ?>
            <h2>Professor Profile</h2>
        <?php } ?>

        

<div>
    <div class="row-fluid">
        <div class="well span4 center-text">
            <?php 
                //first check if the user has an img mapping in the db
                $src = getUserImage($this, $userDetails->user->picture);
                if ($src == '/img/no-photo.jpeg')
                {
                    $src = checkUserUploadedPic($this, $userDetails->user->id);
                    if($src == null)
                        $src = '/img/no-photo.jpeg';
                }
                echo img(array(
                    'id' => 'img-user-profile',
                    'src' => $src . '?x='. time(),//getUserImage($this, $userDetails->user->picture),
                    'class' => 'user-img-large',
                    'alt' => $userDetails->getFullName()
                ));  
                
                
            ?>
            <div>
                <?php echo form_open_multipart('usercontroller/do_upload');?>
                <input class="btn-small" type="file" name="userfile" size="20" style="margin-top: 10px; margin-bottom: 5px" />
                
                <?php if ($file != null) { ?>
                
                <button type="button" class="btn-small btn-primary" data-toggle="modal" data-target="#UploadModal">Upload Picture</button>
                <!-- Modal -->
                <div class="modal modal-narrow fade" id="UploadModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h3 class="modal-title" id="myModalLabel">Replacing picture</h3>
                      </div>
                      <div class="modal-body">
                          <p>Uploading a new picture will replace your current one.</p>
                          <p>Would you like to proceed?</p>
                      </div>
                      <div class="modal-footer">
                        <input class="btn btn-primary span6" type="submit" value="Yes">
                        <input class="btn btn-default span6" data-dismiss="modal" value="No">
                      </div>
                    </div>
                  </div>
                </div>
                <?php }else { ?>
                <input class="btn-small btn-primary" type="submit" value="Upload Picture">    
                <?php } ?>
                <?php echo form_close() ?>
            </div>
        </div>

        <div class="span8">
            <?php 
                echo form_open('usercontroller/update', array(
                    'id' => 'form-update-user'
                 )); 
            
                echo form_input(array(
                    'id' => 'text-first-name',
                    'name' => 'text-first-name',
                    'type' => 'text',
                    'class' => 'input-small',
                    'placeholder' => 'First Name...',
                    'value' => $userDetails->user->first_name,
                    'required' => '',
                    'title' => 'First Name'
                ));
            ?>

            <?php 
                echo form_input(array(
                    'id' => 'text-last-name',
                    'name' => 'text-last-name',
                    'type' => 'text',
                    'class' => 'input-large',
                    'placeholder' => 'Last Name...',
                    'value' => $userDetails->user->last_name,
                    'required' => '',
                    'title' => 'Last Name'
                ));
            ?>

            <?php
                if (isset($userDetails->user->email) && 
                    strlen($userDetails->user->email) > 0)
                {
            ?> 
                    <p>
                        <?php echo mailto($userDetails->user->email, $userDetails->user->email) ?>
                    </p>
            <?php
                }
            ?>

            <?php if (isset($canChangePassword) && $canChangePassword && 
                    (strcmp($userDetails->user->role ,"PROFESSOR") == 0 || strcmp($userDetails->user->role ,"HEAD") == 0 || strcmp($userDetails->user->role ,"STUDENT") == 0)) { ?>
                <p>
                    <?php echo anchor('change-password', 'Click to change password') ?>
                </p>
            <?php } ?>
        </div>
    </div>


                <div class="spaced-top">
                    <?php if (isset($userDetails->lSkills) && count($userDetails->lSkills) > 0) { ?>
                        <h4>Skills</h4>
                        <?php $this->load->view('subviews/skills_list', array('lSkills' => $userDetails->lSkills) )?>
                    <?php }?>
                </div>

                <div class="spaced-top">
                    <?php if (isset($userDetails->lLanguages) && count($userDetails->lLanguages) > 0) { ?>
                        <h4>Languages</h4>
                        <?php $this->load->view('subviews/skills_list', array('lSkills' => $userDetails->lLanguages) )?>
                    <?php }?>
                </div>

                <div class="spaced-top">

                        <h4>Short Bio</h4>

                        <?php 
                            echo form_textarea(array(
                                'id' => 'text-description',
                                'name' => 'text-description',
                                //'class' => 'input-large',
                                'rows' => '12',
                                'placeholder' => 'Tell us a little bit about yourself...',
                                'value' => $userDetails->user->summary_spw,
                                'required' => '',
                                'Title' => 'Project Description'
                            ));
                        ?>

                </div>

                <?php 
                    echo form_submit(array(
                        'id' => 'btn-submit',
                        'name' => 'btn-submit',
                        'type' => 'Submit',
                        'class' => 'btn btn-large btn-primary pull-right',
                        'value' => 'Save Changes'
                    ));
                ?>

                <div class="spaced-top">
                    <?php if(isset($userDetails->user->summary_linkedIn) && strlen($userDetails->user->summary_linkedIn) > 0) {?>
                        <h4>Linked In Summary</h4>
                        <?php echo $userDetails->user->summary_linkedIn ?>
                    <?php }?>
                </div>

                <div class="spaced-top">
                    <?php $this->load->view('subviews/experience_list', array('lExperiences' => $userDetails->lExperiences)) ?>
                </div>

                <div class="clearfix"></div>

            </div>

        <?php echo form_close() ?>


        <script type="text/javascript">
            $(document).ready(function(){
                $('#text-img-url-container').hide();

                $('#link-change-image').click(function(e){
                    e.preventDefault();
                    e.stopPropagation();

                    $('#link-change-image-container').hide();
                    $('#text-img-url-container').show();
                });

                $('#link-change-image-cancel').click(function(e){
                    e.preventDefault();
                    e.stopPropagation();

                    $('#text-img-url-container').hide();
                    $('#link-change-image-container').show();
                });

                $('#link-change-image-ok').click(function(e){
                    e.preventDefault();
                    e.stopPropagation();

                    var newImgSrc = $('#text-img-url').val();

                    //alert(newImgSrc);

                    $('#img-user-profile').attr('src', newImgSrc);
                    $('input[name=hidden-img-src]').val(newImgSrc);

                    $('#text-img-url-container').hide();
                    $('#link-change-image-container').show();
                });
            });
        </script>
<?php 
    }
?>

<?php $this->load->view("template_footer"); ?>