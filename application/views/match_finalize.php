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
                text-align: center;
            }
            #on-hover{
                display: none;
            }
            #overflow:hover #on-hover{
                display: block;
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
       ?>
     <!--Note: "warning" to make red; "success" for green-->
     <h1>Match Results</h1>
     <h6>Below is the final match configuration for all projects please confirm to send to database.</h6>
     Note: When applicable green means the skill is fulfilled. Orange unfulfilled. Gray unnecessary (hover to reveal).
                  <?php
            echo form_open('match/saveMatchings');?>
     <button type="button" id="s" class="globalStud" onclick="globalStudFunction()">Show/Hide All Students</button><br>
     <table style="width: 1000px">
    <tr>
        <td> <h2>VIP Matching Final Details</h2>
            <b>Overall Match Data</b><br>
        Student Average Interest: <?php echo $VIPfinalMD->avgInterest;?><br>
        Average Total Skill Fulfillment: <?php echo $VIPfinalMD->avgTotalSkill;?>%<br>
        Student Average Fulfillment <?php echo $VIPfinalMD->avgAvgFulfillment;?>%<br>
        Total Overflow Skills: <?php echo $VIPfinalMD->totalOverflow;?></td>
    </tr>
        <?php
        //$PLc = array_values($_SESSION['VIPs']);
        
        for($i = 0; $i<count($VIPfinal); $i++){
            echo '<tr>';
            echo '<td>';
            echo '<h3>';
            echo $VIPfinal[$i]->name;
            echo '</h3>';
            echo '';
            echo "<b>Head Professor's Rating: </b>".$VIPfinal[$i]->score."<br>";
            echo '<b>Student Interest Average: </b>';
            echo $VIPfinal[$i]->calculateAvgInterest();
            echo '<br>';
            echo '<b>Skill Total Fulfillment: </b>';
            echo $VIPfinal[$i]->calculateTotalFulfillment();
            echo '%<br>';
            echo '<b>Student Average Fulfillment: </b>';
            echo $VIPfinal[$i]->calculateAvgFulfillment();
            echo '%<br>';
            echo '<b>Student Total Overflow Skills:</b>';
            echo $VIPfinal[$i]->calculateTotalOverflow();
            echo '<br>';
            echo '<b>Skill Fulfillment Data:</b><br>';
            foreach ($VIPfinal[$i]->fulfilledSkills as $s) {
                echo '<li class="label label-success skill">';
                echo $s;
                echo '</li>';
                echo ' ';
            }    
            foreach ($VIPfinal[$i]->missingSkills as $s) {
                echo '<li class="label label-warning skill">';
                echo $s;
                echo '</li>';
                echo ' ';
            }

            echo '<br><h5>Students Added:(';
            echo count($VIPfinal[$i]->desiredStudents);
            echo ' out of ';
            echo $VIPfinal[$i]->max;
            echo ')   <button type="button" id="s'.$i .'" class="regionalStud" onclick="regionalStudFunction(this)">Show/Hide Students</button></h5>';
            echo '<div id="s'.$i.'" class="studentData">';
            foreach($VIPfinal[$i]->desiredStudents as $s){
                echo '<h6>';
                echo $s->name;
                echo '</h6>';
                echo 'Interest: ';
                echo $s->scoreList[$VIPfinal[$i]->id];
                echo '<br>';
                echo '% of Project Skills Acheived: ';
                echo $VIPfinal[$i]->figureSkillContribution($s);
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
     
     </table><br><br><br>
     
<table style="width: 1000px">
    <tr>
        <td> <h2>Other Projects Final Details</h2>
            <b>Overall Match Data</b><br>
        Student Average Interest: <?php echo $OtherMD->avgInterest;?><br>
        Average Total Skill Fulfillment: <?php echo $OtherMD->avgTotalSkill;?>%<br>
        Student Average Fulfillment <?php echo $OtherMD->avgAvgFulfillment;?>%<br>
        Total Overflow Skills: <?php echo $OtherMD->totalOverflow;?></td>
    </tr>
        <?php
        //$PLc = array_values($_SESSION['VIPs']);
        $OtherP = array_values($OtherP);
        for($i = 0; $i<count($OtherP); $i++){
            echo '<tr>';
            echo '<td>';
            echo '<h3>';
            echo $OtherP[$i]->name;
            echo '</h3>';
            echo '';
            echo '<b>Student Interest Average: </b>';
            echo $OtherP[$i]->calculateAvgInterest();
            echo '<br>';
            echo '<b>Skill Total Fulfillment: </b>';
            echo $OtherP[$i]->calculateTotalFulfillment();
            echo '%<br>';
            echo '<b>Student Average Fulfillment: </b>';
            echo $OtherP[$i]->calculateAvgFulfillment();
            echo '%<br>';
            echo '<b>Student Total Overflow Skills:</b>';
            echo $OtherP[$i]->calculateTotalOverflow();
            echo '<br>';
            echo '<b>Skill Fulfillment Data:</b><br>';
            foreach ($OtherP[$i]->fulfilledSkills as $s) {
                echo '<li class="label label-success skill">';
                echo $s;
                echo '</li>';
                echo ' ';
            }    
            foreach ($OtherP[$i]->missingSkills as $s) {
                echo '<li class="label label-warning skill">';
                echo $s;
                echo '</li>';
                echo ' ';
            }

            echo '<br><h5>Students Added:(';
            echo count($OtherP[$i]->desiredStudents);
            echo ' out of ';
            echo $OtherP[$i]->max;
            echo ')   <button type="button" id="s'.$i .'" class="regionalStud" onclick="regionalStudFunction(this)">Show/Hide Students</button></h5>';
            echo '<div id="s'.$i.'" class="studentData">';
            foreach($OtherP[$i]->desiredStudents as $s){
                echo '<h6>';
                echo $s->name;
                echo '</h6>';
                echo 'Interest: ';
                echo $s->scoreList[$OtherP[$i]->id];
                echo '<br>';
                echo '% of Project Skills Acheived: ';
                echo $OtherP[$i]->figureSkillContribution($s);
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
     
</table><br>   
<table style="width: 1000px">
         <tr>
             <td>            
                 <h3>Unmatched Students </h3>
                <?php
                if(count($unmatched) == 0){
                    echo 'All students matched!';
                }
                else{
                    foreach($unmatched as $s){
                        echo $s->name;
                        echo "<br>";
                    }
                }
            ?></td>
         </tr>
     </table>
<div id="alignForm">
        <?php
                echo form_submit(array(
                    'id' => 'save matchings',
                    'name' => 'save matchings',
                    'type' => 'Submit',
                    'class' => 'btn btn-primary btn-small pull-left',
                    'value' => 'Save Match Configuration',
                ));
                ?></div>
            <?php echo form_close() ?>

     <?php// $_SESSION['otherProjectState']= $_POST["OtherProject"];?>
    <?php $this->load->view("template_footer"); ?>
    </body>
</html>