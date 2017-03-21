<?php
require_once 'modules/smsad_SMS/vendor/autoload.php';
include_once('modules/smsad_SMS/src/Controllers/MessagesController.php');
include_once('modules/smsad_SMS/src/Configuration.php');
include_once('modules/smsad_SMS/src/Models/Message.php');
include_once('modules/smsad_SMS/src/APIHelper.php');
include_once('modules/smsad_SMS/src/APIException.php');

use SMSCountryMessagingLib\Controllers\MessagesController;
use SMSCountryMessagingLib\Models\Message;

require_once 'modules/Configurator/Configurator.php';
$configurator = new Configurator();
$configurator->loadConfig();
if(isset($configurator->config['sms_user']) && isset($configurator->config['sms_secret']) && isset($configurator->config['sms_host']))
{
	$sms_user = $configurator->config['sms_user'];
	$sms_secret = $configurator->config['sms_secret'];
	$sms_host = $configurator->config['sms_host'];

	$controller = new MessagesController($sms_user, $sms_secret, $sms_host);
	$response = $controller->getSenderId();
	$dropdown = '<select name="from_sms" tabindex="0" id="from_sms">';
	foreach($response->SenderIds as $SenderIds)
	{
		$dropdown .= '<option value="'.$SenderIds->SenderId.'">	' . $SenderIds->SenderId . ' </option>';
	}
	$dropdown .= '</select>';
}

$module_name = 'smsad_SMS';
$viewdefs [$module_name] = 
array (
  'QuickCreate' => 
  array (
    'templateMeta' => 
    array (
      'javascript' => '<script type="text/javascript" src="https://www.google.com/jsapi"></script>',
      'includes' => 
      array (
        0 => 
        array (
          'file' => 'custom/modules/smsad_SMS/javascript/quickcreatesmspopup.js',
        ),
      ),
      'maxColumns' => '2',
      'widths' => 
      array (
        0 => 
        array (
          'label' => '10',
          'field' => '30',
        ),
        1 => 
        array (
          'label' => '10',
          'field' => '30',
        ),
      ),
      'useTabs' => false,
      'tabDefs' => 
      array (
        'DEFAULT' => 
        array (
          'newTab' => false,
          'panelDefault' => 'expanded',
        ),
      ),
    ),
    'panels' => 
    array (
      'default' => 
      array (
        0 => 
        array (
          0 => 
          array (
            'name' => 'parent_name',
            'studio' => 'visible',
            'label' => 'LBL_FLEX_RELATE',
          ),
          1 => 'name',
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'to_sms',
            'label' => 'LBL_TO_SMS',
          ),
        ),
        2 => 
        array (
          0 => 
          array (
            'name' => 'chksmslanguage_translation_c',
            'label' => 'LBL_CHKSMSLANGUAGE_TRANSLATION',
          ),
          1 => 
          array (
            'name' => 'sms_language_c',
            'studio' => 'visible',
            'label' => 'LBL_SMS_LANGUAGE',
          ),
        ),
        3 => 
        array (
          0 => 
          array (
            'name' => 'description',
            'comment' => 'Full text of the note',
            'studio' => 'visible',
            'label' => 'LBL_DESCRIPTION',
          ),
          1 => 
          array (
            'name' => 'from_sms',
            'label' => 'LBL_FROM_SMS',
			'customCode' => $dropdown,
          ),
        ),
        4 => 
        array (
          0 => 
          array (
            'name' => 'smsad_sms_template_smsad_sms_name',
            'label' => 'Load From SMS Template',
            'displayParams' => 
            array (
              'field_to_name_array' => 
              array (
                'id' => 'smsad_sms_template_smsad_smssmsad_sms_template_ida',
                'name' => 'smsad_sms_template_smsad_sms_name',
                'description' => 'description',
              ),
            ),
          ),
          1 => '',
        ),
      ),
    ),
  ),
);
?>
