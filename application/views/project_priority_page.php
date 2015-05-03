<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<div id='area'>
<?php $this->load->view("template_header"); ?>
    <h3>Welcome to the Senior Project Website Project Priority Page.</h3>
    <p>Here you set the priorities of current projects.</p>
    <p>As a reminder, projects with the rank "0" or less will not be considered in the matching algorithm.</p>
    <p>Projects ranked "1" will be considered as not important to you.</p>
    <p>Projects between "2-100" will be going intensive matching for optimality and are considered VIP (very important projects). Higher means more priority.</p>
    <hr>
    <?php
    echo form_open('match/saveRank', array(
        'class' => 'form-inline',
    ));
    ?>

    <?php if ($no_results) { ?>

        <p>There are no Current Projects to rank</p>

    <?php } else { ?>
        <?php if (isset($lRegularProjects) && count($lRegularProjects) > 0) { ?>
            <?php
            $this->load->view('subviews/project_match_list', array(
                'lProjects' => $lRegularProjects,
            ))
            ?>
        <?php } ?>
    <?php } ?>

      
            <?php $this->load->view("template_footer"); ?>
</div>

