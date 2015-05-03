<?php $this->load->view("template_header"); ?>
<?php $this->load->helper("skills"); ?>
<?php $this->load->helper("deadline_term"); ?>
<!-- <?php $this->load->helper("loading"); ?> -->
<!-- edit the current project -->

<!-- START displaying server-side validation errors -->
<?php
    $fullErrorText = validation_errors();

    if (strlen($fullErrorText) > 0)
    { 
?>
        <div class="alert alert-error">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
            <div class="errors"> 
<?php 
        echo $fullErrorText;
?>
            </div>
        </div>
<?php
    }
?>

<!-- END displaying server-side validation errors -->



<?php 
//    if (isset($can_leave_project) && $can_leave_project)
//    {
        if (!isset($creating_new))
        {
            echo form_open('projectcontroller/delete', array(
                'id' => 'form-delete-project'
            ));

            echo form_hidden(array(
                    'pid' => $projectDetails->project->id,
                    'pbUrl' => current_url()
                ));

            //<button id='btn-leave' type="button" class="btn btn-warning btn-large pull-right">Leave Project</button>
            echo form_submit(array(
                    'id' => 'btn-delete',
                    'name' => 'btn-delete',
                    'type' => 'Submit',
                    //'onSubmit' => 'submitForm()',
                    'class' => 'btn btn-danger pull-right hor-margin',
                    'value' => 'Delete Project'
                ));

            echo form_close();
        }
    //}

?>

<?php 
?>

<?php if (isset($creating_new) && $creating_new) { ?>
   <?php if(isset($isUserHeadProfessor)) { ?>
    <h2>Create Project</h2>
    <?php } else { ?>
    <h2>Propose Project</h2>
    <?php } ?>
<?php } else { ?>
    <h2>Edit Project</h2>
<?php } ?>

