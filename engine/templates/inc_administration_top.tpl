<div id="tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all" style="background: #f3f3f3; margin-bottom: 5px; position: relative">
    <ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
        {if "admin"|has_role}
            <li class="ui-state-default ui-corner-top {if $page=="annotation_edit"}ui-state-active ui-tabs-selected{/if}">
                <a href="index.php?page=annotation_edit">Annotations</a>
            </li>
            <li class="ui-state-default ui-corner-top {if $page=="relation_edit"}ui-state-active ui-tabs-selected{/if}">
                <a href="index.php?page=relation_edit">Relations</a>
            </li>
            <li class="ui-state-default ui-corner-top {if $page=="event_edit"}ui-state-active ui-tabs-selected{/if}">
                <a href="index.php?page=event_edit">Events</a>
            </li>
            <li class="ui-state-default ui-corner-top {if $page=="sens_edit"}ui-state-active ui-tabs-selected{/if}">
                <a href="index.php?page=sens_edit">WSD senses</a>
            </li>
            <li class="ui-state-default ui-corner-top {if $page=="user_admin"}ui-state-active ui-tabs-selected{/if}">
                <a href="index.php?page=user_admin">Users</a>
            </li>

    {*  <li{if $page=="user_activities"} class="active"{/if}><a href="index.php?page=user_activities">Activities</a></li> *}

        {/if}
    </ul>

    <div>