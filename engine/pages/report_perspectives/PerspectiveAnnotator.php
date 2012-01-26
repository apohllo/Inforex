<?php

class PerspectiveAnnotator extends CPerspective {
	
	function execute()
	{
		$this->set_panels();
		$this->set_annotation_menu();
		$this->set_relations();		
		$this->set_events();
		$this->set_annotations();
		
	}
	
	/**
	 * Set up twin panels.
	 */
	function set_panels()
	{
		$this->page->set('showRight', $_COOKIE['showRight']=="true"?true:false);
	}
	
	function set_annotation_menu()
	{
		global $mdb2;
		$sql = "SELECT t.*, s.description as `set`" .
				"	, ss.description AS subset" .
				"	, ss.annotation_subset_id AS subsetid" .
				"	, s.annotation_set_id AS groupid" .
				"	, ac.annotation_name AS common" .
				" FROM annotation_types t" .
				" JOIN annotation_sets_corpora c ON (t.group_id=c.annotation_set_id)" .
				" JOIN annotation_sets s ON (s.annotation_set_id = t.group_id)" .
				" LEFT JOIN annotation_types_common ac ON (t.name = ac.annotation_name)" .
				" LEFT JOIN annotation_subsets ss USING (annotation_subset_id)" .
				" WHERE c.corpus_id = {$this->document['corpora']}" .
				" ORDER BY `set`, subset, t.name";
		$select_annotation_types = new HTML_Select('annotation_type', 1, false, array("id"=>"annotation_type", "disabled"=>"true"));
		$select_annotation_types->loadQuery($mdb2, $sql, 'name', 'name', "");		
		$annotation_types = db_fetch_rows($sql);
		$annotationCss = "";
		$annotation_grouped = array();
		foreach ($annotation_types as $an){
			if ($an['css']!=null && $an['css']!="") $annotationCss = $annotationCss . "span." . $an['name'] . " {" . $an['css'] . "} \n"; 
			$set = $an['set'];
			$subset = $an['subset'] ? $an['subset'] : "none"; 
			if (!isset($annotation_grouped[$set])){
				$annotation_grouped[$set] = array();
				$annotation_grouped[$set]['groupid']=$an['groupid']; 
			}
			if (!isset($annotation_grouped[$set][$subset])){
				$annotation_grouped[$set][$subset] = array();
				$annotation_grouped[$set][$subset]['subsetid']=$an['subsetid'];
			}
			$annotation_grouped[$set][$subset][$an[name]] = $an;
		}
		$this->page->set('select_annotation_types', $select_annotation_types->toHtml());				
		$this->page->set('annotation_types', $annotation_grouped);
	}
	
	function set_relations(){
		$sql = 	"SELECT  relations.source_id, " .
						"relations.target_id, " .
						"relation_types.name, " .
						"rasrc.text source_text, " .
						"rasrc.type source_type, " .
						"radst.text target_text, " .
						"radst.type target_type " .
						"FROM relations " .
						"JOIN relation_types " .
							"ON (relations.relation_type_id=relation_types.id " .
							"AND relations.source_id IN " .
								"(SELECT ran.id " .
								"FROM reports_annotations ran " .
								"JOIN annotation_types aty " .
								"ON (ran.report_id={$this->page->id} " .
								"AND ran.type=aty.name " .
								"AND aty.group_id IN " .
									"(SELECT annotation_set_id " .
									"FROM annotation_sets_corpora  " .
									"WHERE corpus_id={$this->page->cid}) " .
								"))) " .
						"JOIN reports_annotations rasrc " .
							"ON (relations.source_id=rasrc.id) " .
						"JOIN reports_annotations radst " .
							"ON (relations.target_id=radst.id) " .
						"ORDER BY relation_types.name";		
		$allRelations = db_fetch_rows($sql);		
		$this->page->set('allrelations',$allRelations);
	}
	
