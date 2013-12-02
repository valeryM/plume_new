jsToolBar.prototype.can_wwg = (document.designMode != undefined);
jsToolBar.prototype.iframe = null;
jsToolBar.prototype.iwin = null;
jsToolBar.prototype.ibody = null;
jsToolBar.prototype.iframe_css = null;

/* Editor methods
-------------------------------------------------------- */
jsToolBar.prototype.drawToolBar = jsToolBar.prototype.draw;
jsToolBar.prototype.draw = function(mode) {
	mode = mode || 'xhtml';
	
	if (this.can_wwg) {
		this.mode = 'wysiwyg';
		this.drawToolBar('wysiwyg');
		this.initWindow();
	} else {
		this.drawToolBar(mode);
	}
};

jsToolBar.prototype.switchMode = function(mode) {
	mode = mode || 'xhtml';
	
	if (mode == 'xhtml') {
		this.draw(mode);
	} else {
		if (this.wwg_mode) {
			this.syncContents('iframe');
		}
		this.removeEditor();
		this.textarea.style.display = '';
		this.drawToolBar(mode);
	}
};

jsToolBar.prototype.syncContents = function(from) {
	from = from || 'textarea';
	var This = this;
	if (from == 'textarea') {
		initContent();
	} else {
		this.validBlockquote();
		var html = this.tagsoup2xhtml(this.ibody.innerHTML);
		if (html == '<br />') { html = '<p></p>'; }
		this.textarea.value = html;
	}
	
	function initContent() {
		if (!This.iframe.contentWindow.document || !This.iframe.contentWindow.document.body) {
			setTimeout(initContent, 1);
			return;
		}
		This.ibody = This.iframe.contentWindow.document.body;

		if (This.textarea.value != '' && This.textarea.value != '<p></p>') {
			This.ibody.innerHTML = This.textarea.value;
			if (This.ibody.createTextRange) { //cursor at the begin for IE
				var IErange = This.ibody.createTextRange();
				IErange.execCommand("SelectAll");
				IErange.collapse();
				IErange.select();
			}
		} else if (window.navigator.product != undefined && 
							 window.navigator.product == 'Gecko') {
			This.ibody.innerHTML = '<p><br _moz_editor_blogus_node="TRUE" _moz_dirty=""></p>';
		} else {
			var idoc = This.iwin.document;
			var para = idoc.createElement('p');
			para.appendChild(idoc.createTextNode(''));
			while (idoc.body.hasChildNodes()) {
				idoc.body.removeChild(idoc.body.lastChild);
			}
			idoc.body.appendChild(para);
		}
	}
};

jsToolBar.prototype.switchEdit = function() {
	if (this.wwg_mode) {
		this.textarea.style.display = '';
		this.iframe.style.display = 'none';
		this.syncContents('iframe');
		this.drawToolBar('xhtml');
		this.wwg_mode = false;
		this.focusEditor();
	} else {
		this.iframe.style.display = '';
		this.textarea.style.display = 'none';
		this.syncContents('textarea');
		this.drawToolBar('wysiwyg');
		this.wwg_mode = true;
		this.focusEditor();
	}
	this.setSwitcher();
};

