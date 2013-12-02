function jsToolBar(textarea) {
	if (!document.createElement) { return; }
	
	if (!textarea) { return; }
	
	if ((typeof(document["selection"]) == "undefined")
	&& (typeof(textarea["setSelectionRange"]) == "undefined")) {
		return;
	}
	
	this.textarea = textarea;
	
	this.editor = document.createElement('div');
	this.editor.className = 'jstEditor';
	
	this.textarea.parentNode.insertBefore(this.editor,this.textarea);
	this.editor.appendChild(this.textarea);
	
	this.toolbar = document.createElement("div");
	this.toolbar.className = 'jstElements';
	this.editor.parentNode.insertBefore(this.toolbar,this.editor);
	
	// Dragable resizing (only for gecko)
	if (this.editor.addEventListener)
	{
		this.handle = document.createElement('div');
		this.handle.className = 'jstHandle';
		var dragStart = this.resizeDragStart;
		var This = this;
		this.handle.addEventListener('mousedown',function(event) { dragStart.call(This,event); },false);
		
		this.editor.parentNode.insertBefore(this.handle,this.editor.nextSibling);
	}
	
	this.context = null;
	this.toolNodes = {}; // lorsque la toolbar est dessinée , cet objet est garni 
					// de raccourcis vers les éléments DOM correspondants aux outils.
}

function jsButton(title, fn, scope, className) {
	this.title = title || null;
	this.fn = fn || function(){};
	this.scope = scope || null;
	this.className = className || null;
}
jsButton.prototype.draw = function() {
	if (!this.scope) return null;
	
	var button = document.createElement('button');
	button.setAttribute('type','button');
	if (this.className) button.className = this.className;
	button.title = this.title;
	var span = document.createElement('span');
	span.appendChild(document.createTextNode(this.title));
	button.appendChild(span);
	
	if (this.icon != undefined) {
		button.style.backgroundImage = 'url('+this.icon+')';
	}
	if (typeof(this.fn) == 'function') {
		var This = this;
		button.onclick = function() { try { This.fn.apply(This.scope, arguments) } catch (e) {} return false; };
	}
	return button;
}

function jsSpace(id) {
	this.id = id || null;
	this.width = null;
}
jsSpace.prototype.draw = function() {
	var span = document.createElement('span');
	if (this.id) span.id = this.id;
	span.appendChild(document.createTextNode(String.fromCharCode(160)));
	span.className = 'jstSpacer';
	if (this.width) span.style.marginRight = this.width+'px';
	
	return span;
} 

function jsCombo(title, options, scope, className) {
	this.title = title || null;
	this.options = options || null;
	this.scope = scope || null;
	this.className = className || null;
}
jsCombo.prototype.draw = function() {
	if (!this.scope || !this.options) return null;

	var select = document.createElement('select');
	if (this.className) select.className = className;
	select.title = this.title;
	
	for (var o in this.options) {
	// aOption == {label: aLabel, fn: aFunction}
		var opt = this.options[o];
		var option = document.createElement('option');
		option.value = o;
		option.appendChild(document.createTextNode(opt.label));
		select.appendChild(option);
	}

	var This = this;
	select.onchange = function() {
	// pour l'instant on associe une fonction par option.
	// l'autre solution est d'attacher une fonction globable auxquelle on passe
	// un argument dépendant de l'option sélectionnée ( ce serait peut être plus
	// léger en lignes de code...mais pas sûr..)
		var option = This.options[this.value];
		if (typeof option.fn != 'function') return false;
		try { 
			option.fn.apply(This.scope);
			//option.fn.apply(This.scope, arguments);
		} catch (e) { alert(e); }

		return false;
	}

	return select;
}


