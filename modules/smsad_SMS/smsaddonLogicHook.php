<?php

// custom/modules/smsad_sms/smsad_smsLogicHook.php

if (!defined('sugarEntry') || !sugarEntry)
	die('Not A Valid Entry Point');
//ini_set('display_errors', '1');

require_once 'modules/smsad_SMS/vendor/autoload.php';
include_once('modules/smsad_SMS/src/Controllers/MessagesController.php');
include_once('modules/smsad_SMS/src/Configuration.php');
include_once('modules/smsad_SMS/src/Models/Message.php');
include_once('modules/smsad_SMS/src/APIHelper.php');
include_once('modules/smsad_SMS/src/APIException.php');

use SMSCountryMessagingLib\Controllers\MessagesController;
use SMSCountryMessagingLib\Models\Message;

class smsad_smsLogicHook {

	function __construct() {
		
	}

	/**
	 * @deprecated deprecated since version 7.6, PHP4 Style Constructors are deprecated and will be remove in 7.8, please update your code, use __construct instead
	 */
	function ProjectJjwg_MapsLogicHook() {
		$deprecatedMessage = 'PHP4 Style Constructors are deprecated and will be remove in 7.8, please update your code';
		if (isset($GLOBALS['log'])) {
			$GLOBALS['log']->deprecated($deprecatedMessage);
		} else {
			trigger_error($deprecatedMessage, E_USER_DEPRECATED);
		}
		self::__construct();
	}