/** Creates iframe for editor, inits a blank document
*/
jsToolBar.prototype.initWindow = function() {
	var This = this;
	
	var container = document.createElement('div');
	
	this.iframe = document.createElement('iframe');
	container.appendChild(this.iframe);
	
	this.textarea.parentNode.insertBefore(this.iframe,this.textarea.nextSibling);
	
	this.switcher = document.createElement('ul');
	this.switcher.className = 'jstSwitcher';
	this.editor.appendChild(this.switcher);
	
	this.iframe.height = this.textarea.offsetHeight + 0;
	this.iframe.width = this.textarea.offsetWidth + 0;
	
	if (this.textarea.tabIndex != undefined) {
		this.iframe.tabIndex = this.textarea.tabIndex;
	}
	
	function initIframe() {
		var doc = This.iframe.contentWindow.document;
		if (!doc) {
			setTimeout(initIframe,1);
			return false;
		}
		
		doc.open();
		var html =
		'<html>\n'+
		'<head>\n'+
		'<style type="text/css">'+This.iframe_css+'</style>\n'+
		(This.base_url != '' ? '<base href="'+This.base_url+'" />' : '')+
		'</head>\n'+
		'<body>\n'+
		'</body>\n'+
		'</html>';
		
		doc.write(html);
		doc.close();
		if (document.all) { // for IE
			doc.designMode = 'on';
		}
		
		This.iwin = This.iframe.contentWindow;
		
		This.syncContents('textarea');
		
		if (This.wwg_mode == undefined) {
			This.wwg_mode = true;
		}
		
		if (This.wwg_mode) {
			This.textarea.style.display = 'none';
		} else {
			This.iframe.style.display = 'none';
		}
		
		// update textarea on submit
		if (This.textarea.form) {
			chainHandler(This.textarea.form,'onsubmit', function() {
				if (This.wwg_mode) {
					This.syncContents('iframe');
				}
			});
		}
		
		for (var evt in This.iwinEvents) {
			var event = This.iwinEvents[evt];
			addEvent(This.iwin.document, event.type, function(){event.fn.apply(This, arguments)}, true);
		}
		
		This.setSwitcher();
		This.focusEditor();
		
		return true;
	}
	setTimeout(initIframe,1);
};
jsToolBar.prototype.iwinEvents = {
	block1: {
		type: 'mouseup',
		fn: function(){ this.adjustBlockLevelCombo() }
	},
	block2: {
		type: 'keyup',
		fn: function(){ this.adjustBlockLevelCombo() }
	}
}

/** Insert a mode switcher after editor area
*/
jsToolBar.prototype.switcher_visual_title = 'visual';
jsToolBar.prototype.switcher_source_title = 'source';
jsToolBar.prototype.setSwitcher = function() {
	while (this.switcher.hasChildNodes()) {
		this.switcher.removeChild(this.switcher.firstChild);
	}
	
	var This = this;
	function setLink(title,link) {
		var li = document.createElement('li');
		if (link) {
			var a = document.createElement('a');
			a.href = '#';
			a.editor = This;
			a.onclick = function() { this.editor.switchEdit(); return false; };
			a.appendChild(document.createTextNode(title));
		} else {
			a = document.createTextNode(title);
		}
		
		li.appendChild(a);
		This.switcher.appendChild(li);
	}
	
	setLink(this.switcher_visual_title,!this.wwg_mode);
	setLink(this.switcher_source_title,this.wwg_mode);
};

/** Removes editor area and mode switcher
*/
jsToolBar.prototype.removeEditor = function() {
	if (this.iframe != null) {
		this.iframe.parentNode.removeChild(this.iframe);
		this.iframe = null;
	}
	
	if (this.switcher != undefined && this.switcher.parentNode != undefined) {
		this.switcher.parentNode.removeChild(this.switcher);
	}
};

/** Focus on the editor area
*/
jsToolBar.prototype.focusEditor = function() {
	if (this.wwg_mode) {
		this.iwin.document.designMode = 'on'; // Firefox needs this
		var This = this;
		setTimeout(function() {This.iframe.contentWindow.focus()},1);
	} else {
		this.textarea.focus();
	}
};

/** Resizer
*/
jsToolBar.prototype.resizeSetStartH = function() {
	if (this.wwg_mode && this.iframe != undefined) {
		this.dragStartH = this.iframe.offsetHeight;
		return;
	}
	this.dragStartH = this.textarea.offsetHeight + 0;
};
jsToolBar.prototype.resizeDragMove = function(event) {
	var new_height = (this.dragStartH+event.clientY-this.dragStartY)+'px';
	if (this.iframe != undefined) {
		this.iframe.style.height = new_height;
	}
	this.textarea.style.height = new_height;
};