	function set_events(){
		/*****obsluga zdarzeń********/
		//lista dostepnych grup zdarzen dla danego korpusu
		$sql = "SELECT DISTINCT event_groups.event_group_id, event_groups.name " .
				"FROM corpus_event_groups " .
				"JOIN event_groups " .
					"ON (corpus_event_groups.corpus_id={$this->page->cid} AND corpus_event_groups.event_group_id=event_groups.event_group_id) " .
				"JOIN event_types " .
					"ON (event_groups.event_group_id=event_types.event_group_id)";
		$event_groups = db_fetch_rows($sql);
		
		//lista zdarzen przypisanych do raportu
		$sql = "SELECT reports_events.report_event_id, " .
					  "event_groups.name AS groupname, " .
					  "event_types.name AS typename, " .
					  "event_types.event_type_id, " .
					  "count(reports_events_slots.report_event_slot_id) AS slots " .
					  "FROM reports_events " .
					  "JOIN reports " .
					  	"ON (reports_events.report_id={$this->page->id} " .
					  	"AND reports_events.report_event_id=reports.id) " .
				  	  "JOIN event_types " .
				  	  	"ON (reports_events.event_type_id=event_types.event_type_id) " .
			  	  	  "JOIN event_groups " .
			  	  	  	"ON (event_types.event_group_id=event_groups.event_group_id) " .
		  	  	  	  "LEFT JOIN reports_events_slots " .
		  	  	  	  	"ON (reports_events.report_event_id=reports_events_slots.report_event_id) " .
	  	  	  	  	  "GROUP BY (reports_events.report_event_id)";		
		$events = db_fetch_rows($sql);			
		$this->page->set('event_groups',$event_groups);
		$this->page->set('events',$events);
		
	}
	
