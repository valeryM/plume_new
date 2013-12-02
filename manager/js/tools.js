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
        document.formPost.c_path.value = idArray[document.formPost.cat_id.value];
    }
}

function setUrl(titleField, urlField, param, param2)
{
    url   = document.getElementById(urlField);
    title = document.getElementById(titleField);
    if (param == 'cat') {
        if (document.getElementById('quirk_category_path').value == 0) {
            urlFromTitle = titleToUrl(title.value,'cat');
            url.value = param2[document.formPost.cat_id.value] + urlFromTitle + '/';
        }
    } else {
        if (param2 == 0) {
            url.value = titleToUrl(title.value,'art');
        }
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
	if(document.getElementById) {
		element = document.getElementById(id);
		img = document.getElementById('img_' + id);
	} else if(document.all) {
		element = document.all[id];
		img = document.all['img_' + id];
	} else return;

	if(element.style) {
		if(mode == 0) {
			if(element.style.display == 'block' ) {
				element.style.display = 'none';
				img.src = 'themes/'+pxThemeid+'/images/plus.png';
			} else {
				element.style.display = 'block';
				img.src = 'themes/'+pxThemeid+'/images/minus.png';
			}
		} else if(mode == 1) {
			element.style.display = 'block';
			img.src = 'themes/'+pxThemeid+'/images/minus.png';
		} else if(mode == -1) {
			element.style.display = 'none';
			img.src = 'themes/'+pxThemeid+'/images/plus.png';
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
			if(element.style.display == 'inline' ) {
				element.style.display = 'none';
			} else {
				element.style.display = 'inline';
			}
		} else if(mode == 1) {
			element.style.display = 'inline';
		} else if(mode == -1) {
			element.style.display = 'none';
		}
	}
        return false;
}


function modifDateRefererTo(Referer,dt_field) {
	
	if(document.getElementById) {
		element = document.getElementById(Referer);
		fieldDate = document.getElementById(dt_field);
	} else if(document.all) {
		element = document.all[Referer];
		fieldDate = document.all[dt_field];
	} else return;
	
	statut = element.checked;
	
	today = new Date();
	if (statut == false) {
	    mois = '0'+ (today.getMonth()+1);
	    mois = mois.substr(mois.length-2);
	    fieldDate.value = today.getFullYear().toString()+mois+today.getDate().toString();
	} else  {
		fieldDate.value = '99991231';
	}
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

function mOpenClose(idArray,mode)
{
	for(var i=0;i<idArray.length;i++)
	{
		openClose(idArray[i],mode);
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

    if (form.n_content)           form.n_content.value += image;
    else if (form.a_description)  form.a_description.value += image;
    else if (form.a_page_content) form.a_page_content.value += image;
    else if (form.c_description)  form.c_description.value += image;

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
		}
	} else {
		alert('Tools::isGreater : Erreur de référence !');
		return false;
	}
	//alert(firstElt+'-'+secondElt);
	if (firstElt>secondElt) {
		alert(text);
		return false;
	} else return true;
}