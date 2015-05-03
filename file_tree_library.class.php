<?php

class file_tree_library
{    
    var $odd = false;
    var $elementArray = array();
    var $nameOfStorage = "expandedStorage";
    var $date = "";
    var $owner = "";
    var $ownerID = "";
    var $due_date = "";
    var $needsHeader = false;

    function writeCSS()
    {
        ?>
        <style type="text/css">
            
        .ellipsis {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                -o-text-overflow: ellipsis;
            }
        #table{
                background:#F8F8F8;
                border:1px solid #A0A0A0;
                border-right:none;
                float:right;
                font-size:11px
            }

        #topNodes{
                margin-left:10px;
                padding-left:0px;
        }
        #topNodes ul{
                margin-left:20px;
                padding-left:15px;
               
                display:none;
        }
        #tree li{
                list-style-type:none;
                font-family:sans-serif;
                font-size:16px;
                padding-top:8px;
                border:1px solid #A0A0A0;

        }        
        #tree .tree_link{
                line-height:20px;
                padding-left:15px;
              
        }
        #tree due{
                
        }
        #tree img{
                padding-top:6px;
        }
        #tree a{
                color: #282828;
                text-decoration:none;    
                
        }
        .activeNodeLink{
                line-height:18px;
                background-color: #316AC5;
                color: #FFFFFF;
                font-weight:bold;
                padding-top:6px;
        }
        </style>
        <?php
    }
    
    function writeJavascript()
    {
        $base_url = $this->config->base_url();
        ?>
        <script type="text/javascript">

        var plusNode = '<?php echo $$base_url; ?>' . '/img/plus_folder.png';
        var minusNode = '<?php echo $$base_url; ?>' . '/img/minus_folder.png';

        var nameOfStorage = '<?php echo $this->nameOfStorage; ?>';
        <?php
        $cookieValue = "";
        if(isset($_COOKIE[$this->nameOfStorage]))
        {
            $cookieValue = $_COOKIE[$this->nameOfStorage];
        }
        echo "var initExpandedNodes =\"".$cookieValue."\";\n";
        ?>
        function expandAll()
        {
            var treeObj = document.getElementById('tree');
            var images = treeObj.getElementsByTagName('IMG');
            for(var no=0;no<images.length;no++)
            {
                if(images[no].className=='tree_plusminus' && images[no].src.indexOf(plusNode)>=0)
                {
                    expandNode(false,images[no]);
                }
            }
        }
        
        function collapseAll()
        {
            var treeObj = document.getElementById('tree');
            var images = treeObj.getElementsByTagName('IMG');
            for(var no=0;no<images.length;no++)
            {
                if(images[no].className=='tree_plusminus' && images[no].src.indexOf(minusNode)>=0)
                {
                    expandNode(false,images[no]);
                }
            }
        }

        function expandNode(e,inputNode)
        {
            if(initExpandedNodes.length==0)
            {
                initExpandedNodes=",";
            }
            if(!inputNode)
            {
                inputNode = this;
            }
            if(inputNode.tagName.toLowerCase()!='img')
            {
                inputNode = inputNode.parentNode.getElementsByTagName('IMG')[0];
            }
            var inputId = inputNode.id.replace(/[^\d]/g,'');
            var parentUl = inputNode.parentNode;
            var subUl = parentUl.getElementsByTagName('UL');
            if(subUl.length==0)
            {
                return;
            }
            if(subUl[0].style.display=='' || subUl[0].style.display=='none')
            {
                subUl[0].style.display = 'block';
                inputNode.src = minusNode;
                initExpandedNodes = initExpandedNodes.replace(',' + inputId+',',',');
                initExpandedNodes = initExpandedNodes + inputId + ',';
            }
            else
            {
                subUl[0].style.display = '';
                inputNode.src = plusNode;
                initExpandedNodes = initExpandedNodes.replace(','+inputId+',',',');
            }
        }

        function initTree()
        {
            var parentNode = document.getElementById('tree');
            var lis = parentNode.getElementsByTagName('LI'); 
            for(var no=0;no<lis.length;no++)
            {
                var subNodes = lis[no].getElementsByTagName('UL');
                if(subNodes.length>0)
                {
                    lis[no].childNodes[0].style.visibility='visible';
                }
                else
                {
                    lis[no].childNodes[0].style.display='none';
                }
            }

            var images = parentNode.getElementsByTagName('IMG');
            for(var no=0;no<images.length;no++)
            {
                if(images[no].className=='tree_plusminus')
                {
                    images[no].onclick = expandNode;
                    images[no].style.cursor = 'pointer';
                }
            }
            var aTags = parentNode.getElementsByTagName('A');         
            var cursor = 'pointer';
            if(document.childNodes)
            {
                cursor = 'hand';
            }
            for(var no=0;no<aTags.length;no++)
            {
                aTags[no].onclick = expandNode;    
                aTags[no].style.cursor = cursor;
            }
            
            var labelTags = parentNode.getElementsByTagName('LABEL');
            for(var no=0;no<labelTags.length;no++)
            {
                labelTags[no].style.cursor = 'default';
            }
                        
            var initExpandedArray = initExpandedNodes.split(',');
            for(var no=0;no<initExpandedArray.length;no++)
            {
                if(document.getElementById('plusMinus' + initExpandedArray[no]))
                {
                    var obj = document.getElementById('plusMinus' + initExpandedArray[no]);
                    expandNode(false,obj);
                }
            }
        }
        window.onload = initTree;

        </script>
        <?php
    }
    
    function addToArrayAss($element)
    {
        if(!isset($element['parentId']) || !$element['parentId'])
        {
            $element['parentId'] = 0;
        }

        $base_url = $this->config->base_url();
        
        $element['code'] = isset($element['code']) ? $element['code'] : 'javascript:return false';
        $element['url'] = isset($element['url']) ? $element['url'] : 'javascript:return false';
        $element['target'] = isset($element['target']) ? $element['target'] : '';
        $element['icon'] = isset($element['icon']) ? '' . $base_url . '/img/empty_folder2.png': '';
        $element['onclick'] = isset($element['onclick']) ? $element['onclick'] : 'javascript:return false';
        $element['category'] = isset($element['category']) ? $element['category'] : 'uncateg';
        $element['date'] = isset($element['date']) ? $element['date'] : '';
        $element['owner'] = isset($element['owner']) ? $element['owner'] : '';
        $element['ownerID'] = isset($element['ownerID']) ? $element['ownerID'] : '';
        
        $this->elementArray[$element['parentId']][] = array(
                                'id' => $element['id'],
                                'code' => $element['code'],
                                'title' => $element['title'],
                                'url' => $element['url'],
                                'target' => $element['target'],
                                'icon' => $element['icon'],
                                'onclick' => $element['onclick'],
                                'category' => $element['category'],
                                'date' => $element['date'],
                                'owner' => $element['owner'],
                                'ownerID' => $element['ownerID']
                        );
    }

    function drawSubNode($parentID)
    {
        if(isset($this->elementArray[$parentID]))
        {            
            echo "<ul>";            
            for($no=0;$no<count($this->elementArray[$parentID]);$no++)
            {
                $urlAdd = " href=\"#\"";
                if($this->elementArray[$parentID][$no]['url'])
                {                    
                    $urlAdd = " href=\"".$this->elementArray[$parentID][$no]['url']."\"";
                    if($this->elementArray[$parentID][$no]['target'])
                    {
                        $urlAdd.=" target=\"".$this->elementArray[$parentID][$no]['target']."\"";
                    }
                }
                
                $onclick = "";
                if($this->elementArray[$parentID][$no]['onclick'])
                {
                    $onclick = " onmouseup=\"".$this->elementArray[$parentID][$no]['onclick'].";return false\"";
                }
                
                //***************************FILE**********************************************************     
                if($this->elementArray[$parentID][$no]['category'] == 'file')          
                {
                    if($this->needsHeader)
                    {
                        echo " <table id='table' width='100%' >
                                    <tr>
                                        <th width='57%' style='padding-left:20px;'>File Name</th>
                                        <th width='15%'>Uploaded By</th>       
                                        <th width='12%'>Upload Date</th>
                                        <th width='10%'>Download</th>
                                        <th>Delete</th>
                                    </tr>
                               </table><br>";
                        $this->needsHeader = false;
                    }
                    
                    if($this->odd)
                    {
                        echo "<li class=\"tree_node\" style=\"padding-left:5px;"
                                                           . "height:30px;"
                                                           . "background:#E8E8E8;"
                                                           . "border-right:none;"
                                                           . "border-bottom:none;\">";
                        $this->odd = false;
                    }
                    else
                    {
                        echo "<li class=\"tree_node\" style=\"padding-left:5px;"
                                                           . "height:30px;"
                                                           . "background:#F0F0F0;"
                                                           . "border-right:none;"
                                                           . "border-bottom:none;\">";
                        $this->odd = true;
                    }
                    
                    $base_url = $this->config->base_url();
                    
                    echo "<img class=\"tree_plusminus\" id=\"plusMinus"
                                        . $this->elementArray[$parentID][$no]['id']
                                        . "\" src=\"" . $base_url . "/img/plus_folder.png\">"
                                        . "<img src=\"".$this->elementArray[$parentID][$no]['icon']."\">";
                    echo "<a class=\"tree_link\" $urlAdd$onclick style=\"width:350px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;-o-text-overflow: ellipsis;display:inline-block\" >"
                                        . $this->elementArray[$parentID][$no]['title']."</a>";            
                    $CI =& get_instance();
                    $CI->load->helper('current_user');                    
                    if(isHeadProfessor($this))
                    {
                        echo form_submit(array(              
                                 'type'  => 'checkbox',
                                 'name' => 'delete_files[]',
                                 'value' => $this->elementArray[$parentID][$no]['code'],
                                 'style' => 'float:right;margin-right:20px;'
                        ));   
                    }
                    elseif(getCurrentUserId($this) == $this->elementArray[$parentID][$no]['ownerID'])
                    {
                        echo form_submit(array(              
                                 'type'  => 'checkbox',
                                 'name' => 'delete_files[]',
                                 'value' => $this->elementArray[$parentID][$no]['code'],
                                 'style' => 'float:right;margin-right:25px;'
                        ));    
                    }
                    else 
                    {
                        echo form_submit(array(              
                                 'type'  => 'checkbox',
                                 'name' => 'delete_files[]',
                                 'value' => $this->elementArray[$parentID][$no]['code'],
                                 'style' => 'float:right;margin-right:25px;',
                                 'disabled' => 'disabled'
                        ));   
                    }
                    $data = array(
                                'name' => 'download_files[]',
                                'id' => 'button',
                                'value' => $this->elementArray[$parentID][$no]['code'], 
                                'class' => 'btn-small btn-info',
                                'type' => 'submit',
                                'content' => 'Download',
                                'style' => 'float:right;margin-right:18px;font-size:13px;'
                    );
                    echo form_button($data);
                    
                    //convert sql date format yyyy-mm-dd into mm/dd/yyyy format
                    $sqlDate = $this->elementArray[$parentID][$no]['date'];
                    $newDate = date("m/d/Y", strtotime($sqlDate));
                    if(($newDate > $this->due_date) && ($this->due_date != ""))
                    {
                        $attributes1 = array(
                            'class' => '',
                            'style' => 'float:right;'
                                    . 'margin-right:18px;'
                                    . 'font-size:13px;'
                                    . 'color:red;'
                        );
                    } else
                    {
                        $attributes1 = array(
                            'class' => '',
                            'style' => 'float:right;'
                                     . 'margin-right:18px;'
                                     . 'font-size:13px;'
                        );
                    }
                    echo form_label($newDate, 'due_date', $attributes1);
                    $attributes2 = array('class' => '',
                                         'style' => 'float:right;'
                                                   . 'margin-right:25px;'
                                                   . 'font-size:13px;'                                                   
                                        );
                    echo form_label($this->elementArray[$parentID][$no]['owner'], 'owner', $attributes2);
                    echo "</li>";                       
                }
                
                //******************************MILESTONE**************************************************
                elseif($this->elementArray[$parentID][$no]['category'] == 'milestone')  
                {
                    $this->odd = false;
                    echo "<li class=\"tree_node\" style=\"background:#F8F8F8;"
                                                       . "border-right:none;"
                                                       . "border-bottom:none;\">";
                    
                    echo "<img class=\"tree_plusminus\" id=\"plusMinus"
                                     . $this->elementArray[$parentID][$no]['id']
                                     . "\" src=\"" . $base_url . "/img/plus_folder.png\">"
                                     . "<img src=\"".$this->elementArray[$parentID][$no]['icon']."\">";
                    
                    echo "<b><a class=\"tree_link\">"
                                     . $this->elementArray[$parentID][$no]['title']."</a></b>";
                    if(!$this->elementArray[$parentID][$no]['date'] == "")
                    {
                        $attributes = array(
                                'class' => 'milestone_due',
                                'style' => 'margin-right:140px;'
                                         . 'font-size:13px;'
                                         . 'padding-top:3px;'
                                         . 'float:right;'
                                         . 'font-weight:bold;'
                        );
                        echo form_label('[ Due Date:&nbsp&nbsp&nbsp&nbsp&nbsp'
                                        . $this->elementArray[$parentID][$no]['date'].' ]', 
                                        'date', 
                                        $attributes);
                    }
                    $this->needsHeader = true;
                    $this->due_date = $this->elementArray[$parentID][$no]['date'];
                    $this->drawSubNode($this->elementArray[$parentID][$no]['id']);
                    echo "</li>"; 
                }
                //*****************************************PROJECT********************************************
                else                
                {
                    echo "<li class=\"tree_node\" style=\"background:#E8E8E8;"
                                                       . "border-right:none;"
                                                       . "border-bottom:none;\">";
                    echo "<img class=\"tree_plusminus\" id=\"plusMinus" 
                                    . $this->elementArray[$parentID][$no]['id'] 
                                    . "\" src=\"" . $base_url . "/img/plus_folder.png\">"
                                    . "<img src=\"".$this->elementArray[$parentID][$no]['icon']."\">";                        
                    echo "<b><a class=\"tree_link\" style=\"width:600px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;-o-text-overflow: ellipsis;display:inline-block\" >"
                                    . $this->elementArray[$parentID][$no]['title']."</a></b>";  
                    
                    
                    $project_name = str_replace("'","\'", $this->elementArray[$parentID][$no]['title']);
////                    echo $project_name;
////                    echo readline();
//                    
                    $files = $this->db->query('SELECT *
                                            FROM spw_uploaded_file
                                            WHERE project_name = "'.$this->elementArray[$parentID][$no]['title'].'"');   

                    if($files->num_rows() > 0)
                    {
                        $data = array(
                                    'name' => 'download_project[]',
                                    'id' => 'button',
                                    'value' => $this->elementArray[$parentID][$no]['title'], 
                                    'class' => 'btn-small btn-info',
                                    'type' => 'submit',
                                    'content' => 'Download ZIP',
                                    'style' => 'float:right;margin-right:18px;font-size:13px;'
                        );
                        echo form_button($data);
                    }
//                    else 
//                    {                    
//                        $data = array(
//                                    'name' => 'download_project[]',
//                                    'id' => 'button',
//                                    'value' => $this->elementArray[$parentID][$no]['title'], 
//                                    'class' => 'btn-small btn-info.disabled',
//                                    'type' => 'submit',
//                                    'content' => 'Download ZIP',
//                                    'style' => 'float:right;margin-right:24px;font-size:13px;',
//                                    'disabled' => 'disabled',
//                        );
//                        echo form_button($data);    
//                    }
                    $this->needsHeader = true; 
                    $this->drawSubNode($this->elementArray[$parentID][$no]['id']);
                    echo "</li>"; 
                }
            }
            echo "</ul>";            
        }                
    }

    function drawTree()
    {
        if(!isProfessor($this) || !isHeadProfessor($this)) 
        {
            $data = array(
                        'name' => 'download_project[]',
                        'id' => 'button',
                        'value' => $this->elementArray[0][0]['title'], 
                        'class' => 'btn-small btn-info',
                        'type' => 'submit',
                        'content' => 'Download Project as ZIP',
                        'style' => 'float:right;margin-right:10px;font-size:13px;'
            );                               
            echo form_button($data);
            echo "<br>";
        }
        echo "<br>";

        echo "<div id=\"tree\">";       
        echo "<ul id=\"topNodes\">";    
        echo form_submit(array(
                    'id'    => 'btn-act-deact',
                    'name'  => 'action',
                    'type'  => 'Submit',
                    'class' => 'btn btn-primary pull-right',
                    'value' => 'Delete',
                    'style' => 'margin-right:10px;margin-top:8px;',                    
        ));           
        echo form_submit(array(
                    'id'    => 'expand',
                    'name'  => 'expand',
                    'type'  => 'Submit',
                    'class' => 'btn btn-primary pull-right',
                    'value' => 'Expand All',
                    'style' => 'margin-right:10px;margin-top:8px;',
                    'onclick' =>'expandAll();return false'
        ));
        echo form_submit(array(
                    'id'    => 'collapse',
                    'name'  => 'collapse',
                    'type'  => 'Submit',
                    'class' => 'btn btn-primary pull-right',
                    'value' => 'Collapse All',
                    'style' => 'margin-right:10px;margin-top:8px;',
                    'onclick' => 'collapseAll();return false'
        ));

        for($no=0;$no<count($this->elementArray[0]);$no++)
        {
            $urlAdd = "";
            if($this->elementArray[0][$no]['url'])
            {
                $urlAdd = " href=\"".$this->elementArray[0][$no]['url']."\"";
                if($this->elementArray[0][$no]['target'])
                {
                    $urlAdd.=" target=\"".$this->elementArray[0][$no]['target']."\"";
                }
            }
            $onclick = "";
            if($this->elementArray[0][$no]['onclick'])
            {
                $onclick = " onmouseup=\"".$this->elementArray[0][$no]['onclick'].";return false\"";
            }
            
            echo "<li class=\"tree_node\" id=\"node_".$this->elementArray[0][$no]['id']."\">";
            
            $base_url = $this->config->base_url();
            
            echo "<img id=\"plusMinus"
                            . $this->elementArray[0][$no]['id']
                            . "\" class=\"tree_plusminus\" "
                            . "src=\"" . $base_url . "/img/plus_folder.png\">";
            echo "<img src=\"".$this->elementArray[0][$no]['icon']."\">";
            echo "<b><a class=\"tree_link\">"               
                            . $this->elementArray[0][$no]['title']."</a></b>";
            $this->drawSubNode($this->elementArray[0][$no]['id']);

            echo "</li>";            
        }
        echo "</ul>";        
        echo "</div>";
    }
    
    public function __get($var)
    {
        return get_instance()->$var;
    }
    
    function isEmpty()
    {
        $answer= true;
        if(parentId > 0)
        {
            $answer = false;
        }
        return $answer;
    }
}
?>
