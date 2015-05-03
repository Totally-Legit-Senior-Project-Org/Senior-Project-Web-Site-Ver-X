<div id="progress" style="display: none; text-align: center;">
    <img alt="Press the back button on your browser re-rank and save. Then you can click Run Match again." src="img/loader.gif">
    <br><br>
</div>

<div id='area'>
    <?php $this->load->view("template_header"); ?>
    <h3>Welcome to the Senior Project Website Matching Algorithm page!</h3>
    <hr>
    <h4>Click  go to project priority if you want to change current project priorities.</h4>
    <div>
    <?php echo anchor('match/gotoProjectPriority', 'Go to Project Priority', array('class' => 'btn btn-primary btn-small pull-left')) ?>
</div>
    <br><hr>
    <h4>Click run match to do a matching based on current student interests and project priorities.</h4>
   



    
            <?php
            echo form_open('match/preProcessSteps', array(
                'class' => 'form-inline',
            ));
            ?>

                <?php
                echo form_submit(array(
                    'id' => 'runmatch',
                    'name' => 'run match',
                    'type' => 'Submit',
                    'class' => 'btn btn-primary btn-small pull-left',
                    'value' => 'Run Match'
                ));
                ?>

        <?php echo form_close() ?>
        <script>
            $('#runmatch').click(function(event) {
                $('#area').empty(); 
                //document.getElementById("progress").style.display = "block";
                $.post("match/matchStart") 
                   
                        .done(function(data) {
                            //alert("Data Loaded: " + data);
                           //document.getElementById("progress").style.display = "none";
                            $('#area').html(data);
                        })
                        .fail(function() {
                            alert("error");
                        });
                event.preventDefault();
            });

        </script>
        <br>
        <br>
        <?php echo anchor('files/match_guide' , "Matchmaking Guide Click Here to Download Guide"); ?>
        <?php $this->load->view("template_footer"); ?>
    </div>

    

</div>



