<?php 
    if (isset($listTitle) && strlen($listTitle) > 0)
    {
?>
    <h4 class="muted"><?php echo $listTitle ?></h4>
<?php
    }
?>

<ul class="inline" id="<?php echo $prefix.'-container' ?>" 
    data-idwithlist="<?php echo $prefix.'hidden-ids'?>">
    <?php 
        if (isset($lUserSummaries) && count($lUserSummaries) > 0)
        {
            $userIdsArray = array();
            
            foreach ($lUserSummaries as $user_summary) 
            {
                $userIdsArray[] = $user_summary->user->id;
    ?>
                <li id="<?php echo $prefix.'-item-'.$user_summary->user->id ?>" 
                    data-userid="<?php echo $user_summary->user->id ?>"
                    class="top-align"> 
                    <?php
                        if (isset($bottomView) && strlen($bottomView) > 0 &&
                            (!isset($noBottomViewForCurrentUser) || 
                                !$noBottomViewForCurrentUser ||
                                getCurrentUserId($this) != $user_summary->user->id))
                        {
                            $this->load->view($bottomView, array('user_summary' => $user_summary, 'prefix' => $prefix));
                        }
                    ?>
                    
                    <?php $this->load->view('subviews/user_summary_full_name_image', array('user_summary' => $user_summary)) ?>

                      <div class="usr-top-view-padding center-text myUserRemover">
                        <?php
                            
                            if (isset($topView) && strlen($topView) > 0 &&
                                ((isset($isUserProfessor)
                                    || $isUserProfessor)))
                            {
                                $this->load->view($topView, array('user_summary' => $user_summary, 'prefix' => $prefix));
//                                echo form_open('projectcontroller/leaveByProf',array(
//                                    'id' => 'form-leaveBP-project',                                    
//                                ));
//
//                                echo form_hidden(array(
//                                    'pid' => $user_summary->user->project,
//                                    'uid' => $user_summary->user->id,
//                                    'pbUrl' => current_url()
//                                ));
//
//                                echo form_submit(array(
//                                    'id' => 'btn-leaveBP',
//                                    'name' => 'btn-submit',
//                                    'type' => 'Submit',
//                                    'class' => 'btn btn-mini btn-danger center-text',
//                                    'value' => 'Remove'
//                                    
//                                ));
//                                echo form_close();
                              }                   
                           ?>
                    </div>
                </li>
    <?php   
            }

            $lUserIdsStr = join(', ', $userIdsArray);
        } 
        else 
        {
            if (isset($errorMessage) && strlen($errorMessage) > 0)
            {
    ?>
                <li><?php echo $errorMessage ?></li>
    <?php
            }
        }
    ?>
</ul>
<?php 
    if (isset($lUserIdsStr) && strlen($lUserIdsStr) > 0) 
    {
?>
        <input type="hidden" 
            name="<?php echo $prefix.'hidden-ids'?>" 
            id="<?php echo $prefix.'hidden-ids'?>" 
            value="<?php echo $lUserIdsStr ?>"/>
<?php
    }
?>
 