jsToolBar.prototype = {
	base_url: '',
	mode: 'xhtml',
	elements: {},
	
	getMode: function() {
		return this.mode;
	},
	
	setMode: function(mode) {
		this.mode = mode || 'xhtml';
	},
	
	switchMode: function(mode) {
		mode = mode || 'xhtml';
		this.draw(mode);
	},
	
	button: function(toolName) {
		var tool = this.elements[toolName];
		if (typeof tool.fn[this.mode] != 'function') return null;
		var b = new jsButton(tool.title, tool.fn[this.mode], this, 'jstb_'+toolName);
		if (tool.icon != undefined) b.icon = tool.icon;
		return b;
	},
	space: function(toolName) {
		var tool = new jsSpace(toolName)
		if (this.elements[toolName].width !== undefined)
			tool.width = this.elements[toolName].width;
		return tool;
	},
	combo: function(toolName) {
		var tool = this.elements[toolName];
		var options = {};
		var length = 0;
		for (var o in tool.options) {
			var opt = tool.options[o];
			if (typeof opt.fn[this.mode] != 'function') continue;
			options[o] = {label: opt.label, fn: opt.fn[this.mode]};
			length++;
		}
		if (length == 0) return null;

		return new jsCombo(tool.title, options, this);
	},
	draw: function(mode) {
		this.setMode(mode);
		
		// Empty toolbar
		while (this.toolbar.hasChildNodes()) {
			this.toolbar.removeChild(this.toolbar.firstChild)
		}
		this.toolNodes = {}; // vide les raccourcis DOM/**/
		
		// Draw toolbar elements
		var b, tool, newTool;
		
		for (var i in this.elements) {
			b = this.elements[i];

			var disabled =
			b.type == undefined || b.type == ''
			|| (b.disabled != undefined && b.disabled)
			|| (b.context != undefined && b.context != null && b.context != this.context);
			
			if (!disabled && typeof this[b.type] == 'function') {
				tool = this[b.type](i);
				if (tool) newTool = tool.draw();
				if (newTool) {
					this.toolNodes[i] = newTool; //mémorise l'accès DOM pour usage éventuel ultérieur
					this.toolbar.appendChild(newTool);
				}
			}
		}
	},
	
	singleTag: function(stag,etag) {
		stag = stag || null;
		etag = etag || stag;
		
		if (!stag || !etag) { return; }
		
		this.encloseSelection(stag,etag);
	},
	
	encloseSelection: function(prefix, suffix, fn) {
		this.textarea.focus();
		
		prefix = prefix || '';
		suffix = suffix || '';
		
		var start, end, sel, scrollPos, subst, res;
		
		if (typeof(document["selection"]) != "undefined") {
			sel = document.selection.createRange().text;
		} else if (typeof(this.textarea["setSelectionRange"]) != "undefined") {
			start = this.textarea.selectionStart;
			end = this.textarea.selectionEnd;
			scrollPos = this.textarea.scrollTop;
			sel = this.textarea.value.substring(start, end);
		}
		
		if (sel.match(/ $/)) { // exclude ending space char, if any
			sel = sel.substring(0, sel.length - 1);
			suffix = suffix + " ";
		}
		
		if (typeof(fn) == 'function') {
			res = (sel) ? fn.call(this,sel) : fn('');
		} else {
			res = (sel) ? sel : '';
		}
		
		subst = prefix + res + suffix;
		
		if (typeof(document["selection"]) != "undefined") {
			var range = document.selection.createRange().text = subst;
			this.textarea.caretPos -= suffix.length;
		} else if (typeof(this.textarea["setSelectionRange"]) != "undefined") {
			this.textarea.value = this.textarea.value.substring(0, start) + subst +
			this.textarea.value.substring(end);
			if (sel) {
				this.textarea.setSelectionRange(start + subst.length, start + subst.length);
			} else {
				this.textarea.setSelectionRange(start + prefix.length, start + prefix.length);
			}
			this.textarea.scrollTop = scrollPos;
		}
	},
	
	stripBaseURL: function(url) {
		if (this.base_url != '') {
			var pos = url.indexOf(this.base_url);
			if (pos == 0) {
				url = url.substr(this.base_url.length);
			}
		}
		
		return url;
	}
};