/* Editing methods
-------------------------------------------------------- */
/** Replaces current selection by given node
*/
jsToolBar.prototype.insertNode = function(node) {
	var range;
	
	if (this.iwin.getSelection) { // Gecko
		var sel = this.iwin.getSelection();
		range = sel.getRangeAt(0);
		
		// deselect all ranges
		sel.removeAllRanges();
		
		// empty range
		range.deleteContents();
		
		// Insert node
		range.insertNode(node);
		
		range.selectNodeContents(node);
		range.setEndAfter(node);
		if (range.endContainer.childNodes.length > range.endOffset &&
		range.endContainer.nodeType != Node.TEXT_NODE) {
			range.setEnd(range.endContainer.childNodes[range.endOffset], 0);
		} else {
			range.setEnd(range.endContainer.childNodes[0]);
		}
		sel.addRange(range);

		sel.collapseToEnd();
	} else { // IE
		// lambda element
		var p = this.iwin.document.createElement('div');
		p.appendChild(node);
		range = this.iwin.document.selection.createRange();
		range.execCommand('delete');
		// insert innerHTML from element
		range.pasteHTML(p.innerHTML);
		range.collapse(false);
		range.select();
	}
	this.iwin.focus();
};

/** Returns a document fragment with selected nodes
*/
jsToolBar.prototype.getSelectedNode = function() {
	//this.focusEditor(); // inutile
	
	if (this.iwin.getSelection) { // Gecko
		var sel = this.iwin.getSelection();
		var range = sel.getRangeAt(0);
		var content = range.cloneContents();
	} else { // IE
		var sel = this.iwin.document.selection;
		var d = this.iwin.document.createElement('div');
		d.innerHTML = sel.createRange().htmlText;
		var content = this.iwin.document.createDocumentFragment();
		for (var i=0; i < d.childNodes.length; i++) {
			content.appendChild(d.childNodes[i].cloneNode(true));
		}
	}
	return content;
};

/** Returns string representation for selected node
*/
jsToolBar.prototype.getSelectedText = function() {
	//this.focusEditor(); // inutile
	
	if (this.iwin.getSelection) { // Gecko
		return this.iwin.getSelection().toString();
	} else { // IE
		var range = this.iwin.document.selection.createRange();
		return range.text;
	}
};

jsToolBar.prototype.getBlockLevel = function() {
	var blockElts = ['p','h1','h2','h3','h4','h5','h6'];

	var range, commonAncestorContainer;
	if (this.iwin.getSelection) { //gecko
		var selection = this.iwin.getSelection();
		range = selection.getRangeAt(0);
		commonAncestorContainer = range.commonAncestorContainer;
		while (commonAncestorContainer.nodeType != 1) {
			commonAncestorContainer = commonAncestorContainer.parentNode;
		}
	} else { //ie
		range = this.iwin.document.selection.createRange();
		commonAncestorContainer = range.parentElement();
	}

	var ancestorTagName = commonAncestorContainer.tagName.toLowerCase();
	while (arrayIndexOf(blockElts, ancestorTagName)==-1 && ancestorTagName!='body') {
		commonAncestorContainer = commonAncestorContainer.parentNode;
		ancestorTagName = commonAncestorContainer.tagName.toLowerCase();
	}
	if (ancestorTagName == 'body') return '';
	else return ancestorTagName;
}
jsToolBar.prototype.adjustBlockLevelCombo = function() {
	var blockLevel = this.getBlockLevel();
	if (blockLevel!='') this.toolNodes.blocks.value = blockLevel;
	else {
		if (this.mode == 'wysiwyg') this.toolNodes.blocks.value = 'none';
		if (this.mode == 'xhtml') this.toolNodes.blocks.value = 'nonebis';
	}
}

