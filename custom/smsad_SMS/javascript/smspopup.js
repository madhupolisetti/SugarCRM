//**
// * Asterisk SugarCRM Integration 
// * (c) KINAMU Business Solutions AG 2009
// * 
// * Parts of this code are (c) 2006. RustyBrick, Inc.  http://www.rustybrick.com/
// * Parts of this code are (c) 2008 vertico software GmbH  
// * Parts of this code are (c) 2009 Copyright (c) 2009 Anant Garg (anantgarg.com | inscripts.com)
// * Parts of this code are (c) 2009 abcona e. K. Angelo Malaguarnera E-Mail admin@abcona.de
// * Parts of this code are (c) 2011 Blake Robertson http://www.blakerobertson.com
// * Parts of this code are (c) 2012 Patrick Hogan askhogan@gmail.com
// * http://www.sugarforge.org/projects/yaai/
// * Contribute To Project: http://www.github.com/blak3r/yaai
// * 
// * This program is free software; you can redistribute it and/or modify it under
// * the terms of the GNU General Public License version 3 as published by the
// * Free Software Foundation with the addition of the following permission added
// * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
// * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
// * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
// * 
// * This program is distributed in the hope that it will be useful, but WITHOUT
// * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
// * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
// * details.
// * 
// * You should have received a copy of the GNU General Public License along with
// * this program; if not, see http://www.gnu.org/licenses or write to the Free
// * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
// * 02110-1301 USA.
// * 
// * You can contact KINAMU Business Solutions AG at office@kinamu.com
// * 
// * The interactive user interfaces in modified source and object code versions
// * of this program must display Appropriate Legal Notices, as required under
// * Section 5 of the GNU General Public License version 3.
// * 


