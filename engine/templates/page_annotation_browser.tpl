{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header.tpl"}

<div id="annotation_stages_types" style="width: 250px; float: left; ">
    <div id="annotation_stages">
	    <h2>Annotation stage</h2>
	    <div>
	        <table class="tablesorter" cellspacing="1">
	        {assign var="last_set" value=""}
	        {foreach from=$annotation_stages item=stage}
	        <tr{if $stage.stage==$annotation_stage} class="selected"{/if}>
	            <td style="text-align: right; width: 50px">{$stage.count}</td>
	            <td><a href="index.php?corpus={$corpus.id}&amp;page=annotation_browser&amp;annotation_stage={$stage.stage}">{$stage.stage}</a></td>
	        </tr>
	        {/foreach}
	        </table>
	    </div>
    </div>
    {if $annotation_stage}
    <div id="annotation_types">
	    <h2>Annotation types</h2>
	    <div class="scroll" style="overflow: auto;height: 500px; ">
			<table class="tablesorter" cellspacing="1">
			{assign var="last_set" value=""}
			{foreach from=$annotation_types item=type}
			{if $last_set != $type.annotation_set_id}
			<tr class="annotation_set">
			    <td colspan="2">{$type.description}</td>
			    {assign var="last_set" value=$type.annotation_set_id}
			</tr>
			{/if}
			<tr{if $type.annotation_type_id==$annotation_type_id} class="selected"{/if}>
	            <td style="text-align: right; width: 50px">{$type.count}</td>
			    <td><a href="index.php?corpus={$corpus.id}&amp;page=annotation_browser&amp;annotation_stage={$stage.stage}&amp;annotation_type_id={$type.annotation_type_id}">{$type.name}</a></td>
			</tr>
			{/foreach}
			</table>
		</div>
	</div>
	{/if}
</div>

{if $annotation_stage && $annotation_type_id}
<div id="annotation_texts" style="width: 250px; float: left; margin-left: 10px;">
    <h2>Annotation orths</h2>
    <div id="annotation_orths" class="scroll" style="overflow: auto; height: 100px; ">
        <table class="tablesorter" cellspacing="1">
        {foreach from=$annotation_orths item=type}
        <tr{if $type.text==$annotation_orth} class="selected"{/if}>
            <td style="text-align: right">{$type.count}</td>
            <td><a href="index.php?corpus={$corpus.id}&amp;page=annotation_browser&amp;annotation_stage={$annotation_stage}&amp;annotation_type_id={$type.annotation_type_id}&amp;annotation_orth={$type.text}">{$type.text}</a></td>
        </tr>
        {/foreach}        
        </table>
        {if $annotation_orths|@count==0}
        <i>Choose annotation type.</i>
        {/if}
    </div>
    <h2>Annotation lemmas</h2>
    <div id="annotation_lemmas" class="scroll" style="overflow: auto; height: 100px; ">
        <table class="tablesorter" cellspacing="1">
        {foreach from=$annotation_lemmas item=type}
        <tr{if $type.text==$annotation_lemma} class="selected"{/if}>
            <td style="text-align: right">{$type.count}</td>
            <td><a href="index.php?corpus={$corpus.id}&amp;page=annotation_browser&amp;annotation_stage={$annotation_stage}&amp;annotation_type_id={$type.annotation_type_id}&amp;annotation_lemma={$type.text}">{$type.text}</a></td>
        </tr>
        {/foreach}        
        </table>
        {if $annotation_lemmas|@count==0 && $annotation_orths|@count>0}
        <i>Selected annotations do not have lemmas.</i>
        {/if}
    </div>
</div>
{/if}

{if $annotation_stage && $annotation_type_id && ($annotation_orth || $annotation_lemma) }
<div id="annotation_contexts" style="margin-left: 520px; width: 1100px;">
    <h2>Annotation contexts</h2>
    <div class="flexigrid">
        <table id="table-annotations">
          <tr>
              <td style="vertical-align: middle"><div>Loading ... <img style="vertical-align: baseline" title="" src="gfx/flag_4.png"></div></td>
          </tr>
        </table>
    </div>
</div>
{/if}

<br style="clear: both;"/>

{include file="inc_footer.tpl"}