<div>
    <?php 
        echo form_open('projectcontroller/update', array(
            'id' => 'form-edit-project'
        ));
    ?>
        <div class="row-fluid">
            <div class="span3">

                <?php 
                    echo form_input(array(
                        'id' => 'text-project-title',
                        'name' => 'text-project-title',
                        'type' => 'text',
                        'class' => 'input-large',
                        'placeholder' => 'Enter the project title...',
                        'value' => $projectDetails->project->title,
                        'required' => '',
                        'title' => 'Project Title'
                    ));
                ?>
            </div>

            <div class="span9">
                <div class="pull-right">
                    <!--<input type="text" name="tags" placeholder="Tags" class="tagManager"/> -->
                    <?php 
                        echo form_input(array(
                            'id' => 'text-new-tag',
                            'name' => 'text-new-tag',
                            'type' => 'text',
                            'class' => 'tagManager input-small',
                            'placeholder' => 'Enter skills...'
                        ));
                    ?>
                </div>
            </div>
        </div>


        <?php 
            echo form_textarea(array(
                'id' => 'text-description',
                'name' => 'text-description',
                //'class' => 'input-large',
                'rows' => '12',               
                'placeholder' => 'Enter a description for the project...',
                'value' => $projectDetails->project->description,
                'required' => '',
                'Title' => 'Project Description'
            ));
        ?>
            
            </div>
    
        <div class="pull-right">
            <?php 
                echo anchor('/', 'Cancel', array('class' => 'btn btn-large'))
            ?>

            <?php 
                echo form_submit(array(
                    'id' => 'btn-save-changes',
                    'name' => 'btn-save-changes',
                    'type' => 'Submit',
                    'class' => 'btn btn-large btn-primary',
                    'value' => 'Save Changes'
                ));
            ?>
        </div>

        <div>     
            <p>
                <h4 class="muted inline">Maximum project capacity: </h4>
                <?php 
                    $arrTermsOptions = array();

                    for ($i = 2; $i <= 6; $i++) 
                    {
                        $arrTermsOptions[$i] = $i.' students';
                    }

                    echo form_dropdown('text-project-max-students', $arrTermsOptions, $projectDetails->project->max_students);
                ?>
            </p>

            <?php if ((!isset($creating_new) || !$creating_new)) { ?>                
                <?php if(isset($isUserHeadProfessor)){    ?>  
                <p>
                    <h4 class="muted inline">Change Project status: </h4>
                    <?php 
                        $arrStatusOptions = array();

                        if($projectDetails->statusName == "PENDING APPROVAL")
                        {
                            $arrStatusOptions['PENDING APPROVAL'] = 'PENDING APPROVAL';
                            $arrStatusOptions['APPROVED'] = 'APPROVED';
                            $arrStatusOptions['REJECTED'] = 'REJECTED';  
                            echo form_dropdown('text-status', $arrStatusOptions, $projectDetails->statusName);
                        }
                        else if($projectDetails->statusName == "APPROVED")
                        {
                            $arrStatusOptions['APPROVED'] = 'APPROVED';
                            $arrStatusOptions['PENDING APPROVAL'] = 'PENDING APPROVAL';
                            $arrStatusOptions['CLOSED'] = 'CLOSED';  
                            echo form_dropdown('text-status', $arrStatusOptions, $projectDetails->statusName);
                        }
                        else if($projectDetails->statusName == "REJECTED")
                        {
                            $arrStatusOptions['REJECTED'] = 'REJECTED'; 
                            $arrStatusOptions['APPROVED'] = 'APPROVED';
                            $arrStatusOptions['PENDING APPROVAL'] = 'PENDING APPROVAL';
                            echo form_dropdown('text-status', $arrStatusOptions, $projectDetails->statusName);
                        }
                        else if($projectDetails->statusName == "CLOSED")
                        {
                            $arrStatusOptions['CLOSED'] = 'CLOSED';
                            $arrStatusOptions['APPROVED'] = 'APPROVED';  
                            echo form_dropdown('text-status', $arrStatusOptions, $projectDetails->statusName);
                        
                        ?>
                       <?php }else{  ?>
                            <p>
                                 <ul class="unstyled inline">
               <h4 class="muted inline">Status:</h4>
                <?php if($projectDetails->statusName == "APPROVED"){?>
                <li class="label label-success"></span>          
                <?php }else if($projectDetails->statusName == "PENDING APPROVAL"){?>
                    <li class="label label-warning"></span>    
                <?php }else if($projectDetails->statusName == "REJECTED"){?>
                    <li class="label label-important"></span>   
                <?php }else if($projectDetails->statusName == "CLOSED"){?>
                    <li class="label" ></span>   
                <?php }?>
                <?php echo ucfirst($projectDetails->statusName) ?>
                    </li>
            </ul>
                       
                        </p>
                        <?php }  ?> 
                       
                </p>
                <?php }else{?>            
                <p>
                <ul class="unstyled inline">
                <h4 class="muted inline">Status:</h4>
                <?php if($projectDetails->statusName == "APPROVED"){?>
                <li class="label label-success"></span>          
                <?php }else if($projectDetails->statusName == "PENDING APPROVAL"){?>
                    <li class="label label-warning"></span>    
                <?php }else if($projectDetails->statusName == "REJECTED"){?>
                    <li class="label label-important"></span>   
                <?php }else if($projectDetails->statusName == "CLOSED"){?>
                    <li class="label" ></span>   
                <?php }?>
                <?php echo ucfirst($projectDetails->statusName) ?>
                    </li>
            </ul>
                   
                </p>
            <?php } ?>
                <?php } ?>
        </div>

          <?php if($projectDetails->project->mentor != ''){ ?> 
            <h4 class="muted inline ">Mentor:</h4> 
                 <?php echo anchor('user/'.$projectDetails->project->mentor , $this->spw_user_model->get_fullname($projectDetails->project->mentor));?>
        <?php if(isset($isUserHeadProfessor) && $projectDetails->statusName != "CLOSED"){ ?>
            
        <body>
            <p>
	<h4 class="muted inline">Change the Mentor: 
         </h4>
          <?php 
                        $arrMentors = array();               
                        $arrMentors['no selection']= "";
                        foreach ($mentorList as $mentor) 
                        {                        
                            $arrMentors[$mentor->id] = $mentor->first_name." ".$mentor->last_name;                           
                        }        
                        
                        echo form_dropdown('text-new-mentor', $arrMentors, 'no selection');                    
                     ?>    
                </p>
	</body>
        <?php } ?>
        <?php } else if(isset($isUserHeadProfessor) && $projectDetails->statusName != "CLOSED"){ ?>
        <body>
	<h4 class="muted inline">Add a Mentor: </h4> 
         <?php 
                        $arrMentors = array();               
                        $arrMentors['no selection']= "";
                        foreach ($mentorList as $mentor) 
                        {                        
                            $arrMentors[$mentor->id] = $mentor->first_name." ".$mentor->last_name;                           
                        }                
                        
                        echo form_dropdown('text-new-mentor', $arrMentors, 'no selection');                    
                     ?>    
         </h4> 
	</body>
        <?php }else if(!isset($creating_new)){?>  
        <h4 class="muted inline ">Mentor:</h4> 
                          <?php  echo "This project does not have a mentor"?>  
                    <?php } ?> 
        
        <?php if (!isset($creating_new)) { ?>
            <div class="row-fluid"> 
                <div class="span2">
                    <?php $this->load->view('subviews/user_summaries_full_list_edit_project', array(
                        'listTitle' => 'Proposed By:',
                        'lUserSummaries' => array($projectDetails->proposedBySummary),
                        'topView' => '',
                        'bottomView' => '',
                        'prefix' => 'prop'
                    )) ?>
                </div>
                </div>
    
         <?php if($projectDetails->statusName != "CLOSED"){ ?>
             <?php if(isset($studentList) && count($studentList) > 0) { ?>      
             <p>
                    <h4 class="muted inline">Add Students to the Project: </h4>
                    <?php 
                        $arrStudents = array();               
                        $arrStudents['no selection']= "";
                        foreach ($studentList as $student) 
                        {                        
                            $arrStudents[$student->id] = $student->first_name." ".$student->last_name;                           
                        }                
                        
                        echo form_dropdown('text-new-student', $arrStudents, 'no selection');                    
                     ?>     
              </p>
                <?php } ?>    
                <?php } ?>  
    
    <?php } ?>
              
              <?php
            echo form_hidden(array(
                'pid' => $projectDetails->project->id,
                'pbUrl' => current_url()
                )
            );
        ?>
     
    <?php
        echo form_close();
    ?>

               <?php if (!isset($creating_new)) { ?>
               <?php $this->load->view('subviews/user_summaries_full_list_edit_project', array(
                'listTitle' => 'Team Members:',
                'lUserSummaries' => $projectDetails->lTeamMemberSummaries,
                'errorMessage' => 'This team has no members',
                'topView' => 'subviews/user_remove',
                'noTopViewForCurrentUser' => $projectDetails->statusName,
                'isUserProfessor' => $isUserProfessor,
                'bottomView' => '',
                'prefix' => 'usr'
            )); ?>        
              <?php } ?> 
              
   