/** HTML code cleanup
-------------------------------------------------------- */
jsToolBar.prototype.simpleCleanRegex = new Array(
	/* Remove every tags we don't need */
	[/<meta[\w\W]*?>/gim,''],
	[/<style[\w\W]*?>[\w\W]*?<\/style>/gim, ''],
	[/<\/?font[\w\W]*?>/gim, ''],
	
	
	/* Replacements */
	[/<(\/?)(B|b|STRONG)([\s>\/])/g, "<$1strong$3"],
	[/<(\/?)(I|i|EM)([\s>\/])/g, "<$1em$3"],
	[/<IMG ([^>]*?[^\/])>/gi, "<img $1 />"],
	[/<INPUT ([^>]*?[^\/])>/gi, "<input $1 />"],
	[/<COL ([^>]*?[^\/])>/gi, "<col $1 />"],
	[/<AREA ([^>]*?[^\/])>/gi, "<area $1 />"],
	[/<PARAM ([^>]*?[^\/])>/gi, "<param $1 />"],
	[/<(\/?)U([\s>\/])/gi, "<$1ins$2"],
	[/<(\/?)STRIKE([\s>\/])/gi, "<$1del$2"],
	[/<span style="font-weight: normal;">([\w\W]*?)<\/span>/gm, "$1"],
	[/<span style="font-weight: bold;">([\w\W]*?)<\/span>/gm, "<strong>$1</strong>"],
	[/<span style="font-style: italic;">([\w\W]*?)<\/span>/gm, "<em>$1</em>"],
	[/<span style="text-decoration: underline;">([\w\W]*?)<\/span>/gm, "<ins>$1</ins>"],
	[/<span style="text-decoration: line-through;">([\w\W]*?)<\/span>/gm, "<del>$1</del>"],
	[/<span style="text-decoration: underline line-through;">([\w\W]*?)<\/span>/gm, "<del><ins>$1</ins></del>"],
	[/<span style="(font-weight: bold; ?|font-style: italic; ?){2}">([\w\W]*?)<\/span>/gm, "<strong><em>$2</em></strong>"],
	[/<span style="(font-weight: bold; ?|text-decoration: underline; ?){2}">([\w\W]*?)<\/span>/gm, "<ins><strong>$2</strong></ins>"],
	[/<span style="(font-weight: italic; ?|text-decoration: underline; ?){2}">([\w\W]*?)<\/span>/gm, "<ins><em>$2</em></ins>"],
	[/<span style="(font-weight: bold; ?|text-decoration: line-through; ?){2}">([\w\W]*?)<\/span>/gm, "<del><strong>$2</strong></del>"],
	[/<span style="(font-weight: italic; ?|text-decoration: line-through; ?){2}">([\w\W]*?)<\/span>/gm, "<del><em>$2</em></del>"],
	[/<span style="(font-weight: bold; ?|font-style: italic; ?|text-decoration: underline; ?){3}">([\w\W]*?)<\/span>/gm, "<ins><strong><em>$2</em></strong></ins>"],
	[/<span style="(font-weight: bold; ?|font-style: italic; ?|text-decoration: line-through; ?){3}">([\w\W]*?)<\/span>/gm, "<del><strong><em>$2</em></strong></del>"],
	[/<span style="(font-weight: bold; ?|font-style: italic; ?|text-decoration: underline line-through; ?){3}">([\w\W]*?)<\/span>/gm, "<del><ins><strong><em>$2</em></strong></ins></del>"],
	[/<strong style="font-weight: normal;">([\w\W]*?)<\/strong>/gm, "$1"],
	[/<([a-z]+) style="font-weight: normal;">([\w\W]*?)<\/\1>/gm, "<$1>$2</$1>"],
	[/<([a-z]+) style="font-weight: bold;">([\w\W]*?)<\/\1>/gm, "<$1><strong>$2</strong></$1>"],
	[/<([a-z]+) style="font-style: italic;">([\w\W]*?)<\/\1>/gm, "<$1><em>$2</em></$1>"],
	[/<([a-z]+) style="text-decoration: underline;">([\w\W]*?)<\/\1>/gm, "<ins><$1>$2</$1></ins>"],
	[/<([a-z]+) style="text-decoration: line-through;">([\w\W]*?)<\/\1>/gm, "<del><$1>$2</$1></del>"],
	[/<([a-z]+) style="text-decoration: underline line-through;">([\w\W]*?)<\/\1>/gm, "<del><ins><$1>$2</$1></ins></del>"],
	[/<([a-z]+) style="(font-weight: bold; ?|font-style: italic; ?){2}">([\w\W]*?)<\/\1>/gm, "<$1><strong><em>$3</em></strong></$1>"],
	[/<([a-z]+) style="(font-weight: bold; ?|text-decoration: underline; ?){2}">([\w\W]*?)<\/\1>/gm, "<ins><$1><strong>$3</strong></$1></ins>"],
	[/<([a-z]+) style="(font-weight: italic; ?|text-decoration: underline; ?){2}">([\w\W]*?)<\/\1>/gm, "<ins><$1><em>$3</em></$1></ins>"],
	[/<([a-z]+) style="(font-weight: bold; ?|text-decoration: line-through; ?){2}">([\w\W]*?)<\/\1>/gm, "<del><$1><strong>$3</strong></$1></del>"],
	[/<([a-z]+) style="(font-weight: italic; ?|text-decoration: line-through; ?){2}">([\w\W]*?)<\/\1>/gm, "<del><$1><em>$3</em></$1></del>"],
	[/<([a-z]+) style="(font-weight: bold; ?|font-style: italic; ?|text-decoration: underline; ?){3}">([\w\W]*?)<\/\1>/gm, "<ins><$1><strong><em>$3</em></strong></$1></ins>"],
	[/<([a-z]+) style="(font-weight: bold; ?|font-style: italic; ?|text-decoration: line-through; ?){3}">([\w\W]*?)<\/\1>/gm, "<del><$1><strong><em>$3</em></strong></$1></del>"],
	[/<([a-z]+) style="(font-weight: bold; ?|font-style: italic; ?|text-decoration: underline line-through; ?){3}">([\w\W]*?)<\/\1>/gm, "<del><ins><$1><strong><em>$3</em></strong></$1></ins></del>"],
	/* mise en forme identique contigue */
	[/<\/(strong|em|ins|del|q|code)>(\s*?)<\1>/gim, "$2"],
	[/<(br|BR)>/g, "<br />"],
	/* opera est trop strict ;)) */
	[/([^\s])\/>/g, "$1 />"],
	/* br intempestifs de fin de block */
	[/<br \/>\s*<\/(h1|h2|h3|h4|h5|h6|ul|ol|li|p|blockquote|div)/gi, "</$1"],
	[/<\/(h1|h2|h3|h4|h5|h6|ul|ol|li|p|blockquote)>([^\n\u000B\r\f])/gi, "</$1>\n$2"],
	[/<hr ([^>]*?[^\/])>/gi, "<hr $1 />"],
	[/<HR ([^>]*?[^\/])>/gi, "<hr $1 />"],
	[/<(hr|HR)( style="width: 100%; height: 2px;")?>/g, "<hr />"]
);

