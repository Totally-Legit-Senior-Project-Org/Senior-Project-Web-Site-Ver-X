<?php $this->load->view("template_header"); ?>

<?php if ($no_results) { ?>

    <p>There are no Current Projects</p>

<?php } else { ?>

    <?php if ($isUserAStudent) { ?>

	<strong><p>As a reminder, the initial rank values of -1 mean that the projects are unranked. Score the projects 1-100 (100 being of more interest to you). Once the professor informs you in class to rank the projects, you need to rank a minimum of <?php echo $minimum ?> project(s).
        </p></strong>

        <?php if (isset($lSuggestedProjects) && count($lSuggestedProjects) > 0) { ?>
            <?php
            $this->load->view('subviews/project_summary_rank_list', array(
                'lProjects' => $lSuggestedProjects,
                'list_title' => 'Suggested Projects',
                'list_class' => 'suggested-projects'
            ))
            ?>
            <hr>
        <?php } ?>

        <?php if (isset($lRegularProjects) && count($lRegularProjects) > 0) { ?>
            <?php
            $this->load->view('subviews/project_summary_rank_list', array(
                'lProjects' => $lRegularProjects,
                'list_title' => '',
                'list_class' => ''
            ))
            ?>
        <?php } ?>


        <ul class="unstyled">
            <li id="Save-Priority-Ranking-Scheme">
              
                <?php  
                echo form_submit(array(
                    'id' => 'save rank',
                    'name' => 'save rank',
                    'type' => 'Submit',
                    'class' => 'pull-right btn btn-primary',
                    'value' => 'Save Interest Ranking Scheme'
                ));
                ?>
            </li>
        </ul> 

        <?php echo form_close(); ?>

    <?php } else { ?>

        <?php if (isset($lSuggestedProjects) && count($lSuggestedProjects) > 0) { ?>
            <?php
            $this->load->view('subviews/project_summary_list', array(
                'lProjects' => $lSuggestedProjects,
                'list_title' => 'Suggested Projects',
                'list_class' => 'suggested-projects'
            ))
            ?>
            <hr>
        <?php } ?>

        <?php if (isset($lRegularProjects) && count($lRegularProjects) > 0) { ?>
            <?php
            $this->load->view('subviews/project_summary_list', array(
                'lProjects' => $lRegularProjects,
                'list_title' => '',
                'list_class' => ''
            ))
            ?> 

        <?php } ?> 
    <?php } ?>

<?php } ?>

<?php $this->load->view("template_footer"); ?>
