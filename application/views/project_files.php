<?php 
    $this->load->view('template_header');
    $this->load->helper('user_image');
    $this->load->helper('current_user'); 
    $this->load->helper('form');
    $this->load->helper('file');
    $this->load->helper('nav_top');    

    $milestones = $this->db->query('SELECT milestone_id, milestone_name, due_date 
                                                FROM spw_milestones
                                                WHERE deleted = "false"
                                                ORDER BY due_date');
    
    if(isset($_SESSION['sessionOrder']) && !isset($_POST['order'])) 
    {
        $order = $_SESSION['sessionOrder'];  
    }
    elseif(isset($_POST['order']))
    {
        $order = $_POST['order'];
        $_SESSION['sessionOrder'] = $order;
    }    
    else 
    {
        if(!$milestones->num_rows() > 0)
        {
            $order = 'View By Project';
        }
        else 
        {
            $order = 'View By Milestone';
        }
    }                             
        ?>
        <div>
        <?php                
        if( isHeadProfessor($this) ) {
        ?>
           <h2>Files Repository</h2>
        <?php 
        } else {
        ?>
           <h3>My Project Repository</h3>             
        <?php 
        }
        ?>      
        <?php        
            if( isHeadProfessor($this) ) 
            {
                echo form_open('admin/milestones_view');
                echo form_submit(array(              
                          'type'  => 'button',
                          'value' => 'Manage Milestones',
                          'style'   => 'float:right;margin-left: 8px',
                          'class'   => 'btn btn-primary',
                          'onclick' => 'submit();',
                 ));
                echo form_close(); 
            }   
        ?>           
           <br><br>                              
        </div>
        <div class="well">
            <h4 style="padding-left:25px;">
                Upload: 
            </h4>
            <br>
                <?php 
                echo form_open_multipart('filescontroller/do_upload');
                $milestones = $this->db->query('SELECT milestone_id, milestone_name, due_date 
                                                FROM spw_milestones
                                                WHERE deleted = "false"
                                                ORDER BY due_date');
                if(isHeadProfessor($this))
                {
                    $projects = $this->db->query('SELECT id, title 
                                                FROM spw_project
                                                WHERE status = "APPROVED" OR status = "PENDING APPROVAL"
                                                ORDER BY title'); 
                } 
                elseif(isProfessor($this))
                {
                    $projects = $this->db->query('SELECT id, title
                                                FROM spw_project
                                                WHERE mentor = "'.getCurrentUserId($this).'"
                                                AND status = "APPROVED" OR status = "PENDING APPROVAL"');
                }
                else
                {
                    $project_num = $this->db->query('SELECT project
                                                     FROM spw_user
                                                     WHERE id = "'.getCurrentUserId($this).'"');   
                    foreach($project_num->result_array() as $row)
                    {     
                        $projects = $this->db->query('SELECT id, title 
                                                    FROM spw_project
                                                    WHERE id = "'.$row['project'].'"
                                                    AND status = "APPROVED" OR status = "PENDING APPROVAL"');
                    }
                }
              ?>      
            <table style="width:60%;">
                <?php                
                if( isHeadProfessor($this) || isProfessor($this))
                {
                ?>
                <tr>
                    <td style="padding-left:30px;">
                       <b>Projects:</b>
                    </td>
                    <?php
                    if($milestones->num_rows() > 0)
                    {
                    ?>
                    <td style="padding-left:20px;">
                        <b>Milestones:</b>
                    </td>
                    <?php
                    }
                    ?>
               </tr>
                <?php
                }
                ?>      
               <tr>
                <?php      
                if(!isHeadProfessor($this) && !isProfessor($this)) 
                {
                    if($milestones->num_rows() > 0)
                    {
                    ?>
                     <td >
                         <b style="margin-left:100px">Milestones:</b>
                     </td>
                     <?php
                    }
                    else
                    {
                        ?>
                        <td >
                            <b style="margin-left:200px"></b>
                        </td>
                        <?php
                    }
                 }
                 $projects_list = array();
                 $milestone_array = array();
                 ?>
                 <td style="padding-left:25px;">
                 <?php  
                 foreach ($projects->result_array() as $row)
                 {             
                      $projects_list[$row["title"]] = $row["title"];
                 }   
                 if(isHeadProfessor($this) || isProfessor($this))
                 {                                                                                         
                     echo form_dropdown('projects', $projects_list);                                                        
                 }
                 else 
                 {                                      
                     echo form_hidden('projects', $projects_list[$row["title"]]);                                              
                 }
                   ?>              
                 </td>
                 <?php

                 //if no milestone exist do not show dropdown
                 if($milestones->num_rows() > 0)
                 {
                 ?>
                 <td style="padding-left:10px;">
                     <?php
                     foreach ($milestones->result_array() as $row)
                     {                  
                         $milestone_array[$row["milestone_name"]] = $row["milestone_name"];
                     } 
                     echo form_dropdown('milestones', $milestone_array);
                     ?>
                 </td>
                 <?php
                 }  
                 ?>                   
                 <td>
                     <?php
                     echo form_submit(array(              
                              'type'  => 'file',
                              'class' => 'btn-small btn-info',
                              'name'  => 'userfile',
                              'style' => 'margin-left:10px;'
                     ));
                     ?>
                 </td>
                 <td>
                     <?php
                     echo form_submit(array(              
                              'type'  => 'Submit',
                              'class' => 'btn btn-primary',
                              'value' => 'Upload File',   
                              'style' => 'margin-left:10px;'
                     ));    
                     ?> 
                 </td>    
               </tr>
            </table>
            <?php
            echo form_close();
            ?>
        </div>
        <div class="well">
        <?php

        if( isHeadProfessor($this) || isProfessor($this)) 
        {
            echo form_open('files/project_files', array(
                            'action' => $_SERVER['PHP_SELF'],
                            'method' => 'POST',
                            'name'   => 'order',
                            'id'     => 'order',
            ));       

            if ($order == 'View By Project')
            {
                if($milestones->num_rows() > 0)
                {
                    echo form_submit(array(
                                'name'  => 'order',
                                'type'  => 'button',
                                'class' => 'btn btn-primary pull-left',
                                'value' => 'View By Milestone',
                                'style' => 'margin-left:30px;margin-top:25px;',
                                'onClick' => 'submit();'
                    ));  
                    echo form_hidden('order', 'View By Milestone');                   
                } else 
                { 
                echo form_submit(array(
                                'name'  => 'order',
                                'type'  => 'button',
                                'class' => 'btn btn-primary.disabled pull-left ',
                                'value' => 'View By Milestone',
                                'style' => 'margin-left:30px;margin-top:25px;',
                                'disabled' => 'disabled'
                    ));  
                }
               
            } else 
            {
                echo form_submit(array(
                            'name'  => 'order',
                            'type'  => 'button',
                            'class' => 'btn btn-primary pull-left',
                            'value' => 'View By Project',
                            'style' => 'margin-left:30px;margin-top:25px;', 
                            'onClick' => 'submit();'
                ));  
                 echo form_hidden('order', 'View By Project');  
            }
            echo form_close();   
            echo "<br><br>";
        }
        echo form_open('filescontroller/download_delete_files', array(
                        'class' => '',
                        'id' => 'files_delete_form'
        ));                                      
        $index = 1;
        
        $tree = new file_tree_library();
        
        //Display View By Project
        if ($order == 'View By Project')
        {            
            //root named Projects
            $tree->addToArrayAss(array(
                'id'    => $index, 
                'title'  => 'Projects',
                'ParentID' => 0,
                'category' => 'byProjects'
                )
            );
            
            foreach($projects->result_array() as $row)
            {                    
                $projName = $row['title'];               
                
                $files = $this->db->query('SELECT id, file_name, path_to_file, uploaded_by_user, project_name, upload_date, milestone_name
                                            FROM spw_uploaded_file
                                            WHERE project_name = "'.$projName.'" AND milestone_name="0"
                                            ORDER BY upload_date');
                $index++;

                if($milestones->num_rows() > 0 || $files->num_rows() > 0)
                {
                    //add projects to tree with minus or plus folder icons
                    $tree->addToArrayAss(array(
                        'id'    => $index, 
                        'title'  => $projName,
                        'parentId' => 1,
                        'category' => 'project'
                        )
                    );

                    $parent = $index;
                    foreach ($files->result_array() as $row3)
                    {                
                        $owner = $this->db->query('SELECT id, first_name, last_name
                                                   FROM spw_user
                                                   WHERE id = "'.$row3['uploaded_by_user'].'"');    
                        foreach ($owner->result_array() as $row4)
                        {      
                            $ownerID = $row4['id'];
                            $uploaded_by = $row4['first_name']." ".$row4['last_name'];
                        }

                        $timestamp = $row3['upload_date'];
                        $due = substr($timestamp, 0, strrpos($timestamp, ' '));
                        $index++;
                        
                        //add files directly to project in the tree
                        $tree->addToArrayAss(array(
                            'id'    => $index, 
                            'title'  => $row3['file_name'],
                            'parentId' => $parent,
                            'category' => 'file',
                            'code'  => $row3['id'],
                            'date' => $due,
                            'owner' => $uploaded_by,
                            'ownerID' => $ownerID
                        ));
                    }                             
                } else 
                {                  
                    //add projects with empty folder icons to the tree
                    $tree->addToArrayAss(array(
                        'id'    => $index, 
                        'title'  => $projName,
                        'parentId' => 1,
                        'icon'  => '',
                        'category' => 'project'
                        )
                    );
                }

                if($milestones->num_rows() > 0)
                {
                    foreach($milestones->result_array() as $row2)
                    {     
                        $index++;
                        
                        $milesName = $row2['milestone_name'];

                        $files2 = $this->db->query('SELECT id, file_name, path_to_file, uploaded_by_user, project_name, upload_date, milestone_name
                                                    FROM spw_uploaded_file
                                                    WHERE project_name = "'.$projName.'" AND milestone_name="'.$milesName.'"
                                                    ORDER BY upload_date');  

                        if($files2->num_rows() > 0)
                        {
                            //add milestones with plus or minus folder icons inside projects in the tree
                            $tree->addToArrayAss(array(
                                'id'    => $index, 
                                'title'  => $milesName,
                                'parentId' => $parent,
                                'category' => 'milestone',
                                'date' => $row2['due_date']
                            ));                    
                        }
                        else 
                        {
                            //add milestones with empty folder icons inside projects in the tree
                            $tree->addToArrayAss(array(
                                'id'    => $index, 
                                'title'  => $milesName,
                                'parentId' => $parent,
                                'icon'  => '',
                                'category' => 'milestone',
                                'date' => $row2['due_date']
                            )); 
                        }
                        $parent2 = $index;                                                        
                        foreach ($files2->result_array() as $row3)
                        {          
                            $owner = $this->db->query('SELECT id, first_name, last_name
                                                       FROM spw_user
                                                       WHERE id = "'.$row3['uploaded_by_user'].'"');    
                            foreach ($owner->result_array() as $row4)
                            {      
                                $ownerID = $row4['id'];
                                $uploaded_by = $row4['first_name']." ".$row4['last_name'];
                            }

                            $timestamp = $row3['upload_date'];
                            $due = substr($timestamp, 0, strrpos($timestamp, ' '));
                            $index++;
                            $tree->addToArrayAss(array(
                                'id'    => $index, 
                                'title'  => $row3['file_name'],
                                'parentId' => $parent2,
                                'category' => 'file',
                                'code'  => $row3['id'],
                                'date' => $due,
                                'owner' => $uploaded_by,
                                'ownerID' => $ownerID
                            ));
                        }
                    }                
                }  
            }
        }      
  //********************************************************************************************  
        //Display View By Milestones        
        else {
            $title = '';
            foreach($projects->result_array() as $row)
            {  
                $title = $row['title'];
            }
        
            if(isHeadProfessor($this) || isProfessor($this))
            {
                $tree->addToArrayAss(array(
                    'id'    => $index, 
                    'title'  => 'Milestones',
                    'ParentID' => 0,
                    'category' => 'byMilestones'
                    )
                );
            }
            else
            {                                
                $tree->addToArrayAss(array(
                    'id'    => $index, 
                    'title'  => $title,
                    'ParentID' => 0,
                    'category' => 'byMilestones'
                    )
                );
            }
            
            foreach($milestones->result_array() as $row)
            {     
                $index++;
                $milesName = $row['milestone_name'];
//
//                $tree->addToArrayAss(array(
//                        'id'    => $index, 
//                        'title'  => $milesName,
//                        'parentId' => 1,
//                        'category' => 'milestone',
//                        'date' => $row['due_date']
//                        )
//                    );
                
                
                $files3 = $this->db->query('SELECT id, file_name, path_to_file, uploaded_by_user, project_name, upload_date, milestone_name
                            FROM spw_uploaded_file
                            WHERE project_name = "'.$title.'" AND milestone_name="'.$milesName.'"
                            ORDER BY upload_date');
                if(!isHeadProfessor($this) && !isProfessor($this))
                {
                    if($files3->num_rows() > 0)
                    {
                        //add milestones with plus or minus folder icons inside projects in the tree
                        $tree->addToArrayAss(array(
                            'id'    => $index, 
                            'title'  => $milesName,
                            'parentId' => 1,
                            'category' => 'milestone',
                            'date' => $row['due_date']
                        ));                    
                    }
                    else 
                    {
                        //add milestones with empty folder icons inside projects in the tree
                        $tree->addToArrayAss(array(
                            'id'    => $index, 
                            'title'  => $milesName,
                            'parentId' => 1,
                            'icon'  => '',
                            'category' => 'milestone',
                            'date' => $row['due_date']
                        )); 
                    }
                }
                else 
                {
                    //add milestones with empty folder icons inside projects in the tree
                    $tree->addToArrayAss(array(
                        'id'    => $index, 
                        'title'  => $milesName,
                        'parentId' => 1,
                        'category' => 'milestone',
                        'date' => $row['due_date']
                    )); 
                }
                
                $parent = $index;
                foreach($projects->result_array() as $row2)
                {     
                    
                    $projName = $row2['title'];

                    $files = $this->db->query('SELECT id, file_name, path_to_file, uploaded_by_user, project_name, upload_date, milestone_name
                            FROM spw_uploaded_file
                            WHERE project_name = "'.$projName.'" AND milestone_name="'.$milesName.'"
                            ORDER BY upload_date');   
                    if(isHeadProfessor($this) || isProfessor($this))
                    {
                        $index++;
                        //add files without milestones
                        if($files->num_rows() > 0)
                        {
                            $tree->addToArrayAss(array(
                                'id'    => $index, 
                                'title'  => $projName,
                                'parentId' => $parent,
                                'category' => 'project'
                            ));                    
                        }
                        else
                        {
                            $tree->addToArrayAss(array(
                                'id'    => $index, 
                                'title'  => $projName,
                                'parentId' => $parent,
                                'icon'  => '',
                                'category' => 'project'
                            )); 
                        }
                    }
                    $parent2 = $index;                                                        
                    foreach ($files->result_array() as $row3)
                    {                
                        $owner = $this->db->query('SELECT id, first_name, last_name
                                                   FROM spw_user
                                                   WHERE id = "'.$row3['uploaded_by_user'].'"');
                        foreach ($owner->result_array() as $row4)
                        {      
                            $ownerID = $row4['id'];
                            $uploaded_by = $row4['first_name']." ".$row4['last_name'];
                        }

                        $timestamp = $row3['upload_date'];
                        $due = substr($timestamp, 0, strrpos($timestamp, ' '));
                        $index++;
                        $tree->addToArrayAss(array(
                            'id'    => $index, 
                            'title'  => $row3['file_name'],
                            'parentId' => $parent2,
                            'category' => 'file',
                            'code'  => $row3['id'],
                            'date' => $due,
                            'owner' => $uploaded_by,
                            'ownerID' => $ownerID
                            )
                        );                             
                    }
                } 
            }
        }

        $tree->writeCSS();
        $tree->writeJavascript();
        $tree->drawTree(); 
        
        
        
        echo form_close();
            ?>
      </div>
            <?php 
    $this->load->view("template_footer");  
    ?>