/** Resizer
-------------------------------------------------------- */
jsToolBar.prototype.resizeSetStartH = function() {
	this.dragStartH = this.textarea.offsetHeight + 0;
};
jsToolBar.prototype.resizeDragStart = function(event) {
	var This = this;
	this.dragStartY = event.clientY;
	this.resizeSetStartH();
	document.addEventListener('mousemove', this.dragMoveHdlr=function(event){This.resizeDragMove(event);}, false);
	document.addEventListener('mouseup', this.dragStopHdlr=function(event){This.resizeDragStop(event);}, false);
};

jsToolBar.prototype.resizeDragMove = function(event) {
	this.textarea.style.height = (this.dragStartH+event.clientY-this.dragStartY)+'px';
};

jsToolBar.prototype.resizeDragStop = function(event) {
	document.removeEventListener('mousemove', this.dragMoveHdlr, false);
	document.removeEventListener('mouseup', this.dragStopHdlr, false);
};

// Elements definition ------------------------------------
// block format (paragraph, headers)
jsToolBar.prototype.elements.blocks = {
	type: 'combo',
	title: 'Block Format',
	options: {
		none: { label: '-- none --', fn: {} }, // only for wysiwyg mode
		nonebis: {label: '- Block format -', fn: {} }, // only for xhtml mode
		p: { label: 'Paragraph', fn: {} },
		//h1: { label: 'Header 1', fn: {} },
		//h2: { label: 'Header 2', fn: {} },
		h3: { label: 'Header 3', fn: {} },
		h4: { label: 'Header 4', fn: {} },
		h5: { label: 'Header 5', fn: {} },
		h6: { label: 'Header 6', fn: {} }
	}
}
jsToolBar.prototype.elements.blocks.options.nonebis.fn.xhtml = function() {
	this.textarea.focus();
};
jsToolBar.prototype.elements.blocks.options.p.fn.xhtml = function() {
	this.singleTag('<p>','</p>');
};
//jsToolBar.prototype.elements.blocks.options.h1.fn.xhtml = function() {
//	this.singleTag('<h1>','</h1>');
//};
//jsToolBar.prototype.elements.blocks.options.h2.fn.xhtml = function() {
//	this.singleTag('<h2>','</h2>');
//};
jsToolBar.prototype.elements.blocks.options.h3.fn.xhtml = function() {
	this.singleTag('<h3>','</h3>');
};
jsToolBar.prototype.elements.blocks.options.h4.fn.xhtml = function() {
	this.singleTag('<h4>','</h4>');
};
jsToolBar.prototype.elements.blocks.options.h5.fn.xhtml = function() {
	this.singleTag('<h5>','</h5>');
};
jsToolBar.prototype.elements.blocks.options.h6.fn.xhtml = function() {
	this.singleTag('<h6>','</h6>');
};

jsToolBar.prototype.elements.blocks.options.nonebis.fn.wiki = function() {
	this.textarea.focus();
};
//jsToolBar.prototype.elements.blocks.options.h2.fn.wiki = function() {
//	this.encloseSelection('!!!');
//};
jsToolBar.prototype.elements.blocks.options.h3.fn.wiki = function() {
	this.encloseSelection('!!!');
};
jsToolBar.prototype.elements.blocks.options.h4.fn.wiki = function() {
	this.encloseSelection('!!');
};
jsToolBar.prototype.elements.blocks.options.h5.fn.wiki = function() {
	this.encloseSelection('!');
};

// spacer
jsToolBar.prototype.elements.space0 = {type: 'space'}

// strong
jsToolBar.prototype.elements.strong = {
	type: 'button',
	title: 'Strong emphasis',
	fn: {
		wiki: function() { this.singleTag('__') },
		xhtml: function() { this.singleTag('<strong>','</strong>') }
	}
}

// em
jsToolBar.prototype.elements.em = {
	type: 'button',
	title: 'Emphasis',
	fn: {
		wiki: function() { this.singleTag("''") },
		xhtml: function() { this.singleTag('<em>','</em>') }
	}
}

