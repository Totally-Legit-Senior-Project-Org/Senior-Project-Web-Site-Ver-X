<?php $this->load->view("template_header"); 
$this->load->helper("current_user");
$this->load->helper("nav_top");
?>


<h2>Senior Project Website</h2>
<p>Developed to help FIU Computer Science students to choose their senior project. </p>
<p>Performs an intelligent match between users and projects, determining the best project for each student.</p>

<ul>
    <lh><h4>Developers:</h4></lh>
    <lh><h5>Version 1.0 </h5></lh>
    <li>Camilo Sanchez </li>
    <li>Keiser Moya</li>
    <li>Yaneli Fernandez</li>
    
    <lh><h5>Version 2.0 </h5></lh>
    <li>Nelson Capote</li>
    <li>Michael Garcia</li>
    <li>Antonio Vazquez</li>

    <lh><h5>Version 3.0 </h5></lh>
    <li>Christopher Kerrutt</li>
    <li>William Marquez</li>
    <li>Cynthia Tope</li>
    
    <lh><h5>Version 4.0 </h5></lh>
    <li>Julio Perez</li>
</ul>
<ul>
	<?php
    if (isUserLoggedIn ($this))
	{
	 ?> 
     <lh><h4>User Guide</h4></lh>
      	<?php
     if (isHeadProfessor($this))
        echo anchor('files/head_guide' , "Head Professor User Guide");
     else if (isProfessor($this))
        echo anchor('files/mentors_guide' , "Mentor User Guide");
     else if (!isProfessor($this) )
         echo anchor('files/students_guide' , "Student User Guide");
	 
	
	 }  
     ?>   
    
</ul>




<ul>    
    <lh><h4>Technology Stack</h4></lh>
    <li><a href="http://ellislab.com/codeigniter">CodeIgniter</a></li>
    <li><a href="http://twitter.github.com/bootstrap/">Bootstrap</a></li>    
    <li><a href="http://www.mysql.com">MySql</a></li>
    <li><a href="http://git-scm.com">Git</a></li>
    <li><a href="http://www.apache.org">Apache</a></li>
</ul>


<?php $this->load->view("template_footer"); ?>
