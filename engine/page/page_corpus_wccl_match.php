<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_corpus_wccl_match extends CPageCorpus{

    function __construct(){
        parent::__construct();
        $this->anyCorpusRole[] = CORPUS_ROLE_WCCL_MATCH;
    }

	function execute(){
		global $corpus, $user, $db;
				
		$rules = $db->fetch("SELECT * FROM wccl_rules WHERE user_id = ? AND corpus_id = ?",
			array($user['user_id'], $corpus['id']));

		$this->set("rules", $rules['rules']);				
		$this->set("annotations", $rules['annotations']);
	}
}