	function set_annotations(){
		$subpage = $this->page->subpage;
		$id = $this->page->id;
		$cid = $this->page->cid;
		$row = $this->page->row;
		$sql = "SELECT id, type, `from`, `to`, `to`-`from` AS len, text, t.group_id, ans.description setname, ansub.description subsetname, ansub.annotation_subset_id, t.name typename, t.short_description typedesc, an.stage, t.css, an.source"  .
				" FROM reports_annotations an" .
				" LEFT JOIN annotation_types t ON (an.type=t.name)" .
				" LEFT JOIN annotation_subsets ansub ON (t.annotation_subset_id=ansub.annotation_subset_id)" .
				" LEFT JOIN annotation_sets ans on (t.group_id=ans.annotation_set_id)" .
				" WHERE report_id = {$row['id']} " .
				" AND ans.annotation_set_id IN" .
					"(SELECT annotation_set_id " .
					"FROM annotation_sets_corpora  " .
					"WHERE corpus_id=$cid)";
		$sql2 = $sql;
		$sql3 = $sql;
		
		if ($_COOKIE['clearedLayer'] && $_COOKIE['clearedLayer']!="{}"){
			$sql = $sql . " AND group_id " .
					"NOT IN (" . preg_replace("/\:1|id|\{|\}|\"|\\\/","",$_COOKIE['clearedLayer']) . ") " ;
			$sql2 = $sql; 
		} 
		if ($_COOKIE['clearedSublayer'] && $_COOKIE['clearedSublayer']!="{}"){
			$sql = $sql . " AND (ansub.annotation_subset_id " .
					"NOT IN (" . preg_replace("/\:1|id|\{|\}|\"|\\\/","",$_COOKIE['clearedSublayer']) . ") " .
							"OR ansub.annotation_subset_id IS NULL) ";
			$sql2 = $sql; 
		} 
		
		if ($_COOKIE['rightSublayer'] && $_COOKIE['rightSublayer']!="{}"){
			$sql = $sql . " AND ansub.annotation_subset_id " .
					"NOT IN (" . preg_replace("/\:1|id|\{|\}|\"|\\\/","",$_COOKIE['rightSublayer']) . ") " ;
			$sql2 = $sql2 . " AND (ansub.annotation_subset_id " .
					"IN (" . preg_replace("/\:1|id|\{|\}|\"|\\\/","",$_COOKIE['rightSublayer']) . ") " .
							"OR ansub.annotation_subset_id IS NULL) ";
					
		} 
		else {
			$sql2 = $sql2 . " AND ansub.annotation_subset_id=0 "; 
		}
		$sql = $sql . " ORDER BY `from` ASC, `level` DESC"; 
		$sql2 = $sql2 . " ORDER BY `from` ASC, `level` DESC"; 
			
		
		$anns = db_fetch_rows($sql);
		$anns2 = db_fetch_rows($sql2);
		$anns3 = db_fetch_rows($sql3);
		$annotation_set_map = array();
		foreach ($anns3 as $as){
			$setName = $as['setname'];
			$subsetName = $as['subsetname']==NULL ? "!uncategorized" : $as['subsetname'];
			$anntype = $as['typename'];
			if ($annotation_set_map[$setName][$subsetName][$anntype]==NULL){
				$annotation_set_map[$setName][$subsetName]['subsetid'] = $as['annotation_subset_id'];
				$annotation_set_map[$setName][$subsetName][$anntype] = array();
				$annotation_set_map[$setName][$subsetName][$anntype]['description']=$as['typedesc'];
				$annotation_set_map[$setName]['groupid']=$as['group_id'];
			}
			array_push($annotation_set_map[$setName][$subsetName][$anntype], $as);
		}

		$exceptions = array();
		$content = str_replace("\n", "\n ", $row['content']);
		
		$htmlStr =  new HtmlStr($content, true);
		$htmlStr2 = new HtmlStr($content, true);

		foreach ($anns as $ann){
			try{
				if ($ann['stage']=="final" ){
					$htmlStr->insertTag($ann['from'], sprintf("<an#%d:%s:%d:%d>", $ann['id'], $ann['type'], $ann['group_id'], $ann['annotation_subset_id']), $ann['to']+1, "</an>");
				}					
			}
			catch (Exception $ex){
				try{
					$exceptions[] = sprintf("Annotation could not be displayed due to invalid border [%d,%d,%s]", $ann['from'], $ann['to'], $ann['text']);
					if ($ann['from'] == $ann['to']){
						$htmlStr->insertTag($ann['from'], "<b class='invalid_border_one' title='{$ann['from']}'>", $ann['from']+1, "</b>");
					}
					else{				
						$htmlStr->insertTag($ann['from'], "<b class='invalid_border_start' title='{$ann['from']}'>", $ann['from']+1, "</b>");
					}
				}
				catch (Exception $ex2){
					fb($ex2);				
				}				
			}
		}
		
		foreach ($anns2 as $ann){
			try{
				if ($ann['stage']!="discarded")
					$htmlStr2->insertTag($ann['from'], sprintf("<an#%d:%s:%d:%d>", $ann['id'], $ann['type'], $ann['group_id'], $ann['annotation_subset_id']), $ann['to']+1, "</an>");					
			}
			catch (Exception $ex){
				try{
					$exceptions[] = sprintf("Annotation could not be displayed due to invalid border [%d,%d,%s]", $ann['from'], $ann['to'], $ann['text']);
					if ($ann['from'] == $ann['to']){
						$htmlStr2->insertTag($ann['from'], "<b class='invalid_border_one' title='{$ann['from']}'>", $ann['from']+1, "</b>");
					}
					else{				
						$htmlStr2->insertTag($ann['from'], "<b class='invalid_border_start' title='{$ann['from']}'>", $ann['from']+1, "</b>");
					}
				}
				catch (Exception $ex2){
					fb($ex2);				
				}				
			}
		}
		
		//obsluga tokenow	 
		$sql = "SELECT `from`, `to`, `eos`" .
				" FROM tokens " .
				" WHERE report_id={$id}" .
				" ORDER BY `from` ASC";		
		$tokens = db_fetch_rows($sql);
		
		foreach ($tokens as $ann){
			try{
				$htmlStr->insertTag((int)$ann['from'], sprintf("<an#%d:%s:%d>", 0, "token" . ($ann['eos'] ? " eos" : ""), 0), $ann['to']+1, "</an>", true);
				
				if ($subpage=="annotator"){
					$htmlStr2->insertTag((int)$ann['from'], sprintf("<an#%d:%s:%d>", 0, "token" . ($ann['eos'] ? " eos" : ""), 0), $ann['to']+1, "</an>", true);
				}						
			}
			catch (Exception $ex){
				fb($ex);	
			}
		}
		
		$sql_relations = "SELECT an.*, at.group_id, r.target_id, t.name" .
							" FROM relations r" .
							" JOIN reports_annotations an ON (r.source_id=an.id)" .
							" JOIN relation_types t ON (r.relation_type_id=t.id)" .
							" JOIN annotation_types at ON (an.type=at.name)" .
							" WHERE an.report_id = ?" .
							"   AND t.relation_set_id=2 " . // 1-Syntactic relations; 2-Semantic relations; 3-Anaphora
							" ORDER BY an.to ASC";
		$relations = db_fetch_rows($sql_relations, array($id));
		
		foreach ($relations as $r)
			$htmlStr->insert($r[to]+1, "<sup class='rel' title='".$r['name']."' target='".$r['target_id']."'/></sup>", false, true, false);

		if ( count($exceptions) > 0 )
			$this->page->set("exceptions", $exceptions);
		
		$this->page->set('sets', $annotation_set_map);
		$this->page->set('content_inline', Reformat::xmlToHtml($htmlStr->getContent()));
		$this->page->set('content_inline2', Reformat::xmlToHtml($htmlStr2->getContent()));
		$this->page->set('anns',$anns);
	}	
}

?>