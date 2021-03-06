<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class DbCtag{


	static function saveIfNotExists($ctag, $tagset_id){
		global $db;
		$ctag_id = DbCtag::getCtagId($ctag, $tagset_id);
		if($ctag_id) return $ctag_id;

		$sql = "INSERT INTO tokens_tags_ctags(`ctag`, `tagset_id`) VALUES(?, ?);";
		$db->execute($sql, array($ctag, $tagset_id));
		return $db->last_id();
	}

	static function getCtagId($ctag, $tagset_id){
		global $db;
		$sql = "SELECT id FROM tokens_tags_ctags WHERE ctag = ? AND tagset_id = ?";
		return $db->fetch_one($sql, array($ctag, $tagset_id));
	}

	/**
	 * Usuwa Ctagi(tagi morfologiczne) nie przypisane do żadnego tagu
	 */
	static function clean(){
		global $db;
		$sql = "DELETE ctag.* FROM tokens_tags_ctags ctag ".
				" LEFT JOIN tokens_tags_optimized tto ON(ctag.id = tto.ctag_id) ".
				" WHERE tto.ctag_id IS NULL";
		$db->execute($sql);
	}
	
}

?>