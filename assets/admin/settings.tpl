<div class="container">
    <section class="mini-layout">
        <div class="frame_title clearfix">
            <div class="pull-left">
                <span class="help-inline"></span>
                <span class="title">{lang('Convead Integration Module', 'convead')}</span>
            </div>
            <div class="pull-right">
                <div class="d-i_b">
                    <button type="button" class="btn btn-small btn-primary action_on formSubmit" data-form="#settings_form"><i class="icon-ok"></i>{lang('Save', 'convead')}</button>
                </div>
            </div>                            
        </div>
        <form id="settings_form" action="/admin/components/cp/convead/save" method="post" class="m-t_10">
            <table class="table  table-bordered table-hover table-condensed content_big_td">
                <thead>
                    <tr>
                        <th colspan="6">
                            {lang('Settings Convead', 'convead')}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="6">
                            <div class="inside_padd" >
                                <div class="control-group" >
                                    <div class="controls">
                                        <input type="checkbox" name="enabled" {if $enabled == '1'}checked{/if} /> 
                                        {lang('Enable module', 'convead')}
                                    </div>
                                    <br />
                                    
                                    <label class="control-label">{lang('Convead App Key', 'convead')}:</label>
                                    <div class="controls"><input type="text" name="app_key" value="{$app_key}" /></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
    </section>                                            
</div>
