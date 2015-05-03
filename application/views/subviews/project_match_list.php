<ul class="project_list unstyled <?php echo isset($list_class) && strlen($list_class) > 0 ? $list_class : '' ?>  ">
    <?php if (isset($list_title) && strlen($list_title) > 0) { ?>
        <lh><h2><?php echo $list_title ?></h2></lh>
    <?php } ?>



    <?php foreach ($lProjects as $iProject) { ?>
        <li class="well project_list">
            <div class="pull-right right-text">
                <p>
                    <!-- old way of displaying the skills -->
                    <!-- <?php echo $iProject->getlSkillNames() ?> -->
                    <?php $this->load->view('subviews/skills_list', array('lSkills' => $iProject->lSkills)) ?>
                </p>

            </div>

            <h4>
                <?php echo anchor('project/' . $iProject->project->id, $iProject->project->title) ?>
            </h4>

            <ul class="unstyled inline">
                <lh class="muted">Proposed By:</lh>
                <li>
                    <?php $this->load->view('subviews/user_summary_full_name', array('user_summary' => $iProject->proposedBySummary)) ?>
                </li>
            </ul>

            <ul class="unstyled inline">
                <lh class="muted">Mentor:</lh>

                <?php if ($iProject->project->mentor != '') { ?>
                    <li> 
                        <?php echo anchor('user/' . $iProject->project->mentor, $this->spw_user_model->get_fullname($iProject->project->mentor)); ?>
                    </li>
                <?php } else { ?>  
                    <li>This project does not have a mentor</li>
                <?php } ?> 
            </ul>

            <ul class="unstyled inline">
                 <li class="label label-success skill">Max Students: <?php echo $maxNums[$iProject->project->id] ?></li>
                <!--<lh class="muted ">Status:</lh>
                <?php if ($iProject->statusName == "APPROVED") { ?>
                    <li class="label label-success"></span>          
                    <?php } else if ($iProject->statusName == "PENDING APPROVAL") { ?>
                    <li class="label label-warning"></span>    
                    <?php } else if ($iProject->statusName == "REJECTED") { ?>
                    <li class="label label-important"></span>   
                    <?php } else if ($iProject->statusName == "CLOSED") { ?>
                    <li class="label" ></span>   
                    <?php } ?>
                    <?php echo ucfirst($iProject->statusName) ?>
                </li>  --!>
                <!-- <div class="muted pull-right"><li>Rank: <input class="input-small" name="<?php echo htmlspecialchars($iProject->project->id); ?>" type="text"></span></li></div> -->
                <div class="muted pull-right">
                    <li>Rank:  <?php
                        $id = htmlspecialchars($iProject->project->id);

                        if ($no_ranks == true || !isset($ranks[$id])) {
                            echo form_input(array(
                                'id' => $id,
                                'name' => $id,
                                'class' => 'input-small',
                                'value' => '0',
                                'required' => ''
                            ));
                        } else {
                            echo form_input(array(
                                'id' => $id,
                                'name' => $id,
                                'class' => 'input-small',
                                'value' => $ranks[$id],
                                'required' => ''
                            ));
                        }
                        ?></li>
                </div>
            </ul>
        </li>
    <?php }
    ?>
              
                <?php
                echo form_submit(array(
                    'id' => 'save rank',
                    'name' => 'save rank',
                    'type' => 'Submit',
                    'class' => 'pull-right btn btn-primary',
                    'value' => 'Save Priority Ranking Scheme'
                ));
                ?>

            <?php echo form_close() ?>
        <br>
</ul>

