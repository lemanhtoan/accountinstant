	
	function reloadOptionImage(element,option_type_id)
	{	
		//set class for menu-image
		var index_select;
		var i;
		for( i=0 ; i < element.options.length ; i++)
		{
			index_select = element.options[i].value;
			if(index_select != 0)
			{
				$('menu_image_'+ index_select).removeClassName('active-menu-image');
			}
		}		
		if($('menu_image_'+ option_type_id) != null )
		{
			$('menu_image_'+ option_type_id).addClassName('active-menu-image');
		}	
	}
	
	
	function sameReloadPrice(option_id,option_type_id)
	{		
		//checkbox or radio
		if($('options_'+ option_id +'_'+ option_type_id) != null)
		{
			if($('options_'+ option_id +'_'+ option_type_id).type == 'radio')
			{
				$('options_'+ option_id +'_'+ option_type_id).checked = true;
			} else {
				if( $('options_'+ option_id +'_'+ option_type_id).checked == true)
				{
					$('options_'+ option_id +'_'+ option_type_id).checked = false;
				} else {
					$('options_'+ option_id +'_'+ option_type_id).checked = true;
				}
			}
			$('options_'+ option_id +'_'+ option_type_id) .onclick();
		}
		
		if($('select_'+ option_id) == null)
		{
			return;
		}
		
		//drop-down or multi select
		for(var i=0; i < $('select_'+ option_id).options.length; i++)
		{ 
			if($('select_'+ option_id).options[i].value == parseInt(option_type_id))
			{
				$('select_'+ option_id).selectedIndex = i;
			}
		}
		
		//call event onchange 
		$('select_'+ option_id).onchange();
		
		//set class menu-image
		var index_select;
		for(var j=0; j < $('select_'+ option_id).options.length; j++)
		{
			index_select = $('select_'+ option_id).options[j].value;
			if(index_select != 0)
			{
				$('menu_image_'+ index_select).removeClassName('active-menu-image');
			}
		}
		$('menu_image_'+ option_type_id).addClassName('active-menu-image');		
	}			

	function setCheckedValue(radioObj, newValue) 
	{
		var radioLength = radioObj.length;
		if(radioLength == undefined) 
		{
			radioObj.checked = (radioObj.value == newValue.toString());
			return;
		}
		for(var i = 0; i < radioLength; i++) 
		{
			radioObj[i].checked = false;
			if(radioObj[i].value == newValue.toString()) 
			{
				radioObj[i].checked = true;
			}
		}
	}	
	
	function removeClassNameByName(name,className)
	{
		var groupElement;

		groupElement = getElementsByName_iefix('div',name);
		
		for(var i=0;i< groupElement.length;i++)
		{
			groupElement[i].removeClassName(className);
		}
	}
	
	function getElementsByName_iefix(tag, name) {
	
     var elem = document.getElementsByTagName(tag);
	 
     var arr = new Array();
	 var iarr = 0;
	 
     for(var i = 0;i < elem.length; i++) 
	 {
		 att = elem[i].getAttribute("name");
          if(att == name) 
		  {
               arr[iarr] = elem[i];
               iarr++;
          }
     }
     return arr;
	}	
	
	function overviewOption(menuimage,option_id,info)
	{
		menuimage.style.cursor='pointer';
		//$('overviewoption'+ option_id).innerHTML = info;
	}
	
	function hiddenOverview(option_id)
	{
		//$('overviewoption'+ option_id).innerHTML = '';	
	}
	
	function autoLoadImage()
	{
		var imageoptions = document.getElementsByName('imageoption');
		
		var img_url;
		
		var image_obj;
		
		for(var i =0;i < imageoptions.length ;i++)
		{
			img_url = imageoptions[i].value;
			
			image_obj = new Image();
			
			image_obj.src = img_url;
		}
	}

	function clickCheckBox(element_name,optionId){
		var listElement = getElementsByName_iefix('input',element_name);
	
		var is_checked = false;
		
		for(var i=0; i < listElement.length; i++)
		{
			if(listElement[i].checked == true)
			{
				is_checked = true;
			}
		}
	
		if($('radio_'+ optionId) != null)
		{
			if(is_checked == true)
			{
				$('radio_'+ optionId).checked = true;
			} else {
				$('radio_'+ optionId).checked = false;
			}
		}
	}
	
	function setProductCheckboxID()
	{
		var radios = document.getElementsByClassName('checkbox');
			
		for( var mi=0;mi<radios.length ;mi++)
		{
			radios[mi].name = 'productid[]';
			radios[mi].id = 'product_id_' + radios[mi].value;
			
			if($('is_seted') == null)
			{
				Event.observe(radios[mi], 'click', function(event){ 	
					setProduct();
				});
			}				
		}
		
		if($('filter_entity_id') != null)
		{
			$('filter_entity_id').innerHTML = '<input type="hidden" id="is_seted" value="1">';
		}		
		
		setTimeout('setProductCheckboxID()',500); 
	}
	
	function setProduct()
	{
			var radios = document.getElementsByClassName('checkbox');
			var id;
			
			for( var mi=0;mi<radios.length ;mi++)
			{	
				id = radios[mi].value;
				if($('product_'+ id) != null)
				{
					if(radios[mi].checked == false)
						$('product_' + id).value = '';
					else
						$('product_' + id).value = id;
				}
			}
	}	

	
var OptionTemplate = Class.create();
OptionTemplate.prototype = {
    initialize: function(url){
        
        this.url = url;
       
    },

	selecttemplate: function(){	
		
		var template_id = $('optiontemplate_id').value;
		var descrp_id = 'description-templ-'+ template_id;
		var pre_template_id = $('curr-template-id').value;
		var pre_descrp_id = 'description-templ-'+ pre_template_id;
		
		if($(pre_descrp_id) != null)
		{
			$(pre_descrp_id).style.display = 'none';
		}
		
		if($(descrp_id) != null)
		{
			$(descrp_id).style.display = 'block';
			$('curr-template-id').value = template_id;
		}
		/*
	    var url = this.url;
	   
	    url = url + 'template_id/'+ template_id;
		
		var request = new Ajax.Updater('tmpl-description',url,{method: 'get', onFailure: ""}); 
		*/
	}
}
	
function enableInputFile(option_type_id)
{
	var inputfiles = document.getElementsByName('productimage'+option_type_id);
	for(var i=0;i<inputfiles.length;i++) {
		var inputfile = inputfiles[i];
		if(inputfile.disabled == true)
			inputfile.disabled = false;
		else inputfile.disabled = true;	
	}
}