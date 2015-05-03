<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        
        <style>
            td{
                border: 1px solid black;
                vertical-align: top;
            }
            #on-hover{
                display: none;
            }
            #overflow:hover #on-hover{
                display: block;
                transition-delay: 1s;
            }
            table{
                table-layout: fixed;
            }
            div.studentData{
                display: none;
            }
            
            
            
        </style>
        <script>
                        
                function regionalStudFunction(obj){
                    
                    if($("div[class*=studentData][id="+ $(obj).attr("id") +"]").css("display") != "none"){
                        $("div[class*=studentData][id="+ $(obj).attr("id") +"]").css("display","none");
                    }
                    else{
                        $("div[class*=studentData][id="+ $(obj).attr("id") +"]").css("display","block");
                    }
                };
                
                function globalStudFunction(){
                    
                    if($("div[class*=studentData]").css("display") != "none"){
                        $("div[class*=studentData]").css("display","none");
                    }
                    else{
                        $("div[class*=studentData]").css("display","block");
                    }
                };
           
        </script>
        
        <?php $this->load->view("matchmaking_header");
        $MDc = $_SESSION['globalMC']['VIPsMD'];
        $MDf = $_SESSION['globalMC']['VIPfMD'];?>
     <!--Note: "warning" to make red; "success" for green-->
     <h1>Match Phase 1 (Auto): Very Important Projects(VIP)</h1>
     <h6>Choose one of two versions of the heuristic VIP matchmaking to proceed for match finalization.</h6>
     Note: When applicable green means the skill is fulfilled. Orange unfulfilled. Gray unnecessary (hover to reveal).
                  <?php
            echo form_open('match/matchPhase1HelperAuto', array(
            'name' => 'VIPchoice',
            ));?>
     <button type="button" id="s" class="globalStud" onclick="globalStudFunction()">Show/Hide All Students</button><br>
     <table style="width: 1000px">
    <tr>
        <td> <h2>Friendly Heuristic VIP Matching</h2>
            <b>Overall Match Data</b><br>
        Student Average Interest: <?php echo $MDf->avgInterest;?><br>
        Average Total Skill Fulfillment: <?php echo $MDf->avgTotalSkill;?>%<br>
        Student Average Fulfillment <?php echo $MDf->avgAvgFulfillment;?>%<br>
        Total Overflow Skills: <?php echo $MDf->totalOverflow;?></td>
        <td> <h2>Scientific Heuristic VIP Matching</h2>
            <b>Overall Match Data</b><br>
        Student Average Interest: <?php echo $MDc->avgInterest;?><br>
        Average Total Skill Fulfillment: <?php echo $MDc->avgTotalSkill;?>%<br>
        Student Average Fulfillment <?php echo $MDc->avgAvgFulfillment;?>%<br>
        Total Overflow Skills: <?php echo $MDc->totalOverflow;?></td>
    </tr>
        <?php
        $PLc = array_values($_SESSION['globalMC']['VIPs']);
        $PLf = array_values($_SESSION['globalMC']['VIPf']);
        
        for($i = 0; $i<count($PLf); $i++){
            echo '<tr>';
            echo '<td>';
            echo '<h3>';
            echo $PLf[$i]->name;
            echo '</h3>';
            echo '';
            echo "<b>Head Professor's Rating: </b>".$PLf[$i]->score."<br>";
            echo '<b>Student Interest Average: </b>';
            echo $PLf[$i]->calculateAvgInterest();
            echo '<br>';
            echo '<b>Skill Total Fulfillment: </b>';
            echo $PLf[$i]->calculateTotalFulfillment();
            echo '%<br>';
            echo '<b>Student Average Fulfillment: </b>';
            echo $PLf[$i]->calculateAvgFulfillment();
            echo '%<br>';
            echo '<b>Student Total Overflow Skills:</b>';
            echo $PLf[$i]->calculateTotalOverflow();
            echo '<br>';
            echo '<b>Skill Fulfillment Data:</b><br>';
            foreach ($PLf[$i]->fulfilledSkills as $s) {
                echo '<li class="label label-success skill">';
                echo $s;
                echo '</li>';
                echo ' ';
            }    
            foreach ($PLf[$i]->missingSkills as $s) {
                echo '<li class="label label-warning skill">';
                echo $s;
                echo '</li>';
                echo ' ';
            }

            echo '<br><h5>Students Added:(';
            echo count($PLf[$i]->desiredStudents);
            echo ' out of ';
            echo $PLf[$i]->max;
            echo ')   <button type="button" id="s'.$i .'" class="regionalStud" onclick="regionalStudFunction(this)">Show/Hide Students</button></h5>';
            echo '<div id="s'.$i.'" class="studentData">';
            foreach($PLf[$i]->desiredStudents as $s){
                echo '<h6>';
                echo $s->name;
                echo '</h6>';
                echo 'Interest: ';
                echo $s->scoreList[$PLf[$i]->id];
                echo '<br>';
                echo '% of Project Skills Acheived: ';
                echo $PLf[$i]->figureSkillContribution($s);
                echo '%<br>';
                echo 'Amount of overflow skills: ';
                echo count($s->overflowSkills);
                echo '<br>';
                echo 'Skill contribution:';
                echo '<br>';

                if(count($s->fufilledSkills)>0){
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
               }
               else{
                   echo '<i>No Contribution</i><br>';
               }
               if(count($s->overflowSkills)>0){
                echo '<div id="overflow">';
                echo '<li class="label skill">Hover to reveal overflow skill</li>';
                echo '<div id="on-hover">';  
               }

               foreach ($s->overflowSkills as $skill) {
                   echo '<li class="label skill">';
                   echo $skill;
                   echo '</li>';
                   echo ' ';
               } 
               if(count($s->overflowSkills)>0){
               echo '</div>';
               echo '</div>';
               }

               echo '<br>';
            }
            echo '</div>';
            echo '</td>';
            
            echo '<td>';
                        echo '<h3>';
            echo $PLc[$i]->name;
            echo '</h3>';
            echo '';
            echo "<b>Head Professor's Rating: </b>".$PLc[$i]->score."<br>";
            echo '<b>Student Interest Average: </b>';
            echo $PLc[$i]->calculateAvgInterest();
            echo '<br>';
            echo '<b>Skill Total Fulfillment: </b>';
            echo $PLc[$i]->calculateTotalFulfillment();
            echo '%<br>';
            echo '<b>Student Average Fulfillment: </b>';
            echo $PLc[$i]->calculateAvgFulfillment();
            echo '%<br>';
            echo '<b>Student Total Overflow Skills:</b> ';
            echo $PLc[$i]->calculateTotalOverflow();
            echo '<br>';
            echo '<b>Skill Fulfillment Data:</b><br>';
            foreach ($PLc[$i]->fulfilledSkills as $s) {
                echo '<li class="label label-success skill">';
                echo $s;
                echo '</li>';
                echo ' ';
            }    
            foreach ($PLc[$i]->missingSkills as $s) {
                echo '<li class="label label-warning skill">';
                echo $s;
                echo '</li>';
                echo ' ';
            }

            echo '<br><h5>Students Added:(';
            echo count($PLc[$i]->desiredStudents);
            echo ' out of ';
            echo $PLc[$i]->max;
            echo ')   <button type="button" id="s'.$i.'" class="regionalStud" onclick="regionalStudFunction(this)">Show/Hide Students</button></h5>';
            echo '<div id="s'.$i.'" class="studentData">';
            foreach($PLc[$i]->desiredStudents as $s){
                echo '<h6>';
                echo $s->name;
                echo '</h6>';
                echo 'Interest: ';
                echo $s->scoreList[$PLc[$i]->id];
                echo '<br>';
                echo '% of Project Skills Acheived:';
                echo $PLc[$i]->figureSkillContribution($s);
                echo '%<br>';
                echo 'Amount of overflow skills: ';
                echo count($s->overflowSkills);
                echo '<br>';
                echo 'Skill contribution:';
                echo '<br>';

                if(count($s->fufilledSkills)>0){
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
               }
               else{
                   echo '<i>No Contribution</i><br>';
               }
               if(count($s->overflowSkills)>0){
                echo '<div id="overflow">';
                echo '<li class="label skill">Hover to reveal overflow skill</li>';
                echo '<div id="on-hover">';  
               }
               foreach ($s->overflowSkills as $skill) {
                   echo '<li class="label skill">';
                   echo $skill;
                   echo '</li>';
                   echo ' ';
               }
               if(count($s->overflowSkills)>0){
               echo '</div>';
               echo '</div>';
               }

               echo '<br>';
            }
            echo '</div>';
            echo '</td>';
            echo '</tr>';
        }
        ?>
     
     </table>
     
     <br><b>Choose one of the two match results and proceed. Friendly is default.</b>
     <?php
     
     $dataF = array(
       'name' => 'VIPchoice',
       'id' => 'VIPchoice',
       'value' => 'friendly',
       'checked' => true,
     );
     $dataS = array(
       'name' => 'VIPchoice',
       'id' => 'VIPchoice',
       'value' => 'scientific',
     );
     
        echo form_radio($dataF);
        echo "Friendly  ";
        echo form_radio($dataS);
        echo "Scientific";
     ?>
     <?php                               
                echo form_submit(array(
                    'id' => 'match phase 1 helper auto',
                    'name' => 'match phase 1 helper auto',
                    'type' => 'Submit',
                    'class' => 'btn btn-primary btn-small pull-left',
                    'value' => 'Goto Match Phase 1 Finalization',
                ));
                ?><br>
     <?php echo form_close();// $_SESSION['otherProjectState']= $_POST["OtherProject"];?>
    <?php $this->load->view("template_footer"); ?>
    </body>
</html>