<!--<script type="text/javascript">
$(document).ready(function() {
    $(function() {
        $( "#autocomplete" ).autocomplete({
            source: function(request, response) {
                $.ajax({ url: "<?php echo site_url('projectcontroller/autocomplete'); ?>",
                data: { mentor: $("#autocomplete").val()},
                dataType: "json",
                type: "POST",
                success: function(data){
                    response(data);
                },                       
            });
        },
        minLength: 2
        });
    });
});
</script>
   -->


<script type="text/javascript">
    function buildlUserIds(listId)
    {
        var hiddenFieldId = $('#' + listId).attr('data-idwithlist');

        //alert(listId);
        //alert(hiddenFieldId);

        var lUserIds = [];

        $('#' + listId + ' li').each(function(index){
            lUserIds.push($(this).attr('data-userid'));
        });

        var lUserIdsStr = lUserIds.join();
        //alert(lUserIdsStr);

        $('#' + hiddenFieldId).val(lUserIdsStr);

        var isListEmpty = (lUserIdsStr.length == 0);
        addErrorMessageToEmptyList(listId, isListEmpty);
    }

    function addErrorMessageToEmptyList(listId, isListEmpty)
    {
        if (isListEmpty)
        {
            //alert('empty list');

            var errorMessageStr = '';

            if (listId.indexOf('mnt') == 0) {
                errorMessageStr = 'This team needs a mentor...';
            }

            if (listId.indexOf('usr') == 0) {
                errorMessageStr = 'This team has no members';
            }

            if (errorMessageStr.length > 0) {
                $('#' + listId).append($('<li>' + errorMessageStr + '</li>'));
            }
        }
    }

    $(document).ready(function(){
       $('#btn-delete').click(function(e){
             e.preventDefault();
             e.stopPropagation();
         
            alertify.set({ labels: {
                    ok     : "Yes",
                    cancel : "No" }
                });
                
            alertify.confirm("Are you sure you want to delete the Project?", function (e) {
                if (e) {
                     document.getElementById('form-delete-project').submit();
                }
            });
        });
        
        $(".tagManager").tagsManager({
            //prefilled: ["Pisa", "Rome"],
            prefilled: [ <?php echo $projectDetails->getCurrentSkillNames() ?> ],
            CapitalizeFirstLetter: true,
            preventSubmitOnEnter: true,
            typeahead: true,
            //typeaheadSource: ["Pisa", "Rome", "Milan", "Florence", "New York", "Paris", "Berlin", "London", "Madrid"],
            typeaheadSource: [ <?php echo all_skill_names($this) ?> ],
            hiddenTagListName: 'hidden-skill-list',
            tagClass: 'label pull-left'
        });
        
       
    });

</script>

<?php $this->load->view("subviews/create_project_btn_alert") ?>
<?php $this->load->view("template_footer"); ?>