	function beforeSendSMS(&$bean, $event, $arguments) {
		global $db;
		try { 
			
			if(isset($_REQUEST['from_sms']))
				$bean->from_sms = $_REQUEST['from_sms'];
			if(trim($bean->from_sms)=='')
			{
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
					foreach($response->SenderIds as $SenderIds)
					{
						$bean->from_sms = $SenderIds->SenderId;
					}
				}
			}
			if(trim($bean->from_sms) == '')
				throw new exception("No Sender Selected");

			$bean->name = "SMS-OUT";
			if(isset($_REQUEST['message_sms']))
				$bean->description = $_REQUEST['message_sms'];
			$parent_ids = explode(',',$bean->parent_id); //Bala
			$callRecordId = $this->createCallId();
			foreach($parent_ids as $parent_id)      { //Bala
				if(strtolower($bean->parent_type)=="accounts")
				{
					$beanArr = array(
						'Accounts' => $parent_id
					);
				}
				if(strtolower($bean->parent_type)=="leads")
				{
					$beanArr = array(
						'Leads' => $parent_id
					);
				}
				if(strtolower($bean->parent_type)=="contacts")
				{
					$beanArr = array(
						'Contacts' => $parent_id
					);
				}
				
				if(isset($bean->smsad_sms_template_smsad_smssmsad_sms_template_ida) && trim($bean->smsad_sms_template_smsad_smssmsad_sms_template_ida)!='')
				{
					$lbn_lbl_smstemplateId = $bean->smsad_sms_template_smsad_smssmsad_sms_template_ida;
					$smsad_sms_template = BeanFactory::getBean("smsad_sms_template", $lbn_lbl_smstemplateId);
					if($smsad_sms_template)
					{
						$smsad_sms_template->description = $bean->description;
						$bean->description = $smsad_sms_template->parse_template(!empty($bean->description) ? $bean->description : $smsad_sms_template->description, $beanArr);
					}
				}
				else
					$bean->description = $bean->description;
			}
			if( $_REQUEST['directmessage_sms']=='Direct')
			{
				$uidArr = explode(',',$_REQUEST['uid']); 
				foreach($uidArr as $key => $uid)
				{
					$query = "SELECT * FROM smsad_sms WHERE id='".$uid."'";
					$results = $GLOBALS['db']->query($query, true, "Error updating tasks entry: ");
					if($results){
						$beans1 = $GLOBALS['db']->fetchByAssoc($results);
						$bean->to_sms = $beans1['to_sms'];
						$bean->parent_type = $beans1['parent_type'];
						$bean->parent_id = $beans1['parent_id'];
						
						if($key <(count($uidArr)-1))
						{
							$callRecordId = $this->createCallId();
							$query = sprintf("INSERT INTO smsad_sms (id, name, date_entered, date_modified, modified_user_id, created_by, description, deleted, assigned_user_id, to_sms, from_sms, parent_type, parent_id) VALUES('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')", $callRecordId, $bean->name, $bean->date_entered, $bean->date_modified, $bean->modified_user_id, $bean->created_by, $bean->description, $bean->deleted, $bean->assigned_user_id, $bean->to_sms, $bean->from_sms, $bean->parent_type, $bean->parent_id);
							$GLOBALS['db']->query($query, true, "Error updating tasks entry: ");

							$callRecordId1 = $this->createCallId();
							if(strtolower($bean->parent_type)=="accounts")
							{
								$query = sprintf("INSERT INTO smsad_sms_accounts_1_c (id, date_modified, smsad_sms_accounts_1smsad_sms_ida, smsad_sms_accounts_1accounts_idb) VALUES('%s','%s','%s','%s')", $callRecordId1, date("Y-m-d H:i:s"), $callRecordId, $bean->parent_id);
							}
							if(strtolower($bean->parent_type)=="leads")
							{
								$query = sprintf("INSERT INTO smsad_sms_leads_1_c (id, date_modified, smsad_sms_leads_1smsad_sms_ida, smsad_sms_leads_1leads_idb) VALUES('%s','%s','%s','%s')", $callRecordId1, date("Y-m-d H:i:s"), $callRecordId, $bean->parent_id);
							}
							if(strtolower($bean->parent_type)=="contacts")
							{
								$query = sprintf("INSERT INTO smsad_sms_contacts_1_c (id, date_modified, smsad_sms_contacts_1smsad_sms_ida, smsad_sms_contacts_1contacts_idb) VALUES('%s','%s','%s','%s')", $callRecordId1, date("Y-m-d H:i:s"), $callRecordId, $bean->parent_id);
							}
							if(trim($bean->parent_type)!="")
							{
								$GLOBALS['db']->query($query, true, "Error updating tasks entry: ");
							}
							$_REQUEST["mulbeanid"][] = $callRecordId;
							$_REQUEST["mulbeanidnumber"][] = $bean->to_sms;
						}
						else{
							$_REQUEST["mulbeanid"][] = $bean->id;
							$_REQUEST["mulbeanidnumber"][] = $bean->to_sms;
							$query = "Update smsad_sms set to_sms='".$bean->to_sms."', from_sms='" . $bean->from_sms . "' where id='".$bean->id."'";
							$GLOBALS['db']->query($query, true, "Error updating tasks entry: ");
						}
						require_once('modules/Notes/Note.php');
						$note = new Note();
						$note->mode = 'create';
						$note->description = $bean->description;
						$note->parent_id = $parent_id; //Bala
						$note->parent_type = $bean->parent_type;
						$note->name = 'SMS-OUT';
						$note->save();
					}					
				}
			}
			else if( $_REQUEST['directmessage_sms']=='Direct1')
			{
				$uidArr = explode(',',$_REQUEST['uid']); 
				$module = $_REQUEST['return_module'];
				foreach($uidArr as $key => $uid)
				{
					if($module == 'Leads');
						$results = $GLOBALS['db']->query("select phone_mobile,first_name,last_name from leads where id='".$uid."'");
					if($module == 'Contacts')
						$results = $GLOBALS['db']->query("select phone_mobile,first_name,last_name from contacts where id='".$uid."'");
					if($module == 'Accounts')
						$results = $GLOBALS['db']->query("select phone_office,name from accounts where id='".$uid."'");
					if($results){
						$beans1 = $GLOBALS['db']->fetchByAssoc($results);
						if($module == 'Accounts')	{
							$bean->to_sms = $beans1['phone_work'];
						}
						else{
							$bean->to_sms = $beans1['phone_mobile'];
						}
						$bean->parent_type = $module;
						$bean->parent_id = $uid;

						if($key <(count($uidArr)-1))
						{
							$callRecordId = $this->createCallId();
							$query = sprintf("INSERT INTO smsad_sms (id, name, date_entered, date_modified, modified_user_id, created_by, description, deleted, assigned_user_id, to_sms, from_sms, parent_type, parent_id) VALUES('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')", $callRecordId, $bean->name, $bean->date_entered, $bean->date_modified, $bean->modified_user_id, $bean->created_by, $bean->description, $bean->deleted, $bean->assigned_user_id, $bean->to_sms, $bean->from_sms, $bean->parent_type, $bean->parent_id);
							$GLOBALS['db']->query($query, true, "Error updating tasks entry: ");

							$callRecordId1 = $this->createCallId();
							if(strtolower($bean->parent_type)=="accounts")
							{
								$query = sprintf("INSERT INTO smsad_sms_accounts_1_c (id, date_modified, smsad_sms_accounts_1smsad_sms_ida, smsad_sms_accounts_1accounts_idb) VALUES('%s','%s','%s','%s')", $callRecordId1, date("Y-m-d H:i:s"), $callRecordId, $bean->parent_id);
							}
							if(strtolower($bean->parent_type)=="leads")
							{
								$query = sprintf("INSERT INTO smsad_sms_leads_1_c (id, date_modified, smsad_sms_leads_1smsad_sms_ida, smsad_sms_leads_1leads_idb) VALUES('%s','%s','%s','%s')", $callRecordId1, date("Y-m-d H:i:s"), $callRecordId, $bean->parent_id);
							}
							if(strtolower($bean->parent_type)=="contacts")
							{
								$query = sprintf("INSERT INTO smsad_sms_contacts_1_c (id, date_modified, smsad_sms_contacts_1smsad_sms_ida, smsad_sms_contacts_1contacts_idb) VALUES('%s','%s','%s','%s')", $callRecordId1, date("Y-m-d H:i:s"), $callRecordId, $bean->parent_id);
							}
							if(trim($bean->parent_type)!="")
							{
								$GLOBALS['db']->query($query, true, "Error updating tasks entry: ");
							}
							$_REQUEST["mulbeanid"][] = $callRecordId;
							$_REQUEST["mulbeanidnumber"][] = $bean->to_sms;
						}
						else{
							$_REQUEST["mulbeanid"][] = $bean->id;
							$_REQUEST["mulbeanidnumber"][] = $bean->to_sms;
							$query = "Update smsad_sms set to_sms='".$bean->to_sms."', from_sms='" . $bean->from_sms . "' where id='".$bean->id."'";
							$GLOBALS['db']->query($query, true, "Error updating tasks entry: ");
						}
						require_once('modules/Notes/Note.php');
						$note = new Note();
						$note->mode = 'create';
						$note->description = $bean->description;
						$note->parent_id = $parent_id; //Bala
						$note->parent_type = $bean->parent_type;
						$note->name = 'SMS-OUT';
						$note->save();
					}					
				}
			}
			else
			{
				$query2 = "UPDATE smsad_sms SET from_sms='" . $bean->from_sms . "' WHERE  id='" . $bean->id . "'";
				$result2 = $GLOBALS['db']->query($query2, true, "Error updating tasks entry: ");

				$query = "SELECT * FROM smsad_sms WHERE id='".$bean->id."'";
				$results = $GLOBALS['db']->query($query, true, "Error updating tasks entry: ");
				if($results){
					$beans1 = $GLOBALS['db']->fetchByAssoc($results);
					$to_smsarr[] = $beans1['to_sms'];
					$bean->parent_type = $beans1['parent_type'];
					$bean->parent_id = $beans1['parent_id'];
				}
				$parent_ids = explode(',',$bean->parent_id); //Bala
				$callRecordId = $this->createCallId();
				foreach($parent_ids as $parent_id)      { //Bala
					if(strtolower($bean->parent_type)=="accounts")
					{
						$beanArr = array(
							'Accounts' => $parent_id
						);
						$query = sprintf("INSERT INTO smsad_sms_accounts_1_c (id, date_modified, smsad_sms_accounts_1smsad_sms_ida, smsad_sms_accounts_1accounts_idb) VALUES('%s','%s','%s','%s')", $callRecordId, date("Y-m-d H:i:s"), $bean->id, $parent_id);
					}
					if(strtolower($bean->parent_type)=="leads")
					{
						$beanArr = array(
							'Leads' => $parent_id
						);
						$query = sprintf("INSERT INTO smsad_sms_leads_1_c (id, date_modified, smsad_sms_leads_1smsad_sms_ida, smsad_sms_leads_1leads_idb) VALUES('%s','%s','%s','%s')", $callRecordId, date("Y-m-d H:i:s"), $bean->id, $parent_id);
					}
					if(strtolower($bean->parent_type)=="contacts")
					{
						$beanArr = array(
							'Contacts' => $parent_id
						);
						$query = sprintf("INSERT INTO smsad_sms_contacts_1_c (id, date_modified, smsad_sms_contacts_1smsad_sms_ida, smsad_sms_contacts_1contacts_idb) VALUES('%s','%s','%s','%s')", $callRecordId, date("Y-m-d H:i:s"), $bean->id, $parent_id);
					}
					$GLOBALS['db']->query($query, true, "Error updating tasks entry: ");
					
					if(trim($bean->smsad_sms_template_smsad_smssmsad_sms_template_ida)!='')
					{
						$lbn_lbl_smstemplateId = $bean->smsad_sms_template_smsad_smssmsad_sms_template_ida;
						$smsad_sms_template = BeanFactory::getBean("smsad_sms_template", $lbn_lbl_smstemplateId);
						if($smsad_sms_template)
						{
							$bean->description = $smsad_sms_template->parse_template(!empty($bean->message_sms) ? $bean->message_sms : $smsad_sms_template->description, $beanArr);
						}
					}
					else
						$bean->description = $bean->description;
				}
				require_once('modules/Notes/Note.php');
				$note = new Note();
				$note->mode = 'create';
				$note->description = $bean->description;
				$note->parent_id = $parent_id; //Bala
				$note->parent_type = $bean->parent_type;
				$note->name = 'SMS-OUT';
				$note->save();
			}
			return true;
		}catch (\SMSCountryMessagingLib\APIException $e) {
			SugarApplication::appendErrorMessage("Error2 - " . strval($e->getResponseCode()) . ' ' . $e->getMessage());
			$url = $_SERVER['REQUEST_URI'];
			header('Location: '.$url.'?module='.$_REQUEST['module'].'&action=index');
			exit;
		}
		catch(Exception $e)
		{
			$url = $_SERVER['REQUEST_URI'];
			SugarApplication::appendErrorMessage($e->getMessage());
			header('Location: '.$url.'?module='.$_REQUEST['module'].'&action=index');
			exit;
		}
	}

	function sendSMS(&$bean, $event, $arguments) {
		global $db;
		try { 
			if(isset($_REQUEST["mulbeanid"]))
			{
				$number = implode(',', $_REQUEST["mulbeanidnumber"]);
				$number = $_REQUEST["mulbeanidnumber"];
				//foreach($_REQUEST["mulbeanid"] as $beanid)
				//{
					//$bean->id = $beanid;
					$this->MultisendSMS($bean, $event, $arguments, $number);
				//}
			}
			else
			{
				$this->MultisendSMS($bean, $event, $arguments);
			}
			return true;
		} catch (\SMSCountryMessagingLib\APIException $e) {
			SugarApplication::appendErrorMessage("Error - " . strval($e->getResponseCode()) . ' ' . $e->getMessage());
			return true;
		}
    }

	function MultisendSMS(&$bean, $event, $arguments, $number='') {
		global $db;
		
		try {
			$to_smsarr = array();			
			require_once 'modules/Configurator/Configurator.php';
			$configurator = new Configurator();
			$configurator->loadConfig(); 
			$sms_user = $configurator->config['sms_user'];
			$sms_secret = $configurator->config['sms_secret'];
			$sms_host = $configurator->config['sms_host'];

			$controller = new MessagesController($sms_user, $sms_secret, $sms_host);
			if(is_array($number) && count($number)>1)
			{
				try{
					if(trim($bean->from_sms) == '')
						throw new exception("No Sender Selected");
					if(trim($bean->description)== '')
						throw new exception("Message Should not be Empty.");
					$from_number = $bean->from_sms;
					$message = new Message($number, $from_number, $bean->description);
					
					// Send the message
					$response = $controller->createbulkMessage($message);

					// get the mdr id from the response
					$mdr_id1 = $response->MessageUUIDs;
					$Message = $response->Message;
					
					foreach($_REQUEST["mulbeanid"] as $key => $bean_id )
					{
						try{
							$mdr_id = $mdr_id1[$key];
							$query2 = "UPDATE smsad_sms SET message_id='" . $mdr_id . "', message_response ='".addslashes(json_encode($response))."' WHERE  id='" . $bean_id . "';";
							$result2 = $GLOBALS['db']->query($query2, true, "Error updating tasks entry: ");
							$query = "SELECT COUNT(*) AS total FROM smsad_sms_cstm WHERE id_c='".$bean_id."'";
							$results = $GLOBALS['db']->query($query, true, "Error updating tasks entry: ");
							$beans1 = $GLOBALS['db']->fetchByAssoc($results);
							if($beans1['total']>0){
								$query2 = "UPDATE smsad_sms_cstm SET message_status_c = '".$Message."', sms_language_c = '".$bean->sms_language_c."', chksmslanguage_translation_c = '".$bean->chksmslanguage_translation_c."' where id_c='".$bean_id."'";
								$result2 = $GLOBALS['db']->query($query2, true, "Error updating tasks entry: ");
							}
							else{
								$query2 = "INSERT INTO smsad_sms_cstm (id_c, message_status_c, sms_language_c, chksmslanguage_translation_c) values('" . $bean_id . "', '".$Message."','" . $bean->sms_language_c . "','" . $bean->chksmslanguage_translation_c . "');";
								$result2 = $GLOBALS['db']->query($query2, true, "Error updating tasks entry: ");
							}
							if($response->Success=='True')
							{
								$mdr_record = $controller->getMessageLookup($mdr_id); 
								$Message = $mdr_record->SMS->Status;

								 $query2 = "UPDATE smsad_sms SET message_id='" . $mdr_id . "', message_status ='".$mdr_record->SMS->Status."', message_response =concat(message_response,'".addslashes(json_encode($mdr_record))."') WHERE  id='" . $bean_id . "';";
								$result2 = $GLOBALS['db']->query($query2, true, "Error updating tasks entry: ");
								
								$query = "SELECT COUNT(*) AS total FROM smsad_sms_cstm WHERE id_c='".$bean_id."'";
								$results = $GLOBALS['db']->query($query, true, "Error updating tasks entry: ");
								$beans1 = $GLOBALS['db']->fetchByAssoc($results);
								if($beans1['total']>0){
									$query2 = "UPDATE smsad_sms_cstm SET message_status_c = '".$mdr_record->SMS->Status."', sms_language_c = '".$bean->sms_language_c."', chksmslanguage_translation_c = '".$bean->chksmslanguage_translation_c."' where id_c='".$bean_id."'";
									$result2 = $GLOBALS['db']->query($query2, true, "Error updating tasks entry: ");
								}
								else{
									$query2 = "INSERT INTO smsad_sms_cstm (id_c, message_status_c, sms_language_c, chksmslanguage_translation_c) values('" . $bean_id . "', '".$mdr_record->SMS->Status."');";
									$result2 = $GLOBALS['db']->query($query2, true, "Error updating tasks entry: ");
								}
							}
							SugarApplication::appendErrorMessage($Message);
							continue;
						} catch (\SMSCountryMessagingLib\APIException $e) {
							SugarApplication::appendErrorMessage("Error1 - " . strval($e->getResponseCode()) . ' ' . $e->getMessage());
							$query = "SELECT COUNT(*) AS total FROM smsad_sms_cstm WHERE id_c='".$bean_id."'";
							$results = $GLOBALS['db']->query($query, true, "Error updating tasks entry: ");
							$beans1 = $GLOBALS['db']->fetchByAssoc($results);
							if($beans1['total']>0){
								$query2 = "UPDATE smsad_sms_cstm SET message_status_c = '"."Error1 - " . strval($e->getResponseCode()) . ' ' . $e->getMessage() . ' ' . json_encode($e->getResponseBody())."', sms_language_c = '".$bean->sms_language_c."', chksmslanguage_translation_c = '".$bean->chksmslanguage_translation_c."' where id_c='".$bean_id."'";
								$result2 = $GLOBALS['db']->query($query2, true, "Error updating tasks entry: ");
							}
							else{
								$query2 = "INSERT INTO smsad_sms_cstm (id_c, message_status_c, sms_language_c, chksmslanguage_translation_c) values('" . $bean_id . "', '"."Error1 - " . strval($e->getResponseCode()) . ' ' . $e->getMessage() . ' ' . json_encode($e->getResponseBody())."', '".$bean->sms_language_c."', '".$bean->chksmslanguage_translation_c."');";
								$result2 = $GLOBALS['db']->query($query2, true, "Error updating tasks entry: ");
							}
							continue;
						} catch (Exception $e) {
							SugarApplication::appendErrorMessage("Error2 - " . $e->getMessage());
							$query = "SELECT COUNT(*) AS total FROM smsad_sms_cstm WHERE id_c='".$bean_id."'";
							$results = $GLOBALS['db']->query($query, true, "Error updating tasks entry: ");
							$beans1 = $GLOBALS['db']->fetchByAssoc($results);
							if($beans1['total']>0){
								$query2 = "UPDATE smsad_sms_cstm SET message_status_c = '"."Error2 - " . $e->getMessage()."', sms_language_c = '".$bean->sms_language_c."', chksmslanguage_translation_c = '".$bean->chksmslanguage_translation_c."' where id_c='".$bean_id."'";
								$result2 = $GLOBALS['db']->query($query2, true, "Error updating tasks entry: ");
							}
							else{
								$query2 = "INSERT INTO smsad_sms_cstm (id_c, message_status_c, sms_language_c, chksmslanguage_translation_c) values('" . $bean_id . "', '"."Error2 - " . $e->getMessage()."', '".$bean->sms_language_c."', '".$bean->chksmslanguage_translation_c."');";
								$result2 = $GLOBALS['db']->query($query2, true, "Error updating tasks entry: ");
							}
							continue;
						}
					}
					//exit;
					return true;
				}
				catch (Exception $e) {
					SugarApplication::appendErrorMessage("Error2 - " . $e->getMessage());
					Foreach($_REQUEST["mulbeanid"] as $key => $bean_id )
					{
						$query = "SELECT COUNT(*) AS total FROM smsad_sms_cstm WHERE id_c='".$bean_id."'";
						$results = $GLOBALS['db']->query($query, true, "Error updating tasks entry: ");
						$beans1 = $GLOBALS['db']->fetchByAssoc($results);
						if($beans1['total']>0){
							$query2 = "UPDATE smsad_sms_cstm SET message_status_c = '"."Error2 - " . $e->getMessage()."', sms_language_c = '".$bean->sms_language_c."', chksmslanguage_translation_c = '".$bean->chksmslanguage_translation_c."' where id_c='".$bean_id."'";
							$result2 = $GLOBALS['db']->query($query2, true, "Error updating tasks entry: ");
						}
						else{
							$query2 = "INSERT INTO smsad_sms_cstm (id_c, message_status_c, sms_language_c, chksmslanguage_translation_c) values('" . $bean_id . "', '"."Error2 - " . $e->getMessage()."', '".$bean->sms_language_c."', '".$bean->chksmslanguage_translation_c."');";
							$result2 = $GLOBALS['db']->query($query2, true, "Error updating tasks entry: ");
						}
					}
				}
			}
			else
			{
				$to_smsarr[] = $bean->to_sms;
				foreach($to_smsarr as $to_sms) { //Bala
					// Retrieve the MDR record
					try {
						if(trim($bean->from_sms) == '')
							throw new exception("No Sender Selected");
						if(trim($to_sms)== trim($bean->from_sms))
							throw new exception("From and To Number are same.");
						if(trim($to_sms)== '')
							throw new exception("Invalid To Number.");
						if(trim($bean->description)== '')
							throw new exception("Message Should not be Empty.");
						// Build our message
						$from_number = $bean->from_sms;
						$to_number = $to_sms;
						$message = new Message($to_number, $from_number, $bean->description);

						// Send the message
						$response = $controller->createMessage($message);

						// get the mdr id from the response
						$mdr_id = $response->MessageUUID;
						$Message = $response->Message;

						$query2 = "UPDATE smsad_sms SET message_id='" . $mdr_id . "', message_response ='".addslashes(json_encode($response))."' WHERE  id='" . $bean->id . "';";
						$result2 = $GLOBALS['db']->query($query2, true, "Error updating tasks entry: ");
						$query = "SELECT COUNT(*) AS total FROM smsad_sms_cstm WHERE id_c='".$bean->id."'";
						$results = $GLOBALS['db']->query($query, true, "Error updating tasks entry: ");
						$beans1 = $GLOBALS['db']->fetchByAssoc($results);
						if($beans1['total']>0){
							$query2 = "UPDATE smsad_sms_cstm SET message_status_c = '".$Message."', sms_language_c = '".$bean->sms_language_c."', chksmslanguage_translation_c = '".$bean->chksmslanguage_translation_c."' where id_c='".$bean->id."'";
							$result2 = $GLOBALS['db']->query($query2, true, "Error updating tasks entry: ");
						}
						else{
							$query2 = "INSERT INTO smsad_sms_cstm (id_c, message_status_c, sms_language_c, chksmslanguage_translation_c) values('" . $bean->id . "', '".$Message."','" . $bean->sms_language_c . "','" . $bean->chksmslanguage_translation_c . "');";
							$result2 = $GLOBALS['db']->query($query2, true, "Error updating tasks entry: ");
						}
						if($response->Success=='True')
						{
							$mdr_record = $controller->getMessageLookup($mdr_id); // 'mdr1-b334f89df8de4f8fa7ce377e06090a2e'
							//$mdr_record = json_encode($mdr_record);
							// description='" . $mdr_record . "'message_response
							$Message = $mdr_record->SMS->Status;
							$query2 = "UPDATE smsad_sms SET message_id='" . $mdr_id . "', message_status ='".$mdr_record->SMS->Status."', message_response =concat(message_response,'".addslashes(json_encode($mdr_record))."') WHERE  id='" . $bean->id . "';";
							$result2 = $GLOBALS['db']->query($query2, true, "Error updating tasks entry: ");
							
							$query = "SELECT COUNT(*) AS total FROM smsad_sms_cstm WHERE id_c='".$bean->id."'";
							$results = $GLOBALS['db']->query($query, true, "Error updating tasks entry: ");
							$beans1 = $GLOBALS['db']->fetchByAssoc($results);
							if($beans1['total']>0){
								$query2 = "UPDATE smsad_sms_cstm SET message_status_c = '".$mdr_record->SMS->Status."', sms_language_c = '".$bean->sms_language_c."', chksmslanguage_translation_c = '".$bean->chksmslanguage_translation_c."' where id_c='".$bean->id."'";
								$result2 = $GLOBALS['db']->query($query2, true, "Error updating tasks entry: ");
							}
							else{
								$query2 = "INSERT INTO smsad_sms_cstm (id_c, message_status_c, sms_language_c, chksmslanguage_translation_c) values('" . $bean->id . "', '".$mdr_record->SMS->Status."', '".$bean->sms_language_c."', '".$bean->chksmslanguage_translation_c."');";
								$result2 = $GLOBALS['db']->query($query2, true, "Error updating tasks entry: ");
							}
						}
						SugarApplication::appendErrorMessage($Message);
						continue;
					} catch (\SMSCountryMessagingLib\APIException $e) {
						SugarApplication::appendErrorMessage("Error1 - " . strval($e->getResponseCode()) . ' ' . $e->getMessage() . ' ' . json_encode($e->getResponseBody()));
						$query = "SELECT COUNT(*) AS total FROM smsad_sms_cstm WHERE id_c='".$bean->id."'";
						$results = $GLOBALS['db']->query($query, true, "Error updating tasks entry: ");
						$beans1 = $GLOBALS['db']->fetchByAssoc($results);
						if($beans1['total']>0){
							$query2 = "UPDATE smsad_sms_cstm SET message_status_c = '"."Error1 - " . strval($e->getResponseCode()) . ' ' . $e->getMessage() . ' ' . json_encode($e->getResponseBody())."', sms_language_c = '".$bean->sms_language_c."', chksmslanguage_translation_c = '".$bean->chksmslanguage_translation_c."' where id_c='".$bean->id."'";
							$result2 = $GLOBALS['db']->query($query2, true, "Error updating tasks entry: ");
						}
						else{
							$query2 = "INSERT INTO smsad_sms_cstm (id_c, message_status_c, sms_language_c, chksmslanguage_translation_c) values('" . $bean->id . "', '"."Error1 - " . strval($e->getResponseCode()) . ' ' . $e->getMessage() . ' ' . json_encode($e->getResponseBody())."', '".$bean->sms_language_c."', '".$bean->chksmslanguage_translation_c."');";
							$result2 = $GLOBALS['db']->query($query2, true, "Error updating tasks entry: ");
						}
						continue;
					} catch (Exception $e) {
						SugarApplication::appendErrorMessage("Error2 - " . $e->getMessage());
						$query = "SELECT COUNT(*) AS total FROM smsad_sms_cstm WHERE id_c='".$bean->id."'";
						$results = $GLOBALS['db']->query($query, true, "Error updating tasks entry: ");
						$beans1 = $GLOBALS['db']->fetchByAssoc($results);
						if($beans1['total']>0){
							$query2 = "UPDATE smsad_sms_cstm SET message_status_c = '"."Error2 - " . $e->getMessage()."', sms_language_c = '".$bean->sms_language_c."', chksmslanguage_translation_c = '".$bean->chksmslanguage_translation_c."' where id_c='".$bean->id."'";
							$result2 = $GLOBALS['db']->query($query2, true, "Error updating tasks entry: ");
						}
						else{
							$query2 = "INSERT INTO smsad_sms_cstm (id_c, message_status_c, sms_language_c, chksmslanguage_translation_c) values('" . $bean->id . "', '"."Error2 - " . $e->getMessage()."', '".$bean->sms_language_c."', '".$bean->chksmslanguage_translation_c."');";
							$result2 = $GLOBALS['db']->query($query2, true, "Error updating tasks entry: ");
						}
						continue;
					}
				} //Bala
			}
			return true;
		} catch (\SMSCountryMessagingLib\APIException $e) {
			SugarApplication::appendErrorMessage("Error2 - " . strval($e->getResponseCode()) . ' ' . $e->getMessage());
			$query = "SELECT COUNT(*) AS total FROM smsad_sms_cstm WHERE id_c='".$bean->id."'";
			$results = $GLOBALS['db']->query($query, true, "Error updating tasks entry: ");
			$beans1 = $GLOBALS['db']->fetchByAssoc($results);
			if($beans1['total']>0){
				$query2 = "UPDATE smsad_sms_cstm SET message_status_c = '"."Error1 - " . strval($e->getResponseCode()) . ' ' . $e->getMessage() . ' ' . json_encode($e->getResponseBody())."', sms_language_c = '".$bean->sms_language_c."', chksmslanguage_translation_c = '".$bean->chksmslanguage_translation_c."' where id_c='".$bean->id."'";
				$result2 = $GLOBALS['db']->query($query2, true, "Error updating tasks entry: ");
			}
			else{
				$query2 = "INSERT INTO smsad_sms_cstm (id_c, message_status_c, sms_language_c, chksmslanguage_translation_c) values('" . $bean->id . "', '"."Error1 - " . strval($e->getResponseCode()) . ' ' . $e->getMessage() . ' ' . json_encode($e->getResponseBody())."', '".$bean->sms_language_c."', '".$bean->chksmslanguage_translation_c."');";
				$result2 = $GLOBALS['db']->query($query2, true, "Error updating tasks entry: ");
			}
			return true;
		} catch (Exception $e) {
			SugarApplication::appendErrorMessage("Error2 - " . $e->getMessage());
			$query = "SELECT COUNT(*) AS total FROM smsad_sms_cstm WHERE id_c='".$bean->id."'";
			$results = $GLOBALS['db']->query($query, true, "Error updating tasks entry: ");
			$beans1 = $GLOBALS['db']->fetchByAssoc($results);
			if($beans1['total']>0){
				$query2 = "UPDATE smsad_sms_cstm SET message_status_c = '"."Error2 - " . $e->getMessage()."', sms_language_c = '".$bean->sms_language_c."', chksmslanguage_translation_c = '".$bean->chksmslanguage_translation_c."' where id_c='".$bean->id."'";
				$result2 = $GLOBALS['db']->query($query2, true, "Error updating tasks entry: ");
			}
			else{
				$query2 = "INSERT INTO smsad_sms_cstm (id_c, message_status_c, sms_language_c, chksmslanguage_translation_c) values('" . $bean->id . "', '"."Error2 - " . $e->getMessage()."', '".$bean->sms_language_c."', '".$bean->chksmslanguage_translation_c."');";
				$result2 = $GLOBALS['db']->query($query2, true, "Error updating tasks entry: ");
			}
			return true;
		}
    }

	function createCallId(){
		$query = "SELECT UUID() AS id";
		$result = $GLOBALS['db']->query($query, true, "Error updating tasks entry: ");
		$row = $GLOBALS['db']->fetchByAssoc($result);
		return $row['id'];
	}

}
