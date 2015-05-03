<?php $this->load->view("template_header");?>
<?php if (isset($leftOverCheck) && isset($neededCheck)) { ?>
    <ul>
        <h2>Match Results</h2>
        <h6>Woah! Something went wrong in the second stage of preprocessing.</h6>
        <hr><?php if ($leftOverCheck == false) { ?>
            <div class ='well'>
                <ul class="inline">
                    <h4>Students do not have these skills:</h4>
                    <hr>

                    <p> Please check if any students can add the skills below or remove any projects with the skills below: <br /></p>


                    <ul class="inline">
                        <?php
                        foreach ($leftOverSkills as $skills) {
                            ?>
                            <li class="label skill"><?php echo $skills ?></li>
                            <?php
                        }
                        ?>
                    </ul>

                </ul>
            </div>  
        <?php } ?>
        <?php if ($neededCheck == false) { ?>
            <div class ='well'>
                <h4>Some students have these skills, but some projects may not have these skills satisfied:</h4>
                <hr>

                <p> Please check if any students can add the skills below or remove any projects with the skills below: <br /></p>


                <ul class="inline">
                    <?php
                    foreach ($neededSkills as $skills) {
                        ?>
                        <li class="label skill"><?php echo $skills ?></li>
                        <?php
                    }
                    ?>
                </ul>
            </div>
        <?php } ?>
        <div style='padding-bottom: 1px'>
            <ul style='display:block; margin: 0;'>
                <?php
                echo form_open('match/#run match', array(
                ));
                ?>
                <li style = 'float: left; overflow: hidden;'>
                    <?php
                    echo form_submit(array(
                        'id' => 'back',
                        'name' => 'back',
                        'type' => 'Submit',
                        'class' => 'btn btn-primary',
                        'value' => 'Back'
                    ));
                    ?>
                </li> 
                <?php echo form_close() ?>
                <?php
//                echo form_open('match/doMatch', array(
//                ));
//                ?>
                <li style = 'float: right; overflow: hidden;'>
                    <?php
//                    echo form_submit(array(
//                        'id' => 'continue',
//                        'name' => 'continue',
//                        'type' => 'Submit',
//                        'class' => 'btn btn-primary',
//                        'value' => 'Continue'
//                    ));
//                    ?>
                </li>
                <?php //echo form_close() ?>
            </ul> 
        </div>
    <?php
    } else if (isset($doMatch)) {
        $x = 1;
        ?>
        <ul class="project_list unstyled ">
            <?php
            foreach ($comb as $c) {
                echo '<div class="well project_list">';
                echo "<center><h3>Tentative Match " . $x . ':</h3></center><hr>'
                        . '<h4 style="float: left"> Professor Score = ' . $c->profScore . '</h4> <h4 style="float: right">   Student Score = ' . $c->studentScore . '</h4><br><br/>';

                foreach ($c->projects as $p) {
                    echo '<li class="well project_list"><h5>';
                    echo $p->projName;
                    echo "</h5><ul class='unstyled inline'><lh class='muted'>Team Members:</lh>";
                    foreach ($p->members as $m) {
                        echo '<li>';
                        echo $m->name;
                        echo '</li>';
                    }
                    echo '</ul></li>';
                }
                $x++;
                echo '</div>';
                echo '<hr />';
            }
            ?>
            <?php
        }
        ?>
    </ul>
     <!--Note: "warning" to make red; "success" for green-->
     <h1>Other Projects</h1>
     <h6>Choose one of two versions to proceed for match finalization.</h6>
     Note: When applicable green means the skill is fulfilled. Red unfulfilled. Gray unnecessary.
     <h2>Friendly Matching (Student's get their desires):</h2>
        <?php
     foreach ($PLf as $p)  {
         echo '<h3>';
         echo $p->name;
         echo '</h3>';
         echo '';
         echo '<b>Student Interest Average: ';
         echo $p->calculateAvgInterest();
         echo '</b><br>';
         echo '<b>Skill Total Fulfillment: ';
         echo $p->calculateTotalFulfillment();
         echo '%</b><br>';
         echo '<b>Student Average Fulfillment: ';
         echo $p->calculateAvgFulfillment();
         echo '%</b><br>';
         echo '<b>Skill Fulfillment Data:</b><br>';
         foreach ($p->fulfilledSkills as $s) {
             echo '<li class="label label-success skill">';
             echo $s;
             echo '</li>';
             echo ' ';
         }    
         foreach ($p->missingSkills as $s) {
             echo '<li class="label label-warning skill">';
             echo $s;
             echo '</li>';
             echo ' ';
         }
         
         echo '<br><h5>Students Added:(';
         echo count($p->desiredStudents);
         echo ' out of ';
         echo $p->max;
         echo ')</h5>';
         foreach($p->desiredStudents as $s){
             echo '<h6>';
             echo $s->name;
             echo '</h6>';
             echo 'Interest: ';
             echo $s->scoreList[$p->id];
             echo '<br>';
             echo '% of Project Skills Acheived:';
             echo $p->figureSkillContribution($s);
             echo '%<br>';
             echo 'Skill contribution:';
             echo '<br>';
             
             foreach ($s->fufilledSkills as $skill) {
                echo '<li class="label label-success skill">';
                echo $skill;
                echo '</li>';
                echo ' ';
            }
            foreach ($s->missingSkills as $skill) {
                echo '<li class="label label-warning skill">';
                echo $skill;
                echo '</li>';
                echo ' ';
            }
            foreach ($s->overflowSkills as $skill) {
                echo '<li class="label skill">';
                echo $skill;
                echo '</li>';
                echo ' ';
            }
            echo '<br>';
         }
         echo '<hr>';
     }
        ?>
     <h2>Compromise Matching (Student's compromise with projects):</h2>
        <?php
     foreach ($PLc as $p) {
         echo '<h3>';
         echo $p->name;
         echo '</h3>';
         echo '';
         echo '<b>Student Interest Average: ';
         echo $p->calculateAvgInterest();
         echo '</b><br>';
         echo '<b>Skill Total Fulfillment: ';
         echo $p->calculateTotalFulfillment();
         echo '%</b><br>';
         echo '<b>Student Average Fulfillment: ';
         echo $p->calculateAvgFulfillment();
         echo '%</b><br>';
         echo '<b>Skill Fulfillment Data:</b><br>';
         foreach ($p->fulfilledSkills as $s) {
             echo '<li class="label label-success skill">';
             echo $s;
             echo '</li>';
             echo ' ';
         }    
         foreach ($p->missingSkills as $s) {
             echo '<li class="label label-warning skill">';
             echo $s;
             echo '</li>';
             echo ' ';
         }
         
         echo '<br><h5>Students Added:(';
         echo count($p->desiredStudents);
         echo ' out of ';
         echo $p->max;
         echo ')</h5>';
         foreach($p->desiredStudents as $s){
             echo '<h6>';
             echo $s->name;
             echo '</h6>';
             echo 'Interest: ';
             echo $s->scoreList[$p->id];
             echo '<br>';
             echo '% of Project Skills Acheived:';
             echo $p->figureSkillContribution($s);
             echo '%<br>Skill contribution:';
             echo '<br>';
             
             foreach ($s->fufilledSkills as $skill) {
                echo '<li class="label label-success skill">';
                echo $skill;
                echo '</li>';
                echo ' ';
            }
            foreach ($s->missingSkills as $skill) {
                echo '<li class="label label-warning skill">';
                echo $skill;
                echo '</li>';
                echo ' ';
            }
            foreach ($s->overflowSkills as $skill) {
                echo '<li class="label skill">';
                echo $skill;
                echo '</li>';
                echo ' ';
            }
            echo '<br>';
         }
         echo '<hr>';
     }
        ?>
    <?php $this->load->view("template_footer"); ?>


