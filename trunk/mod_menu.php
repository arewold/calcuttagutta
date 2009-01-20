<?php

/*
 * Created on 20.sep.2005
 *
 */

function module_menu() {
	echo '<div class="menu">';
	echo '<div class="menu_main_header">Meny</div>';
	menu(-1, 0);
	echo '</div>';
}

function menu($menu_root, $level) {
	$indent = ">";
	
	if ($menu_root == -1) {
		$query = "SELECT m.menuid, m.title, m.articleid, s.antall FROM menu m left join (SELECT parentid, count(*) AS antall FROM menu GROUP BY parentid) s ON (m.menuid = s.parentid) WHERE m.parentid IS NULL ORDER BY m.priority, m.title";
	} else {
		$query = "SELECT m.menuid, m.title, m.articleid, s.antall FROM menu m left join (SELECT parentid, count(*) AS antall FROM menu GROUP BY parentid) s ON (m.menuid = s.parentid) WHERE m.parentid = " . $menu_root . " ORDER BY m.priority, m.title";
	}

	$result = DB_get_table($query);

	while($row = DB_next_row($result)){
		// hvis den ikke referer til en artikkel, behandles den som en overskrift
		if ($row['articleid'] == '') {
			echo '<div class="menu_header">' . str_repeat($indent, $level) .  $row['title'] . '</div>';
		}
		else {
			echo '<a href="index.php?m_c=m_va&articleid=' . $row['articleid'] . '">' . str_repeat($indent, $level) . $row['title'] . '</a><br />';
		}
			// Går ned ett hakk:
			echo '<div class="menu_sub_header">';
			menu($row['menuid'], $level+1);
			echo '</div>';

	}
}
?>
