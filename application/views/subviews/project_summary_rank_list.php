<ul class="project_list unstyled <?php echo isset($list_class) && strlen($list_class) > 0 ? $list_class : '' ?>  ">
    <?php if (isset($list_title) && strlen($list_title) > 0) { ?>
        <lh><h2><?php echo $list_title ?></h2></lh>
    <?php } ?>
        <?php
        echo form_open('match/saveRank', array(
            'class' => 'form-inline',
        ));
        ?>
        <?php foreach ($lProjects as $iProject) { ?>
            <li class="well project_list">


                <div class="pull-right right-text">
                    <p>
                        <!-- old way of displaying the skills -->
                        <!-- <?php echo $iProject->getlSkillNames() ?> -->
                        <?php $this->load->view('subviews/skills_list', array('lSkills' => $iProject->lSkills)) ?>
                    </p>

                    <p>
                        <?php $this->load->view('subviews/join_leave_buttons', array('projectDetails' => $iProject)) ?>
                    </p>
                </div>

                <h4>
                    <?php echo anchor('project/' . $iProject->project->id, $iProject->project->title) ?>
                </h4>

                <p>
                    <?php echo $iProject->getShortDescription() ?>
                    <?php echo anchor('project/' . $iProject->project->id, 'More Info...') ?>
                </p>

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
                    <lh class="muted">Team Members:</lh>

                    <?php if (isset($iProject->lTeamMemberSummaries) && count($iProject->lTeamMemberSummaries) > 0) { ?>

                        <?php foreach ($iProject->lTeamMemberSummaries as $iMemberSumm) { ?>
                            <li>
                                <?php $this->load->view('subviews/user_summary_full_name', array('user_summary' => $iMemberSumm)) ?>
                            </li>
                        <?php } ?>

                    <?php } else { ?>

                        <li>This project needs members</li>

                    <?php } ?>
                </ul>

                <ul class="unstyled inline">
                    <lh class="muted ">Status:</lh>
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
                    </li> 
                    <div class="muted pull-right">
                        <li>Rank:  <?php
                            $id = htmlspecialchars($iProject->project->id);

                            if (!isset($ranks[$id])) {
                                echo form_input(array(
                                    'id' => $id,
                                    'name' => $id,
                                    'class' => 'input-small',
                                    'value' => '-1',
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
        <?php } ?>
</ul>