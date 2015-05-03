
<?php
       if($noTopViewForCurrentUser != "CLOSED"){
        echo form_open('projectcontroller/leaveByProf',array(
                                    'id' => 'form-leaveBP-project'
                                ));

                                echo form_hidden(array(
                                    'pid' => $user_summary->user->project,
                                    'uid' => $user_summary->user->id,
                                    'pbUrl' => current_url()
                                ));

                                echo form_submit(array(
                                    'id' => 'btn-leaveBP',
                                    'name' => 'btn-submit',
                                    'type' => 'Submit',
                                    'class' => 'btn btn-mini btn-danger center-text',
                                    'value' => 'Remove'
                                ));
                                echo form_close();
       }
 ?>

<!-- <script>  
  // function submitForm(){
   $(document).ready(function(){
        $('.myUserRemover').each(function(){
             $(this).click(function(e){
             e.preventDefault();
             e.stopPropagation();
         
            alertify.set({ labels: {
                    ok     : "Yes",
                    cancel : "No" }
                });
                
            alertify.confirm("Are you sure you want to remove this Student from the Project?", function (e) {
                if (e) {
                     document.getElementById().submit();
                    
                }
            });
        });
        });
    });
    //}
</script>-->