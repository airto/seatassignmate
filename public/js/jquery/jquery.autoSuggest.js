 /*
 * AutoComplete Plugin 1.0 
 * http://amitpatil.byethost22.com/
 *
 * @version
 * 1.0 (May 20 2010)
 * 
 * @copyright
 * Copyright (C) 2010-2011 
 *
 * @Auther
 * Amit Patil (amitpatil321@gmail.com)
 * Maharashtra (India) 
 *
 * @license
 * This file is part of Facebook Style Add Friends.
 * 
 * Facebook Style Add Friends Script is freeware script. you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * Facebook Style Add Friends Script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this script.  If not, see <http://www.gnu.org/copyleft/lesser.html>.
 */
 (function($){
    $.fn.extend({
        autoSuggest : function(options){
            var defaults = {
                ajaxFilePath : "",
                ajaxParams   : "",
                autoFill     : false,
				width		 : "auto",
				opacity		 : "0.9",
				limit		 : "10",
				idHolder	 : "",
				match		 : "starts"
            };
            options = $.extend(defaults, options);		
			
			var ajaxFilePath = options.ajaxFilePath;
			var ajaxParams	 = options.ajaxParams;
			var autoFill     = options.autoFill;
			var width		 = options.width;
			var opacity		 = options.opacity;
			var limit		 = options.limit;
			var idHolder	 = options.idHolder;
			var match		 = options.match;

			return this.each(function() {
				var obj = $(this);
			
				obj.keyup(function(event){
					var p = obj;
					var offset = p.offset();
					var keyword = obj.val();

					if(keyword.length)
					 {
						 if(event.keyCode != 40 && event.keyCode != 38 && event.keyCode != 13)
						 {
							 if(ajaxFilePath != "")
							 {
								// alert(ajaxParams);
								 $.ajax({
								   type: "POST",
								   url: ajaxFilePath,
								   data: "data="+keyword+"&limit="+limit+"&match="+match+"&"+ajaxParams+"&getId=1",
								   success: function(responce){	
								//alert(responce);
									if(responce != 0)
									{
									  var vals = responce.split("|"); 
                                                            
									  if(vals.length)
									  {
                                                                        
										  optionList = "<ul class=\"list\">";
                                                                                
										  for(i=0;i<vals.length;i++)
										  {
										  	 valuenid = vals[i].split("-");
											 myText = valuenid[1]; 
											 myId = valuenid[0]; 
                                                                       alert(myId+' '+myText);             
											 // trim string to remove extra white spaces around the text
											 myText = myText.replace(/^\s+|\s+$/g,"");

											 if(match == "starts")
												 // check if string starts with given characters
												 myText = myText.replace( myText.match("^"+keyword), '<span class="highlighted">'+keyword+'</span>');
											 else if(match == "ends")
												 //alert(myText.replace(/(.*)keyword/, "<b>hi</b>"));
												 myText = myText.replace (new RegExp( keyword + '$'), '<span class="highlighted">'+keyword+'</span>');
											 else if(match == "contains")
												 myText = myText.replace( new RegExp(keyword, "i" ), '<span class="highlighted">'+keyword+'</span>');

											 if(idHolder != "" && idHolder != null)
												optionList += "<li><a href=\"javascript:void(0);\" id=\""+myId+"\" tabindex=\""+-1+"\"  >"+myText+"</a></li>";
											 else 		
												optionList += "<li><a href=\"javascript:void(0);\"  tabindex=\""+myId+"\" >"+-1+"</a></li>";
										  }
										  optionList += "</ul>";
									  }
                                                                    //alert(optionList);
									  if($(".ajax_response").html() == null)
									  {
									  
										  var id = obj.attr("id");
                                                                //alert(id);
										  // initialization
										  $("<div class='ajax_response'></div>").insertAfter(obj)
										  .css("left",parseInt($("#"+id).offset().left))
										  .css("top",parseInt(offset.top + obj.height() + 3))
										  .css("opacity",opacity)
										  .html(optionList).css("display","block");

										  // set responce div width
										  if(width == "auto")
											  $(".ajax_response").css("width",parseInt(obj.width()) + 2);
										  else	
											  $(".ajax_response").css("width",parseInt(width + 2));
									  }
									  else
										  $(".ajax_response").html(optionList).css("display","block");
									}
									else
									  $(".list").css("display","none");
								   }
								 });
							 }
							 else
								 alert("Ajax file path not provided");
						 }
						 else
						 {
							$(".list li .selected").removeClass("selected");
							switch (event.keyCode)
							{
							 case 40:
							 {
								  found = 0;
								  $(".ajax_response .list li").each(function(){
									 if($(this).attr("class") == "selected")
										found = 1;
								  });
								  if(found == 1)
								  {
									var sel = $("li[class='selected']");
									// check if his is a last element in the list
									// if so then add selected class to the first element in the list
									if(sel.next().text() == "")					
										$(".ajax_response li:first").addClass("selected");
									else
										sel.next().addClass("selected");
									// remove class selected from previous item
									sel.removeClass("selected");
								  }
								  else
									$(".ajax_response li:first").addClass("selected");
							  }
							 break;
							 case 38:
							 {
								  found = 0;
								  $(".ajax_response .list li").each(function(){
									 if($(this).attr("class") == "selected")
										found = 1;
								  });
								  if(found == 1)
								  {
									var sel = $(".list li[class='selected']");
									// check if his is a last element in the list
									// if so then add selected class to the first element in the list
									if(sel.prev().text() == "")					
										$(".ajax_response .list li:last").addClass("selected");
									else
										sel.prev().addClass("selected");
									// remove class selected from previous item
									sel.removeClass("selected");
								  }
								  else
									$(".ajax_response .list li:last").addClass("selected");
							 }
							 break;
							 case 13:
								str = $(".list li[class='selected']").text();
								obj.val(str);
								// store id of the selected option
								if(idHolder != "" && idHolder != null)
									$("#"+idHolder).val($("li[class='selected'] a").attr("id"));
								$(".list").fadeOut("fast");
							 break;
							}
							// if autoFill option is true then fill selected value in textbox
							if(autoFill)
							{
								str = $(".list li[class='selected']").text();
								obj.val(str);
							}
						 }
					 }
					else
						// if there is no character in the text field then remove the suggestion box 
						$(".list").fadeOut("fast");
				});

			    // prevent form submission on enter key press
				obj.keypress(function(event){
				 if(event.keyCode == "13")
					 return false;
			    });	

				$(".ajax_response .list li").live("mouseover",function () {
					$(".list li[class='selected']").removeClass("selected");
					$(this).addClass("selected");
					// if autoFill option is true then fill selected value in textbox
					if(autoFill)
					{
						str = $(".list li[class='selected']").text();
						obj.val(str);
					}
				});
				$(".ajax_response .list li").live("click",function () {
					str = $(".list li[class='selected']").text();
					obj.val(str);
					// store id of the selected option
					if(idHolder != "" && idHolder != null)
						$("#"+idHolder).val($("li[class='selected'] a").attr("id"));
					$(".list").fadeOut("fast");
				});
				$(document).click(function(){
					if($(".list").css("display") == "block")
						$(".list").fadeOut("fast");
				});
				$(document).keyup(function(event){
					if(event.keyCode == 9)
					{
						str = $(".list li[class='selected']").text();
						obj.val(str);
						// store id of the selected option
						if(idHolder != "" && idHolder != null)
							$("#"+idHolder).val($("li[class='selected'] a").attr("id"));
						$(".list").fadeOut("fast");
					}
				});
			});
	    }
    });	
}) (jQuery);