$(document).ready(function() {
	if($('#phone_work_hidden').length>0)
	{
		if($('#phone_work_hidden').val().trim()!='')
		  $('#to_sms').val($('#phone_work_hidden').val());
	}
	if($('#first_name_hidden').length>0)
	{
		if($('#first_name_hidden').val().trim()!='')
	        $('#parent_name').val($('#first_name_hidden').val());
	}
	if($('#parent_id_hidden').length>0)
    {
		if($('#parent_id_hidden').val().trim()!='')
		{
			//$('#parent_id').val($('#parent_id_hidden').val());
		}
	}
    var val2 = $('#module').val();
    if(val2 == 'Leads')
        $('#parent_type option[value=Leads]').attr('selected','selected');
    if(val2 == 'Contacts')
        $('#parent_type option[value=Contacts]').attr('selected','selected');
    if(val2 == 'Accounts')
        $('#parent_type option[value=Accounts]').attr('selected','selected');
	//$("#from_sms").parent().html("{SenderIds}");
	//$("#from_sms_label").hide();
	$("#name").parent().hide();
	$("#name_label").hide();		
	
	
	$("#description").parent().append("<span id='counter'>0/160<span>");

	$("#parent_type").on('change',function(){
		if($("#parent_type").val()=='Accounts')
		{
			$("#btn_parent_name").attr('onclick','open_popup(this.form.parent_type.value, 600, 400, "", true, false, {"call_back_function":"myAwesomefunction","form_name":"EditView","field_to_name_array":{"id":"parent_id","name":"parent_name","phone_office":"to_sms"}}, "multi", true);');
		}
		else
		{
			$("#btn_parent_name").attr('onclick','open_popup(this.form.parent_type.value, 600, 400, "", true, false, {"call_back_function":"myAwesomefunction","form_name":"EditView","field_to_name_array":{"id":"parent_id","name":"parent_name","phone_mobile":"to_sms"}}, "multi", true);');
		}
	});
	
	if($("#parent_type").val()=='Accounts')
	{
		$("#btn_parent_name").attr('onclick','open_popup(this.form.parent_type.value, 600, 400, "", true, false, {"call_back_function":"myAwesomefunction","form_name":"EditView","field_to_name_array":{"id":"parent_id","name":"parent_name","phone_office":"to_sms"}}, "multi", true);');
	}
	else
	{
		$("#btn_parent_name").attr('onclick','open_popup(this.form.parent_type.value, 600, 400, "", true, false, {"call_back_function":"myAwesomefunction","form_name":"EditView","field_to_name_array":{"id":"parent_id","name":"parent_name","phone_mobile":"to_sms"}}, "multi", true);');
	}

	$("#btn_clr_smsad_sms_template_smsad_sms_name").attr('onclick','SUGAR.clearRelateField(this.form, "smsad_sms_template_smsad_sms_name", "smsad_sms_template_smsad_smssmsad_sms_template_ida", "smsad_sms_template_smsad_smssmsad_sms_template_ida"); document.getElementById("description").value="";');
			
	$("#parent_type option[value='Tasks']").remove();
	$("#parent_type option[value='Opportunities']").remove();
	$("#parent_type option[value='Bugs']").remove();
	$("#parent_type option[value='Cases']").remove();
	$("#parent_type option[value='Project']").remove();
	$("#parent_type option[value='ProjectTask']").remove();
	$("#parent_type option[value='Prospects']").remove();
	
	$('#send_text').keyup(updateCount);
	$('#send_text').keydown(updateCount);

	$('#description').keyup(updateCount);
	$('#description').keydown(updateCount);
	
	$("#btn_clr_parent_name").attr('onclick','this.form.parent_name.value = \'\'; this.form.parent_id.value = \'\'; this.form.to_sms.value = \'\';');
	
	$("#btn_smsad_sms_template_smsad_sms_name").attr('onclick','open_popup("smsad_sms_template", 600, 400, "", true, false, {"call_back_function":"myAwesomefunction","form_name":"EditView","field_to_name_array":{"id":"smsad_sms_template_smsad_smssmsad_sms_template_ida","name":"smsad_sms_template_smsad_sms_name","description":"description"}}, "single", true);');

	$("#parent_type").attr('onchange','document.EditView.parent_name.value="";document.EditView.to_sms.value="";document.EditView.parent_id.value=""; changeParentQS("parent_name"); checkParentType(document.EditView.parent_type.value, document.EditView.btn_parent_name);');
	
});
	function myAwesomefunction(data){
		//Do stuff before fields are set
		set_return(data);
		//Do stuf after fields are set
		updateCount();
	}
	function updateCount() {
		var cs = $('#description').val().length;
		var str = $("#description").val();
		otherlang = false;
		for (var i = 0; i < cs; i++)
		{
			n = str.charCodeAt(i);
			if (parseInt(n) > 255)
			{
				otherlang = true;
				break;
			}
		}
		var Tckbox = $('input[name=chksmslanguage_translation_c]:checked').val();
		preferredLanguage = $("#sms_language_c").val();
		if((Tckbox != '1' && otherlang==false) || (preferredLanguage=="en" && otherlang==false))
		{
			if(cs>160)
				$('#counter').html(cs +"/154");
			else
				$('#counter').html(cs +"/160");
		}
		else
		{
			if(cs>70)
				$('#counter').html(cs +"/67");
			else
				$('#counter').html(cs +"/70");
		}
	}
	var preferredLanguage = "te";

	var transliterationControl;
	google.load("elements", "1", { packages: "transliteration" , nocss : true, callback : "onLoad"  });
	function onLoad() {
		var a = {
			sourceLanguage: "en",
			destinationLanguage: ["te", "hi", "kn", "ml", "ta", "ar", "ur", "ti", "sr", "si", "ru", "sa", "pa", "fa", "or", "ne", "mr", "gu", "el", "zh", "bn", "am"],
			transliterationEnabled: false,
			shortcutKey: "ctrl+g"
		};
		transliterationControl = new google.elements.transliteration.TransliterationControl(a);
		transliterationControl.makeTransliteratable(["description"]);
		transliterationControl.addEventListener(google.elements.transliteration.TransliterationControl.EventType.SERVER_UNREACHABLE, serverUnreachableHandler);
		transliterationControl.addEventListener(google.elements.transliteration.TransliterationControl.EventType
			.SERVER_REACHABLE,
			serverReachableHandler);
	}
	function serverUnreachableHandler(a) { console.log( "Transliteration Server unreachable"); }
	function serverReachableHandler(a) { document.getElementById("errorDiv").innerHTML = ""; }
	google.setOnLoadCallback(onLoad);

	$(document).ready(function()
	{
		$("#sms_language_c").attr('disabled',true);
		$("#sms_language_c").change(function(){
			preferredLanguage = $("#sms_language_c").val();
			updateCount();
		})
		
		$('#chksmslanguage_translation_c').bind('click',
			function ()
			{
				var Tckbox = $('input[name=chksmslanguage_translation_c]:checked').val();
				$("#sms_language_c").attr('disabled',true);
				if((Tckbox == '1')){
					$("#sms_language_c").attr('disabled',false);
				}
				else
				{
					transliterationControl.disableTransliteration();
					transliterationControl.setLanguagePair(google.elements.transliteration.LanguageCode.ENGLISH, "hi");
				}
				updateCount();
			});
		$('#description').bind('blur change keypress paste input',
			function ()
			{	
				var Tckbox = $('input[name=chksmslanguage_translation_c]:checked').val();
				if((Tckbox == '1')){
					preferredLanguage = $("#sms_language_c").val();
					if(preferredLanguage!='en')
					{
						transliterationControl.makeTransliteratable(["description"]);
						transliterationControl.enableTransliteration();
						transliterationControl.setLanguagePair(google.elements.transliteration.LanguageCode.ENGLISH, preferredLanguage);
					}
					else
					{
						transliterationControl.disableTransliteration();
						transliterationControl.setLanguagePair(google.elements.transliteration.LanguageCode.ENGLISH, "hi");
					}
				}	
				updateCount();
		});
	});