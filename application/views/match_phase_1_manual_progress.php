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
            }
            table{
                table-layout: fixed;
            }
            
            input[type="checkbox"]{
                transform: scale(1.2);         
                -webkit-transform: scale(1.2); 

                -ms-transform: scale(1.2);    
                -moz-transform: scale(1.2);    
                -o-transform: scale(1.2);  
            }
        </style>
        
        <?php $this->load->view("matchmaking_header");
        $PLc = $VIPs;
        $PLf = $VIPf;
        $i = $indexM - 1;
        $max = $PLc[$i]->max;
        ?>
     <!--Note: "warning" to make red; "success" for green-->
     <h1>Match Phase 1 (Manual): Very Important Projects(VIP)</h1>
     <h6>Choose upto <?php echo $max;?> students from the following possibilities.</h6>
     Note: When applicable green means the skill is fulfilled. Orange unfulfilled. Gray unnecessary (hover to reveal).
             <?php
            echo form_open('match/matchPhase1Helper', array(
            'class' => 'acceptedStud',
            ));?>
     <table style="width: 1000px">
    <tr>
        <td> <h2>Friendly Heuristic VIP Matching</h2></td>
        <td> <h2>Scientific Heuristic VIP Matching</h2></td>
    </tr>
        <?php
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
            echo ')</h5>';
            foreach($PLf[$i]->desiredStudents as $s){
                echo '<h6>';
                $data = array(
                    'class' => 'acceptedStud',
                    'name' => $s->id,
                    'id' => 'studentF',
                    'value' => $s->name,
                    'checked' => false,
                    );
                echo form_checkbox($data);
                echo $s->name;
                echo '</h6>Interest: ';
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
            echo ')</h5>';
            foreach($PLc[$i]->desiredStudents as $s){
                echo '<h6>';
                $data = array(
                    'class' => 'acceptedStud',
                    'name' => $s->id,
                    'id' => 'studentC',
                    'value' => $s->name,
                    'checked' => false,
                    );
                echo form_checkbox($data);
                echo $s->name;
                echo '</h6>Interest: ';
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
            echo '</td>';
            echo '</tr>';
        ?>
     
     </table>
     <span></span>
     
     
     <div id="alignForm">
        <?php                                
                echo form_submit(array(
                    'id' => 'match phase 1 helper',
                    'name' => 'match phase 1 helper',
                    'type' => 'Submit',
                    'class' => 'btn btn-primary btn-small pull-left',
                    'value' => 'Continue Manual VIP Matching',
                ));
                ?></div>
     <div id="alignForm" style=" padding-left: 500px">
        <?php                                
                echo form_submit(array(
                    'id' => 'match phase 1 helper',
                    'name' => 'match phase 1 helper',
                    'type' => 'Submit',
                    'class' => 'btn btn-primary btn-small pull-left',
                    'value' => 'Do Rest Automatically',
                ));
                ?></div>
        <script>
            var checkbox = document.getElementsByTagName("input");
            
            $(document).ready(function(){
                $("input").click(function(){
                    
                    if($('input:checked').length > <?php echo json_encode($max);?>){
                        $(this).attr("checked",false);
                        alert("You selected "+ <?php echo json_encode($max);?>+ " already. Cannot select more.");
                    }
                    else{
                        if($(this).attr("id") == "studentC"){
                            $("input[id = 'studentF'][name = '" +$(this).attr("name")+"']").prop("disabled",$(this).prop("checked"));
                        }
                        else if($(this).attr("id") == "studentF"){
                            $("input[id = 'studentC'][name = '" +$(this).attr("name")+"']").prop("disabled",$(this).prop("checked"));
                        }
                    }
                });
            });

            
            $('#match phase 1 helper').click(function(event) {
            
                var check = true;
            
                if($('input:checked').length != <?php echo json_encode($max);?>){
                    check = false;
                }
                
                if(check || confirm("Are you sure you want to continue? You've selected "+ $('input:checked').length + " of a possible " + <?php echo json_encode($max);?> +" students.")==true){
                            
                    $('#area').empty(); 
                    document.getElementById("progress").style.display = "block";
                    
                    $('input:checked');
                    $.post("match/matchPhase1Helper") 

                            .done(function(data) {
                               //alert("Data Loaded: " + data);
                               document.getElementById("progress").style.display = "none";
                                $('#area').html(data);
                            })
                            .fail(function() {
                                alert("error");
                            });
                    event.preventDefault();
                }
                });

        </script>
     <?php echo form_close();// $_SESSION['otherProjectState']= $_POST["OtherProject"];?>
    <?php $this->load->view("template_footer"); ?>
    </body>
</html>