/** Cleanup HTML code
*/
jsToolBar.prototype.tagsoup2xhtml = function(html) {
	for (var reg in this.simpleCleanRegex) {
		html = html.replace(this.simpleCleanRegex[reg][0], this.simpleCleanRegex[reg][1]);
	}
	/* tags vides */
	/* note : on tente de ne pas tenir compte des commentaires html, ceux-ci
	   permettent entre autre d'inserer des commentaires conditionnels pour ie */
	while ( /(<[^\/!]>|<[^\/!][^>]*[^\/]>)\s*<\/[^>]*[^-]>/.test(html) ) {
		html = html.replace(/(<[^\/!]>|<[^\/!][^>]*[^\/]>)\s*<\/[^>]*[^-]>/g, "");
	}
	
	/* tous les tags en minuscule */
	html = html.replace(/<(\/?)([A-Z0-9]+)/g,
			function(match0, match1, match2) {
				return "<" + match1 + match2.toLowerCase();
			});
	
	/* IE laisse souvent des attributs sans guillemets */
	var myRegexp = /<[^>]+((\s+\w+\s*=\s*)([^"'][\w\/]*))[^>]*?>/;
	while ( myRegexp.test(html)) {
		html = html.replace(
			myRegexp,
			function (str, val1, val2, val3){
				var tamponRegex = new RegExp(val1);
				return str.replace(tamponRegex, val2+'"'+val3+'"');
			}
		)
	}
	
	/* les navigateurs rajoutent une unite aux longueurs css nulles */
	/* note: a ameliorer ! */
	while ( /(<[^>]+style=(["'])[^>]+[\s:]+)0(pt|px)(\2|\s|;)/.test(html)) {
		html = html.replace(/(<[^>]+style=(["'])[^>]+[\s:]+)0(pt|px)(\2|\s|;)/gi, "$1"+"0$4");
	}
	
	/* correction des fins de lignes : le textarea edite contient des \n
	* le wysiwyg des \r\n , et le textarea mis a jour SANS etre affiche des \r\n ! */
	html = html.replace(/\r\n/g,"\n");
	
	/* Trim */
	html = html.replace(/^\s+/gm,'');
	html = html.replace(/\s+$/gm,'');
	
	return html;
};
jsToolBar.prototype.validBlockquote = function() {
	var blockElts = ['address','blockquote','dl','div','fieldset','form','h1',
	                 'h2','h3','h4','h5','h6','hr','ol','p','pre','table','ul'];
	var BQs = this.iwin.document.getElementsByTagName('blockquote');
	var bqChilds;
	
	for (var bq = 0; bq < BQs.length; bq++) {
		bqChilds = BQs[bq].childNodes;
		var frag = this.iwin.document.createDocumentFragment();
		for (var i = (bqChilds.length-1); i >= 0; i--) {
			if (bqChilds[i].nodeType == 1 && // Node.ELEMENT_NODE
			    arrayIndexOf(blockElts, bqChilds[i].tagName.toLowerCase()) >= 0)
			{
				if (frag.childNodes.length > 0) {
					var p = this.iwin.document.createElement('p');
					p.appendChild(frag);
					BQs[bq].replaceChild(p, bqChilds[i+1]);
					frag = this.iwin.document.createDocumentFragment();
				}
			} else {
				if (frag.childNodes.length > 0) BQs[bq].removeChild(bqChilds[i+1]);
				frag.insertBefore(bqChilds[i].cloneNode(true), frag.firstChild);
			}
		}
		if (frag.childNodes.length > 0) {
			var p = this.iwin.document.createElement('p');
			p.appendChild(frag);
			BQs[bq].replaceChild(p, bqChilds[0]);
		}
	}
}

/* Removing text formating */
jsToolBar.prototype.removeFormatRegexp = new Array(
	[/(<[a-z][^>]*)margin\s*:[^;]*;/mg, "$1"],
	[/(<[a-z][^>]*)margin-bottom\s*:[^;]*;/mg, "$1"],
	[/(<[a-z][^>]*)margin-left\s*:[^;]*;/mg, "$1"],
	[/(<[a-z][^>]*)margin-right\s*:[^;]*;/mg, "$1"],
	[/(<[a-z][^>]*)margin-top\s*:[^;]*;/mg, "$1"],
	
	[/(<[a-z][^>]*)padding\s*:[^;]*;/mg, "$1"],
	[/(<[a-z][^>]*)padding-bottom\s*:[^;]*;/mg, "$1"],
	[/(<[a-z][^>]*)padding-left\s*:[^;]*;/mg, "$1"],
	[/(<[a-z][^>]*)padding-right\s*:[^;]*;/mg, "$1"],
	[/(<[a-z][^>]*)padding-top\s*:[^;]*;/mg, "$1"],
	
	[/(<[a-z][^>]*)font\s*:[^;]*;/mg, "$1"],
	[/(<[a-z][^>]*)font-family\s*:[^;]*;/mg, "$1"],
	[/(<[a-z][^>]*)font-size\s*:[^;]*;/mg, "$1"],
	[/(<[a-z][^>]*)font-style\s*:[^;]*;/mg, "$1"],
	[/(<[a-z][^>]*)font-variant\s*:[^;]*;/mg, "$1"],
	[/(<[a-z][^>]*)font-weight\s*:[^;]*;/mg, "$1"],
	
	[/(<[a-z][^>]*)color\s*:[^;]*;/mg, "$1"]
);

jsToolBar.prototype.removeTextFormating = function(html) {
	for (var reg in this.removeFormatRegexp) {
		html = html.replace(this.removeFormatRegexp[reg][0], this.removeFormatRegexp[reg][1]);
	}
	
	html = this.tagsoup2xhtml(html);
	html = html.replace(/style="\s*?"/mgi,'');
	return html;
};

/** Toolbar elements
-------------------------------------------------------- */
jsToolBar.prototype.elements.blocks.options.none.fn.wysiwyg = function() {
	// rajouter de quoi supprimer le paragraphe ou header
	this.iwin.focus();
};
jsToolBar.prototype.elements.blocks.options.p.fn.wysiwyg = function() {
	this.iwin.document.execCommand('formatblock',false,'<p>');
	this.iwin.focus();
};
//jsToolBar.prototype.elements.blocks.options.h1.fn.wysiwyg = function() {
//	this.iwin.document.execCommand('formatblock',false,'<h1>');
//	this.iwin.focus();
//};
//jsToolBar.prototype.elements.blocks.options.h2.fn.wysiwyg = function() {
//	this.iwin.document.execCommand('formatblock',false,'<h2>');
//	this.iwin.focus();
//};
jsToolBar.prototype.elements.blocks.options.h3.fn.wysiwyg = function() {
	this.iwin.document.execCommand('formatblock',false,'<h3>');
	this.iwin.focus();
};
jsToolBar.prototype.elements.blocks.options.h4.fn.wysiwyg = function() {
	this.iwin.document.execCommand('formatblock',false,'<h4>');
	this.iwin.focus();
};
jsToolBar.prototype.elements.blocks.options.h5.fn.wysiwyg = function() {
	this.iwin.document.execCommand('formatblock',false,'<h5>');
	this.iwin.focus();
};
jsToolBar.prototype.elements.blocks.options.h6.fn.wysiwyg = function() {
	this.iwin.document.execCommand('formatblock',false,'<h6>');
	this.iwin.focus();
};

jsToolBar.prototype.elements.strong.fn.wysiwyg = function() {
	//this.focusEditor();// pas besoin de sortir l'artillerie pour ces cas, plutot this.iwin.focus(), et c'est plus logique aprés le traitement
	this.iwin.document.execCommand('bold', false, null);
	this.iwin.focus();
};

jsToolBar.prototype.elements.em.fn.wysiwyg = function() {
	this.iwin.document.execCommand('italic', false, null);
	this.iwin.focus();
};

//jsToolBar.prototype.elements.ins.fn.wysiwyg = function() {
//	this.iwin.document.execCommand('underline', false, null);
//	this.iwin.focus();
//};

jsToolBar.prototype.elements.del.fn.wysiwyg = function() {
	this.iwin.document.execCommand('strikethrough', false, null);
	this.iwin.focus();
};

jsToolBar.prototype.elements.quote.fn.wysiwyg = function() {
	var n = this.getSelectedNode();
	var q = this.iwin.document.createElement('q');
	q.appendChild(n);
	this.insertNode(q);
};

jsToolBar.prototype.elements.code.fn.wysiwyg = function() {
	var n = this.getSelectedNode();
	var code = this.iwin.document.createElement('code');
	code.appendChild(n);
	this.insertNode(code);
};

jsToolBar.prototype.elements.br.fn.wysiwyg = function() {
	var n = this.iwin.document.createElement('br');
	this.insertNode(n);
};

jsToolBar.prototype.elements.blockquote.fn.wysiwyg = function() {
	var n = this.getSelectedNode();
	var q = this.iwin.document.createElement('blockquote');
	q.appendChild(n);
	this.insertNode(q);
};

jsToolBar.prototype.elements.pre.fn.wysiwyg = function() {
	this.iwin.document.execCommand('formatblock',false,'<pre>');
	this.iwin.focus();
};

jsToolBar.prototype.elements.ul.fn.wysiwyg = function() {
	this.iwin.document.execCommand('insertunorderedlist',false,null);
	this.iwin.focus();
};

jsToolBar.prototype.elements.ol.fn.wysiwyg = function() {
	this.iwin.document.execCommand('insertorderedlist',false,null);
	this.iwin.focus();
};

jsToolBar.prototype.elements.link.fn.wysiwyg = function() {
	var href, hreflang;
	var range, commonAncestorContainer;
	if (this.iwin.getSelection) { //gecko
		var selection = this.iwin.getSelection();
		range = selection.getRangeAt(0);
		commonAncestorContainer = range.commonAncestorContainer;
		while (commonAncestorContainer.nodeType != 1) {
			commonAncestorContainer = commonAncestorContainer.parentNode;
		}
	} else { //ie
		range = this.iwin.document.selection.createRange();
		commonAncestorContainer = range.parentElement();
	}

	var ancestorTagName = commonAncestorContainer.tagName.toLowerCase();
	while (ancestorTagName!='a' && ancestorTagName!='body') {
		commonAncestorContainer = commonAncestorContainer.parentNode;
		ancestorTagName = commonAncestorContainer.tagName.toLowerCase();
	}
	
	// Update or remove link?
	if (ancestorTagName == 'a') {
		href = commonAncestorContainer.href || '';
		hreflang = commonAncestorContainer.hreflang || '';
	}
	
	href = window.prompt(this.elements.link.href_prompt,href);
	
	// Remove link
	if (ancestorTagName == 'a' && href=='') {
		this.iwin.document.execCommand('unlink',false,null);
		this.iwin.focus();
		return;
	}
	if (!href) return; // user cancel
	
	hreflang = window.prompt(this.elements.link.hreflang_prompt, hreflang);
	
	// Update link
	if (ancestorTagName == 'a' && href) {
		commonAncestorContainer.setAttribute('href', href);
		if (hreflang) {
			commonAncestorContainer.setAttribute('hreflang', hreflang);
		} else {
			commonAncestorContainer.removeAttribute('hreflang');
		}
		return;
	}
	
	// Create link
	var n = this.getSelectedNode();
	var a = this.iwin.document.createElement('a');
	a.href = href;
	if (hreflang) a.setAttribute('hreflang',hreflang);
	a.appendChild(n);
	this.insertNode(a);
};



// Remove format and Toggle
jsToolBar.prototype.elements.removeFormat = {
	type: 'button',
	title: 'Remove text formating',
	fn: {}
}
jsToolBar.prototype.elements.removeFormat.disabled = !jsToolBar.prototype.can_wwg;
jsToolBar.prototype.elements.removeFormat.fn.xhtml = function() {
	var html = this.textarea.value;
	html = this.removeTextFormating(html);
	this.textarea.value = html;
}
jsToolBar.prototype.elements.removeFormat.fn.wysiwyg = function() {
	var html = this.iwin.document.body.innerHTML;
	html = this.removeTextFormating(html);
	this.iwin.document.body.innerHTML = html;
};
/** Utilities
-------------------------------------------------------- */
function arrayIndexOf(aArray, aValue){
	if (typeof Array.indexOf == 'function') {
		return aArray.indexOf(aValue);
	} else {
		var index = -1;
		var l = aArray.length;
		for (var i = 0; i < l ; i++) {
			if (aArray[i] === aValue) {
				index = i;
				break;
			}
		}
		return index;
	}
}
function addEvent(obj, evType, fn, useCapture) {
	if (obj.addEventListener){
		obj.addEventListener(evType, fn, useCapture);
		return true;
	} else if (obj.attachEvent){
		var r = obj.attachEvent("on"+evType, fn);
		return r;
	} else {
		return false;
	}
}
