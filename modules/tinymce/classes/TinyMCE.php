<?php

class TinyMCE extends FermiObject
{
	function __construct() 
	{
		Core::addListener('onAdminAgentReady', array($this, 'setup')) ;
	}
	
	function setup()
	{
		$module = Registry::getModule('tinymce') ;
		Response::append('aux_head', '
<script type="text/javascript" src="'.SYSURI.$module.'lib/tiny_mce/jquery.tinymce.js"></script>') ;

		Response::append('aux_js', '
$(document).ready(function() {
			$("textarea.tinymce").tinymce({
				// Location of TinyMCE script
				script_url : "'.SYSURI.$module.'lib/tiny_mce/tiny_mce.js",

				// General options
				onchange_callback: function(editor) {
							tinyMCE.triggerSave();
							//var content = tinyMCE.get(editor.id).getContent() ;
							//$("#"+editor.id).val("yeah") ;
							//alert($("#"+editor.id).val()) ;
							
							//alert("#" + editor.id);
				},
				theme : "advanced",
				plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist",

				// Theme options
				theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
				theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor,pagebreak",
				/*theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
				theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",*/
				theme_advanced_toolbar_location : "top",
				theme_advanced_toolbar_align : "left",
				theme_advanced_statusbar_location : "bottom",
				theme_advanced_resizing : true,

				// Example content CSS (should be your site CSS)
				content_css : "css/content.css",

				// Drop lists for link/image/media/template dialogs
				template_external_list_url : "lists/template_list.js",
				external_link_list_url : "lists/link_list.js",
				external_image_list_url : "lists/image_list.js",
				media_external_list_url : "lists/media_list.js",

				// Replace values for the template plugin
				template_replace_values : {
					username : "Some User",
					staffid : "991234"
				}
			});
		}) ;') ;
	}
}