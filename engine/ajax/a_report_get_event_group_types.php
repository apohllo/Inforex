<?php
/**
 * metoda pobierajaca typy zdarzeń dla podanej grupy
 * 
 */
class Ajax_report_get_event_group_types extends CPage {
	var $isSecure = false;
	function execute(){
		global $mdb2, $user;
		$group_id = intval($_POST['group_id']);

		$sql = "SELECT event_types.event_type_id, event_types.name " .
				"FROM event_groups " .
				"JOIN event_types " .
					"ON (event_groups.event_group_id={$group_id} AND event_groups.event_group_id=event_types.event_group_id)";
		$result = db_fetch_rows($sql);
		echo json_encode($result);
	}
	
}
?>