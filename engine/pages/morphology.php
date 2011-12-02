<?php
class Page_morphology extends CPage{
	
	function execute(){
		
		$ctag = $_GET['ctag'];
		$subcorpus = $_GET['subcorpus'];
		
		$corpus_id = 3;
				
		$this->set("ctag", $ctag);
		$this->set("subcorpus", $subcorpus);
		$this->set("words", DbCorpusStats::getWordsFrequnces($corpus_id, $subcorpus, $ctag=="ign", true));
		$this->set("subcorpora", DbCorpus::getCorpusSubcorpora($corpus_id));		
	}
	

}
 
?>