<?php

function module_memberlist(){
	$query = "SELECT username, firstname, lastname, may_post FROM user WHERE username NOT LIKE 'admin' ORDER BY firstname, username";
	$result = DB_get_table($query);
	
	echo '<table class="default_table">';
	echo '<tr><td colspan=2><div class="default_header">Medlemmer</div></td></tr>';
	
	while($row = DB_next_row($result)){
		echo '<tr><td colspan=2><a href="index.php?m_c=mvp&amp;username=' . $row['username'] . '">';
		if(strlen($row['firstname']) > 1){
		echo $row['firstname'];

		}else{
		echo $row['username'];
			
			
		}
		echo '</a></td></tr>';		
		
	}
	
	
	echo '</table>';
	
	
	
	
	
}





?>