<!-- THIS FILE IS AUTOGENERATED BY RUNNING configuratorGeneratorUtil > sms_configurator_table.tpl -->

<TR><td colspan="4">&nbsp;&nbsp;<TR><TD colspan="4"><h4>SMScountry Proxy Settings</h4></TD></tr>

<TR>

    <td nowrap width="10%" class="dataLabel">{$MOD.LBL_SMS_HOST}
      {if !empty($MOD.LBL_SMS_HOST_DESC)}
          [<a href="#" data-toggle="tooltip" title="{$MOD.LBL_SMS_HOST_DESC}">?</a>]:
      {/if}
    </td>
    <td width="25%" class="dataField">
    {if empty($config.sms_host )}
        {assign var='sms_host' value=$sms_config.sms_host}
    {else}
        {assign var='sms_host' value=$config.sms_host}
    {/if}
        <input type='textbox' name='sms_host' size="45" value='{$sms_host}'>
    </td>

    <td nowrap width="10%" class="dataLabel">{$MOD.LBL_SMS_PORT}
      {if !empty($MOD.LBL_SMS_PORT_DESC)}
          [<a href="#" data-toggle="tooltip" title="{$MOD.LBL_SMS_PORT_DESC}">?</a>]:
      {/if}
    </td>
    <td width="25%" class="dataField">
    {if empty($config.sms_port )}
        {assign var='sms_port' value=$sms_config.sms_port}
    {else}
        {assign var='sms_port' value=$config.sms_port}
    {/if}
        <input type='int' name='sms_port' size="45" value='{$sms_port}'>
    </td>