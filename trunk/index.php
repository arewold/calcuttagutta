<?
// Encoding UTF-8 TEST
include("globalvars.php");
require_once("common.php");

if(isset($_SESSION['modules_left']))
	$modules_left_array = $_SESSION['modules_left'];
else
	$modules_left_array = $default_modules_left;

if(isset($_REQUEST['module_left']))
	$new_module_left = $_REQUEST['module_left'];
	
if(isset($new_module_left)){
	$modules_left_array[] = $default_module_left;}

	
/* If a module is given for centre, choose it */
if(isset($_REQUEST['m_c']))
	$m_c = $_REQUEST['m_c'];


if(!isset($m_c))
	$m_c = $default_m_c;
	

//FIXME: isset() og else-seksjon her + mange andre steder?
if(isset($_REQUEST['module_right']))
	$module_right = $_REQUEST['module_right'];

if(!isset($module_right))
	$module_right = $default_module_right;

if(isset($_REQUEST['module_right_2']))
	$module_right_2 = $_REQUEST['module_right_2'];

if(!isset($module_right_2))
	$module_right_2 = $default_module_right_2;

/* Look for page title in GET, if it's not there, check POST, then go by default */
/* default */
$page_title = $default_page_title;

if(isset($_REQUEST['page_title'])){
	$page_title .= " - ". $_REQUEST['page_title'];	
}	


module_login();




// FETCH URL TO STYLESHEET FROM DB IF USER IS LOGGED IN
if (isset($_SESSION['valid_user'])){
	global $css_file;
	$query = "SELECT url FROM stylesheets s, user u WHERE u.username ='". $_SESSION['valid_user'] . "' AND u.styleid = s.styleid;";
	$table = getArray($query);
	foreach ($table as $row) {
		$css_file = $row['url'];
	}		
}







module_html_header($page_title);

/* CHECK IF WE NEED TO STORE THE USER'S SCROLLPOSITION ON THE INDEX PAGE */
if(isset($_REQUEST['scroll'])){
	$_SESSION['scrollY'] = $_REQUEST['scroll'];
}else{
	$_SESSION['scrollY'] = 0;
}

/* PAGE BEGINS */

echo '<table>';
echo '<tr><td colspan=3>';
echo '<div class="topmenu">';
	
	if(isset($_SESSION['valid_admin'])){
		echo $adminmenu;
	}else if(isset($_SESSION['valid_user'])){
		if(isset($_SESSION['user_may_post'])){
			echo $usermenu;
		}else{
			echo $cusermenu;
		}
		
	}else{
		echo $freemenu;
	}
	
echo '</div></td></tr>'; 

echo '<tr class="main_row">';


echo '<td class="module_left">'; 

for($i=0; $i<count($modules_left_array); $i++){
	if(approved_func($modules_left_array[$i])){
		$modules_left_array[$i]();
	}else{
		echo $missing_function;	
	}
}


	
echo "</td>" ;


echo '<td class="module_center">';
if(approved_func($m_c)) {
	$m_c();
}else{
	echo $missing_function;
}	

echo "</td>";


echo '<td class="module_right">';
if(approved_func($module_right)) {
	$module_right();
}else{
	echo $missing_function;
}

if(isset($module_right_2) && approved_func($module_right_2)) {
	$module_right_2();
}else{
	echo $missing_function;
}


echo "</td></tr></table>";

do_html_footer();

?>