// ins
jsToolBar.prototype.elements.ins = {
	type: 'button',
	title: 'Inserted',
	fn: {
		// wiki: function() { this.singleTag('++') },
		xhtml: function() { this.singleTag('<ins>','</ins>') }
	}
}


// del
jsToolBar.prototype.elements.del = {
	type: 'button',
	title: 'Deleted',
	fn: {
		// wiki: function() { this.singleTag('--') },
		xhtml: function() { this.singleTag('<del>','</del>') }
	}
}

// quote
jsToolBar.prototype.elements.quote = {
	type: 'button',
	title: 'Inline quote',
	fn: {
		wiki: function() { this.singleTag('^^','^^') },
		xhtml: function() { this.singleTag('<q>','</q>') }
	}
}

// code
jsToolBar.prototype.elements.code = {
	type: 'button',
	title: 'Code',
	fn: {
		wiki: function() { this.singleTag('@@') },
		xhtml: function() { this.singleTag('<code>','</code>')}
	}
}

// span
jsToolBar.prototype.elements.span = {
	type: 'button',
	title: 'Generic inline tag',
	style_prompt: 'CSS style (leave blank if no style):',
	fn: {},
	prompt: function(sty) {
			sty = sty || '';
			return window.prompt(this.elements.span.style_prompt,sty);
		}
};
jsToolBar.prototype.elements.span.fn.xhtml = function() {
	var sty = this.elements.span.prompt.call(this);
	if (sty) {
		this.encloseSelection('','',function(str) {
			if (str) {
				return '<span style="'+sty+'">'+str+'</span>';
			} else {
				return '<span style="'+sty+'"></span>';
			}
		});
	}
	else {
		this.encloseSelection('','',function(str) {
			if (str) {
				return '<span>'+str+'</span>';
			} else {
				return '<span></span>';
			}
		});
	}
};

// spacer
jsToolBar.prototype.elements.space1 = {type: 'space'}

// br
jsToolBar.prototype.elements.br = {
	type: 'button',
	title: 'Line break',
	fn: {
		wiki: function() { this.encloseSelection("%%%\n",'') },
		xhtml: function() { this.encloseSelection("<br />\n",'')}
	}
}

// spacer
jsToolBar.prototype.elements.space2 = {type: 'space'}

// blockquote
jsToolBar.prototype.elements.blockquote = {
	type: 'button',
	title: 'Blockquote',
	fn: {
		xhtml: function() { this.singleTag('<blockquote>','</blockquote>') },
		wiki: function() {
			this.encloseSelection("\n",'',
			function(str) {
				str = str.replace(/\r/g,'');
				return '> '+str.replace(/\n/g,"\n> ");
			});
		}
	}
}

// pre
jsToolBar.prototype.elements.pre = {
	type: 'button',
	title: 'Preformated text',
	fn: {
		wiki: function() {
			this.encloseSelection("\n",'',
			function(str) {
				str = str.replace(/\r/g,'');
				return ' '+str.replace(/\n/g,"\n ");
			});
		}, //wiki: function() { this.singleTag("///\n","\n///") },
		xhtml: function() { this.singleTag('<pre>','</pre>') }
	}
}

// div
jsToolBar.prototype.elements.div = {
	type: 'button',
	title: 'Generic block tag',
	style_prompt: 'CSS style (leave blank if no style):',
	fn: {},
	prompt: function(sty) {
			sty = sty || '';
			return window.prompt(this.elements.div.style_prompt,sty);
		}
};
jsToolBar.prototype.elements.div.fn.xhtml = function() {
	var sty = this.elements.div.prompt.call(this);
	if (sty) {
		this.encloseSelection('','',function(str) {
			if (str) {
				return '\n<div style="'+sty+'">\n'+str+'\n</div>\n';
			} else {
				return '\n<div style="'+sty+'">\n</div>\n';
			}
		});
	}
	else
		{
		this.encloseSelection('','',function(str) {
			if (str) {
				return '\n<div>\n'+str+'\n</div>\n';
			} else {
				return '\n<div>\n</div>\n';
			}
		});
	}
};

