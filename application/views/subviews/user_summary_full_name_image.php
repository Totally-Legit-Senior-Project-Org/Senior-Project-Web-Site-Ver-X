<?php $this->load->helper("user_image"); ?>

<div class='user-summary-full center-text'>
     <?php echo anchor('user/'.$user_summary->user->id, $user_summary->getFullName()) ?>
    <?php 
        $src = getUserImage($this, $user_summary->user->picture);
        if ($src == '/img/no-photo.jpeg')
        {
            $src = checkUserUploadedPic($this, $user_summary->user->id);
            if($src == null)
                $src = '/img/no-photo.jpeg';
        }

        echo '<a href="'.base_url().'user/'.$user_summary->user->id.'">';

            echo img(array(
                'src' => $src . '?x='. time(),
                'class' => 'user-img',
                'alt' => $user_summary->getFullName()
            ));

        echo '</a>';
    ?>
   
</div>