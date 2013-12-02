/* ***** BEGIN LICENSE BLOCK *****
 * This file is part of Plume CMS, a website management application.
 * Copyright (C) 2001-2005 Loic d'Anterroches and contributors.
 *
 * Plume CMS is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Plume CMS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * ***** END LICENSE BLOCK ***** */

function catChangePath(idArray)
{
    if (document.formPost.c_path.value == '') {
        document.formPost.c_path.value = idArray[document.formPost.c_parentid.value];
    }
}

function setUrl(titleField, urlField, param, param2)
{
    url   = $("#"+urlField);
    title = $("#"+titleField);
    if (param == 'cat') {
        //if (document.getElementById('quirk_category_path').value == 0) {
            urlFromTitle = titleToUrl(title.val(),'cat') +'/';
            if (urlFromTitle =='/') urlFromTitle ='';
            url.val(param2[document.formPost.cat_id.value] + urlFromTitle);
        //}
    } else {
        //if (param2 == 0) {
            url.val(titleToUrl(title.val(),'art'));
        //}
    }
}

function titleToUrl(string, type)
{
    if (type == 'art') {
        return string.replace(/([^a-z0-9 ])/ig, replaceFunc).replace(/[^a-z0-9 ]/ig,
        '').replace(/ /g, '-').replace(/^([0-9]+)/ig,
        '').replace(/([\-]+)$/ig, '').replace(/([\-]+)/ig, '-').replace(/([0-9\-]+)$/ig, '').replace(/([\-]+)$/ig, '').replace(/^([\-]+)/ig, '');
    } else {
        return string.replace(/([^a-z0-9 ])/ig, replaceFunc).replace(/[^a-z0-9 ]/ig,
        '').replace(/ /g, '-').replace(/([\-]+)/ig, '-').replace(/([\-]+)$/ig, '').replace(/^([\-]+)/ig, '');
    }
}

/** str: matched substring
    p1: parenthetical match
    offset: offset within the string
    s: string itself
    */
function replaceFunc(str, p1, offset, s)
{
    notclean = new String(unescape("%C0%C1%C2%C3%C4%C5%E0%E1%E2%E3%E4%E5%D2%D3%D4%D5%D6%D8%F2%F3%F4%F5%F6%F8%C8%C9%CA%CB%E8%E9%EA%EB%CC%CD%CE%CF%EC%ED%EE%EF%D9%DA%DB%DC%F9%FA%FB%FC%FF%D1%F1%E7%FD%DD%u0161%u0160%u011B%u011A%u010D%u010C%u0159%u0158%u017E%u017D%u016F%u016E%u0148%u0147%u010F%u010E%u0165%u0164%u013E%u013D%u013A%u0139%u0155%u0154"));
    clean    = new String("AAAAAAaaaaaaOOOOOOooooooEEEEeeeeIIIIiiiiUUUUuuuuyNncyYsSeEcCrRzZuUnNdDtTlLlLrR");
    idx = notclean.indexOf(str);
    if (idx != -1) {
        return clean.charAt(idx);
    } else {
        return ' ';
    }
}

function openClose(id,mode)
{
	/*
	element = $('#'+id);
	img = $("#img_"+id);

	if(mode == 0) {
		if(element.css('display') == 'block' ) {
			element.css('display', 'none');
			img.attr('src', 'themes/'+pxThemeid+'/images/plus.png');
		} else {
			element.css('display', 'block');
			img.attr('src', 'themes/'+pxThemeid+'/images/minus.png');
		}
	} else if(mode == 1) {
		element.css('display', 'block');
		img.attr('src', 'themes/'+pxThemeid+'/images/minus.png');
	} else if(mode == -1) {
		element.css('display', 'none');
		img.attr('src', 'themes/'+pxThemeid+'/images/plus.png');
	}
	*/

}


