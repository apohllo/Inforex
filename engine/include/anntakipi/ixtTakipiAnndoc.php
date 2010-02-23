<?php
/* 
 * ---
 * 
 * ---
 * Created on 2010-01-13
 * Michał Marcińczuk <marcinczuk@gmail.com> [czuk.eu]
 */
 
class TakipiAnnotation{
	var $begin = null;
	var $end = null;
	var $name = null;
	
	function __construct($begin, $end, $name){
		$this->begin = $begin;
		$this->end = $end;
		$this->name = $name;
	}
	
	function to_string(){
		return sprintf("[%d,%d] %s", $this->begin, $this->end, $this->name);
	}
} 
 
class TakipiAnndoc{
	var $annotations = array();
	
	function add($begin, $end, $name){
		$this->annotations[] = new TakipiAnnotation($begin, $end, $name);
	}
	
	/**
	 * Remove all annotations which type is not present on a given list.
	 */
	function remove_other_than($types_to_keep = array()){
		$temp = array();
		foreach ($this->annotations as $an){
			if (in_array($an->name, $types_to_keep))
				$temp[] = $an;
		}
		$this->annotations = $temp;
	}
	
	/**
	 * Returns an array representing a document with IOB annotations.
	 * Example:
	 * Index  Value
	 *   1     B-LOC
	 *   2     I-LOC
	 *   3     O
	 *   4     B-LOC
	 */
	function get_sparce_vector($count){
		$sparse = array();
		for ($i=0; $i<$count; $i++)
			$sparse[$i] = "O";
		foreach ($this->annotations as $an){
			$name = strtoupper($an->name);
			for ($i=$an->begin; $i<=$an->end; $i++)
				$sparse[$i] = "I-{$name}";
			$sparse[$an->begin] = "B-{$name}";
		}
		return $sparse;
	}
}

?>
