<?php
class Ajax_sens_edit_update_word extends CPage {
	function execute(){
		global $db;
		
		$new_name = $_POST['newwordname'];
		$wsd_new_name = "wsd_" . $new_name;
		$old_name = $_POST['oldwordname'];
		$wsd_old_name = "wsd_" . $old_name;
		$sql = " SELECT id FROM annotation_types_attributes WHERE annotation_type='" . $wsd_old_name . "' ";
		$wsd_id = $db->fetch_one($sql);

		$sql = " UPDATE annotation_types SET name='" . $wsd_new_name . "' WHERE name='" . $wsd_old_name . "' ";
		
		$db->execute($sql);	
		
		$sql = " SELECT value FROM annotation_types_attributes_enum WHERE annotation_type_attribute_id='" . $wsd_id . "' "; 
		$rows = $db->fetch_rows($sql);
		$old_name_length = strlen($old_name);
		foreach($rows as $row){
			$old_sens_name = $row['value'];
			$sens_num = substr($old_sens_name,$old_name_length);
			$new_sens_name = $new_name . $sens_num;
			
			$sql = " UPDATE annotation_types_attributes_enum SET value='" . 
					$new_sens_name . 
					"' WHERE value='" . 
					$old_sens_name . "' ";
		
			$db->execute($sql);
		}
			
		echo json_encode(array("success" => 1, "sens_num" => $wsd_id));
	}	
}
?>