function openCloseClass(id,mode)
{
	element = $('.'+id);
	img = $("#imgShow_"+id);

	if(mode == 0) {
		if (element.css('display')=='block' ) {
			element.css('display' ,'none');
			img.attr('src', 'themes/'+pxThemeid+'/images/arrow-d.gif');
		} else {
			element.css('display', 'block');
			img.attr('src','themes/'+pxThemeid+'/images/arrow-u.gif');
		}
	} else if(mode == 1) {
		element.css('display', 'block');
		img.attr('src','themes/'+pxThemeid+'/images/arrow-u.gif');
	} else if(mode == -1) {
		element.css('display' ,'none');
		img.attr('src', 'themes/'+pxThemeid+'/images/arrow-d.gif');
	}

}

function openCloseSpan(id,mode)
{
	element = $('#'+id);

	if(mode == 0) {
		if(element.css('display') == 'inline' ) {
			element.css('display', 'none');
		} else {
			element.css('display', 'inline');
		}
	} else if(mode == 1) {
		element.css('display', 'inline');
	} else if(mode == -1) {
		element.css('display', 'none');
	}
}


function modifDateRefererTo(Referer,dt_field) {
	element = $("#"+Referer);
	fieldDate = $("#"+dt_field);
	
	//statut = element.checked;
	statut = ($('#'+Referer+':checked').val() == 'true');
	
	today = new Date();
	if (statut == false) {
	    mois = '0'+ (today.getMonth()+1);
	    mois = mois.substr(mois.length-2);
	    jour = '0'+ today.getDate();
	    jour = jour.substr(jour.length-2);
	    fieldDate.val(today.getFullYear().toString()+mois+jour);
	} else  {
		fieldDate.val('99991231');
	}
	fieldDate.change();
}

function openCloseBlockIf(id, idsource, cond, mode, modeelse)
{
	if(document.getElementById) {
		element = document.getElementById(id);
        sourceel = document.getElementById(idsource);
	} else if(document.all) {
		element = document.all[id];
        sourceel = document.all[idsource];
	} else return;

    if (sourceel.value == cond) {
        openCloseBlock(id,mode);
    } else {
        openCloseBlock(id,modeelse);
    }
}


// function in_array(needle,haystack)
// craig heydenburg 4/8/02
// this function is similar to the PHP function of the same name
function in_array(value, array)
{
    var bool = false;
    for (var i=0; i<array.length; i++) {
        if (array[i]== value) { bool=true; }
    }
    return bool;
}

function openCloseBlockIfArray(id, idsource, cond, mode, modeelse)
{
	if(document.getElementById) {
		element = document.getElementById(id);
        sourceel = document.getElementById(idsource);
	} else if(document.all) {
		element = document.all[id];
        sourceel = document.all[idsource];
	} else return;

    if (in_array(sourceel.value,cond)) {
        openCloseBlock(id,mode);
    } else {
        openCloseBlock(id,modeelse);
    }

}

function openCloseBlock(id,mode)
{
	if(document.getElementById) {
		element = document.getElementById(id);
	} else if(document.all) {
		element = document.all[id];
	} else return;

	if(element.style) {
		if(mode == 0) {
			if(element.style.display == 'block' ) {
				element.style.display = 'none';
			} else {
				element.style.display = 'block';
			}
		} else if(mode == 1) {
			element.style.display = 'block';
		} else if(mode == -1) {
			element.style.display = 'none';
		}
	}
}

function openCloseSpan(id,mode)
{
	if(document.getElementById) {
		element = document.getElementById(id);
	} else if(document.all) {
		element = document.all[id];
	} else return;

	if(element.style) {
		if(mode == 0) {
			if(element.style.display == 'block' ) {
				element.style.display = 'none';
			} else {
				element.style.display = 'block';
			}
		} else if(mode == 1) {
			element.style.display = 'block';
		} else if(mode == -1) {
			element.style.display = 'none';
		}
	}
}
function mOpenClose(idArray,mode)
{
	for(var i=0;i<idArray.length;i++)
	{
		openCloseSpan(idArray[i],mode);
	}
}