// ul
jsToolBar.prototype.elements.ul = {
	type: 'button',
	title: 'Unordered list',
	fn: {
		wiki: function() {
			this.encloseSelection('','',function(str) {
				str = str.replace(/\r/g,'');
				return '* '+str.replace(/\n/g,"\n* ");
			});
		},
		xhtml: function() {
			this.encloseSelection('','',function(str) {
				str = str.replace(/\r/g,'');
				str = str.replace(/\n/g,"</li>\n <li>");
				return "<ul>\n <li>"+str+"</li>\n</ul>";
			});
		}
	}
}

// ol
jsToolBar.prototype.elements.ol = {
	type: 'button',
	title: 'Ordered list',
	fn: {
		wiki: function() {
			this.encloseSelection('','',function(str) {
				str = str.replace(/\r/g,'');
				return '# '+str.replace(/\n/g,"\n# ");
			});
		},
		xhtml: function() {
			this.encloseSelection('','',function(str) {
				str = str.replace(/\r/g,'');
				str = str.replace(/\n/g,"</li>\n <li>");
				return "<ol>\n <li>"+str+"</li>\n</ol>";
			});
		}
	}
}

// spacer
jsToolBar.prototype.elements.space3 = {type: 'space'}

// link
jsToolBar.prototype.elements.link = {
	type: 'button',
	title: 'Link',
	fn: {},
	href_prompt: 'Please give page URL:',
	hreflang_prompt: 'Language of this page:',
	default_hreflang: '',
	prompt: function(href,hreflang) {
		href = href || '';
		hreflang = hreflang || this.elements.link.default_hreflang;
		
		href = window.prompt(this.elements.link.href_prompt,href);
		if (!href) { return false; }
		
		hreflang = window.prompt(this.elements.link.hreflang_prompt,
		hreflang);
		
		return { href: this.stripBaseURL(href), hreflang: hreflang };
	}
}

jsToolBar.prototype.elements.link.fn.xhtml = function() {
	var link = this.elements.link.prompt.call(this);
	if (link) {
		var stag = '<a href="'+link.href+'"';
		if (link.hreflang) { stag = stag+' hreflang="'+link.hreflang+'"'; }
		if (link.title) { stag = stag+' title="'+link.title+'"'; }
		stag = stag+'>';
		var etag = '</a>';
		
		this.encloseSelection(stag,etag);
	}
};
jsToolBar.prototype.elements.link.fn.wiki = function() {
	var link = this.elements.link.prompt.call(this);
	if (link) {
		var stag = '[';
		var etag = '|'+link.href;
		if (link.hreflang) { etag = etag+'|'+link.hreflang; }
		etag = etag+']';
		
		this.encloseSelection(stag,etag);
	}
};

// img
jsToolBar.prototype.elements.img = {
		type: 'button',
		title: 'External image',
		src_prompt: 'Please give image URL:',
		fn: {},
		prompt: function(src) {
			src = src || '';
			return this.stripBaseURL(window.prompt(this.elements.img.src_prompt,src));
		}
};
jsToolBar.prototype.elements.img.fn.xhtml = function() {
	var src = this.elements.img.prompt.call(this);
	if (src) {
		this.encloseSelection('','',function(str) {
			if (str) {
				return '<img src="'+src+'" alt="'+str+'" />';
			} else {
				return '<img src="'+src+'" alt="" />';
			}
		});
	}
};
jsToolBar.prototype.elements.img.fn.wiki = function() {
	var src = this.elements.img.prompt.call(this);
	if (src) {
		this.encloseSelection('','',function(str) {
			if (str) {
				return '(('+src+'|'+str+'))';
			} else {
				return '(('+src+'))';
			}
		});
	}
};
