<?php $this->load->view("template_header"); ?>

<?php 
    if ((!isset($hideCreateProject) || !$hideCreateProject))
    {
        if(isset($isUserHeadProfessor)){
        echo anchor('project/create', 'Create a Project', array(
            'id' => 'btn-create-new-project',
            'class' => 'btn btn-large pull-right'));
        }else if ($isUnderDeadline){
            echo anchor('project/create', 'Propose a Project', array(
            'id' => 'btn-create-new-project',
            'class' => 'btn btn-large pull-right'));
        }
    }
?>

<?php if ($no_results) { ?>
    <p>
          <?php  if(isset($isUserHeadProfessor)){?> 
           You have not created any projects
           <?php }else if($isUnderDeadline){ 
            ?>
                 You do not have any projects
        <?php }else{?>
                The deadline for proposing and joining Projects has passed. 
           <?php  } ?>
    </p>
<?php } else if(isset($isUserHeadProfessor) && $isUserHeadProfessor){?>
        <?php if (isset($lProjects) && count($lProjects) > 0) { ?>
        <?php $this->load->view('subviews/project_summary_list', array(
            'lProjects' => $lProjects, 
            'list_title' => 'Created Projects',
            'list_class' => 'Created Projects'
        ) )?>
    
    <?php } ?>
    <?php } else { ?>
     <?php if (isset($lProjects) && count($lProjects) > 0) { ?>
   <?php $this->load->view('subviews/project_summary_list', array(
            'lProjects' => $lProjects, 
            'list_title' => 'Proposed Projects',
            'list_class' => 'Proposed Projects'
        ) )?>
    <?php } ?>
     <?php } ?>
    
<!--    <ul class="project_list unstyled">
        <?php if (isset($list_title) && strlen($list_title) > 0) { ?>
            <lh><h2><?php echo $list_title ?></h2></lh>
        <?php } ?>

        <?php foreach ($lProjects as $iProject) { ?>
            <li class="well">
                <h4>
                    <?php echo anchor('project/'.$iProject->project->id, $iProject->project->title) ?>
                </h4>

                <p>
                    <?php echo $iProject->getShortDescription() ?>
                    <?php echo anchor('project/'.$iProject->project->id, 'See more...') ?>
                    <?php echo '<br>Status: '.ucfirst($iProject->statusName) ?>
                </p>
            </li>
        <?php } ?>
    </ul>-->



<?php $this->load->view("subviews/create_project_btn_alert") ?>

<?php $this->load->view("template_footer"); ?>