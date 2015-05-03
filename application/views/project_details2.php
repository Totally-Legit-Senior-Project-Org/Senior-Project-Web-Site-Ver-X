<?php $this->load->view("template_header"); ?>

<?php if ($no_results) { ?>

    <p>No data for the specified project</p>

<?php } else { ?>

     <?php if($projectDetails->statusName ==  "REJECTED"){ ?>        
            <p>Your project has been rejected</p>
        
     <?php    }?>
             <div class="pull-right right-text">
<!--        <div class="pull-right hor-margin">-->
            <?php 
            if (isset($displayJoin) && $displayJoin == TRUE) 
            { 
                echo form_open('projectcontroller/join', array(
                    'id' => 'form-join-project-'.$projectDetails->project->id
                ));

                echo form_hidden(array(
                    'pid' => $projectDetails->project->id,
                    'pbUrl' => current_url()
                ));

                echo form_submit(array(
                    'id' => 'btn-join-'.$projectDetails->project->id,
                    'name' => 'btn-submit',
                    'type' => 'Submit',
                    'class' => 'btn btn-primary',
                    'value' => 'Join'
                ));

                echo form_close();
            } 
           else if(isset($displayLeave) && $displayLeave == TRUE) 
            { 
                //<button class="btn btn-warning" type="button">Leave</button>
                echo form_open('projectcontroller/leave', array(
                    'id' => 'form-leave-project'
                ));

                echo form_hidden(array(
                        'pid' => $projectDetails->project->id,
                        'pbUrl' => current_url()
                    ));

                //<button id='btn-leave' type="button" class="btn btn-warning btn-large pull-right">Leave Project</button>
                echo form_submit(array(
                        'id' => 'btn-leave',
                        'name' => 'btn-submit',
                        'type' => 'Submit',
                        'class' => 'btn btn-warning',
                        'value' => 'Leave'
                    ));

                echo form_close();
            } 
        ?>
                
<!--        </div>
            
        <div class="pull-right">-->
            <?php $this->load->view('subviews/skills_list', array('lSkills' => $projectDetails->lSkills) )?>
        </div>

        <h2><?php echo $projectDetails->project->title ?></h2>

        <p>
            <?php echo $projectDetails->project->description ?>
        </p>

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
<!--            <h4 class="muted inline">Status:</h4> <?php echo ucfirst($projectDetails->statusName) ?>                 -->
        </p>

        <p>
            <h4 class="muted inline">Maximum project capacity:</h4> <?php echo $projectDetails->project->max_students.' students' ?>
        </p>
        
        <p>
            <h4 class="muted inline">Mentor:</h4> 
             <?php if($projectDetails->mentor != ''){ ?>
                       
                         <?php echo anchor('user/'.$projectDetails->mentor , $this->spw_user_model->get_fullname($projectDetails->mentor));?>
                       
                    <?php }else{?>  
                          <?php  echo "This project does not have a mentor"?>  
                    <?php } ?> 
        </p>     

        <?php $this->load->view('subviews/user_summaries_full_list', array(
            'listTitle' => 'Proposed By:',
            'lUserSummaries' => array($projectDetails->proposedBySummary)
        )) ?>

        
        <!--added on SPW v5-->
        <?php
            if(isset($request_machine) && $request_machine){
                echo '<a id="submitRequests" 
                type="button" 
                class="btn btn-default pull-right"
                href="'.base_url('./vm-request').'">
                    Create VM-Request
                </a>';
            }
        ?>
        

        <?php $this->load->view('subviews/user_summaries_full_list', array(
            'listTitle' => 'Team Members:',
            'lUserSummaries' => $projectDetails->lTeamMemberSummaries,
            'errorMessage' => 'This project needs members'
        )) ?>

<?php }?>
<?php $this->load->view("template_footer"); ?>