function levelOpenClose(idArray,mode)
{
	for(var i=0;i<idArray.length;i++)
	{
		openCloseClass(idArray[i],mode);
	}
}
function popup(url)
{
	window.open(url,'dc_popup',
	'alwaysRaised=yes,toolbar=no,height=450,width=480,menubar=no,resizable=yes,scrollbars=yes,status=no');
}

function insertTextIn(formObj,text)
{
	formObj.value += text;
}


function insertUrlImage(origine, url, act, text,funcNum)  {
	window.opener.CKEDITOR.tools.callFunction( funcNum, url );
}


function insertImage(origine,url,act,text)
{
	form = origine.forms['formPost'];
    if (form.n_content_format)           format = form.n_content_format.value;
    else if (form.a_description_format)  format = form.a_description_format.value;
    else if (form.a_page_content_format) format = form.a_page_content_format.value;
    else if (form.c_format)              format = form.c_format.value;

	
	title = window.prompt(text);

	if (act == 'img') {
    	if (format == 'wiki')
    	{
    		if (title != '') {
    			image = '(('+url+'|'+title+'))';
    		} else {
    			image = '(('+url+'))';
    		}
    	}
    	else
    	{
    		image = '<p><img src="'+url+'" alt="'+title+'" /></p>';
    	}
	} else {
    	if (format == 'wiki')
    	{
    		if (title != '') {
    			image = '['+title+'|'+url+']';
    		} else {
    			image = '['+url+']';
    		}
    	}
    	else
    	{
    		image = '<p><a href="'+url+'">'+title+'</a></p>';
    	}

	}

	image = "\n\n"+image;

if (tb && format != 'wiki') {
tb.syncContents('iframe');
}

    if (form.n_content)           form.n_content.value += image;
    else if (form.a_description)  form.a_description.value += image;
    else if (form.a_page_content) form.a_page_content.value += image;
    else if (form.c_description)  form.c_description.value += image;

if (tb && format != 'wiki') {
tb.syncContents('textarea');
}


}

function isFilled(elm) {
    if (elm.value == "" ||
        elm.value == null)
    return false;
    else return true;
}
function isReady(name, text) {
	arr = document.getElementsByName(name);
	inpt = arr.item(0);
    if (isFilled(inpt) == false) {
	    alert (text);
		inpt.focus();
    return false;
    }
return true;
}

function isDateGreater(first, second, text) {
	if(document.getElementById) {
		if (document.getElementById(first)) {
			firstElt = ''+document.getElementById(first).value
				+document.getElementById(first+'_h').value
				+document.getElementById(first+'_i').value
				+document.getElementById(first+'_s').value;
	        secondElt = ''+document.getElementById(second).value
	        	+document.getElementById(second+'_h').value
	        	+document.getElementById(second+'_i').value
	        	+document.getElementById(second+'_s').value;
			if (firstElt>secondElt) {
				alert(text);
				return false;
			} else return true;
		}
	} else if(document.all) {
		if (document.all[first])  {
			firstElt = ''+document.all[first].value
				+document.all[first+'_h'].value
				+document.all[first+'_i'].value
				+document.all[first+'_s'].value;
	        secondElt = ''+document.all[second].value
	        	+document.all[second+'_h'].value;
	        	+document.all[second+'_i'].value;
	        	+document.all[second+'_s'].value;
			if (firstElt>secondElt) {
				alert(text);
				return false;
			} else return true;
				
		}
	} else return false;
}

/* Taken from: http://simon.incutio.com/archive/2004/05/26/addLoadEvent */
function addLoadEvent(func) {
  var oldonload = window.onload;
  if (typeof window.onload != 'function') {
    window.onload = func;
  } else {
    window.onload = function() {
      if (oldonload) {
        oldonload();
      }
      func();
    }
  }
}

