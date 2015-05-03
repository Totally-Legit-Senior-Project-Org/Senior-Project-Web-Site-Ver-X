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
            #alignTable td{
                text-align: center;
               
            }
            
            #alignForm{
                margin: 0 auto;
                width: 400px;
            }
        </style>
        
        <?php $this->load->view("template_header"); ?>
        <h1>Welcome to Senior Project Matchmaking</h1>
        <font size="2>"><b>To start, matchmaking will occur to projects ranked by you between 2 and 100 (also known as very important projects (VIP) in descending order).</b></font>
        <table id="alignTable" style="width: 939px">
        <tr>
            <td><h6>Would you like to match the projects one by one. (Manual)</h6></td>
            <td><h6>Would you like to match the projects all at once. (Automatic)</h6></td>
        </tr>
        <tr><td><div id="alignForm">
        <?php
                echo form_submit(array(
                    'id' => 'runmatch1',
                    'name' => 'run match',
                    'type' => 'Submit',
                    'class' => 'btn btn-primary btn-small pull-left',
                    'value' => 'Run Manual VIP Matching',
                    'onclick' => "send(this.form)"
                ));
                ?></div></td>
            <?php echo form_close() ?>
        <script>
            $('#runmatch1').click(function(event) {
                $('#area').empty(); 
                document.getElementById("progress").style.display = "block";
                $.data('auto','false');
                $.post("match/gotoManual") 
                   
                        .done(function(data) {
                            //alert("Data Loaded: " + data);
                           document.getElementById("progress").style.display = "none";
                            $('#area').html(data);
                        })
                        .fail(function() {
                            alert("error");
                        });
                event.preventDefault();
            });

        </script>
        <br><td><div id="alignForm">
        <?php                                
                echo form_submit(array(
                    'id' => 'runmatch2',
                    'name' => 'run match',
                    'type' => 'Submit',
                    'class' => 'btn btn-primary btn-small pull-left',
                    'value' => 'Run Automatic VIP Matching',
                ));
                ?></div></td>
        <script>
            $('#runmatch2').click(function(event) {
                $('#area').empty(); 
                document.getElementById("progress").style.display = "block";
                $.post("match/gotoAuto") 
                   
                        .done(function(data) {
                            //alert("Data Loaded: " + data);
                           document.getElementById("progress").style.display = "none";
                            $('#area').html(data);
                        })
                        .fail(function() {
                            alert("error");
                        });
                event.preventDefault();
            });

        </script>
        <br>
        </tr>
        </table>
        <?php $this->load->view("template_footer"); ?>
    </body>
</html>
