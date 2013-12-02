/* ***** BEGIN LICENSE BLOCK *****
 * This file is part of DotClear.
 * Copyright (c) 2004 Olivier Meunier and contributors. All rights
 * reserved.
 *
 * DotClear is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * DotClear is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with DotClear; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * ***** END LICENSE BLOCK ***** */
 
function btTab() {
	var wtag = "\t";
	
	encloseSelection("",'',
		function(str) {
				str = str.replace(/\r/g,'');
				return wtag+str.replace(/\n/g,"\n"+wtag);
		});

}
function encloseSelection(prefix, suffix, fn) {
	var textarea = document.getElementById("t_content");
	textarea.focus();
	var start, end, sel, scrollPos, subst;
	
	if (typeof(document["selection"]) != "undefined") {
		sel = document.selection.createRange().text;
	} else if (typeof(textarea["setSelectionRange"]) != "undefined") {
		start = textarea.selectionStart;
		end = textarea.selectionEnd;
		scrollPos = textarea.scrollTop;
		sel = textarea.value.substring(start, end);
	}
	
	if (sel.match(/ $/)) { // exclude ending space char, if any
		sel = sel.substring(0, sel.length - 1);
		suffix = suffix + " ";
	}
	
	if (typeof(fn) == 'function') {
		var res = (sel) ? fn(sel) : fn('');
	} else {
		var res = (sel) ? sel : '';
	}
	
	subst = prefix + res + suffix;
	
	if (typeof(document["selection"]) != "undefined") {
		var range = document.selection.createRange().text = subst;
		textarea.caretPos -= suffix.length;
	} else if (typeof(textarea["setSelectionRange"]) != "undefined") {
		textarea.value = textarea.value.substring(0, start) + subst +
		textarea.value.substring(end);
		if (sel) {
			textarea.setSelectionRange(start + subst.length, start + subst.length);
		} else {
			textarea.setSelectionRange(start + prefix.length, start + prefix.length);
		}
		textarea.scrollTop = scrollPos;
	}
}
