chainHandler(window,'onload',function() {
	var insert_ok = document.getElementById('link-insert-ok');
	var insert_cc = document.getElementById('link-insert-cancel');
	if (insert_cc) {
	
	insert_cc.onclick = function() {
		window.close();
	};
	
	insert_ok.onclick = function() {
		sendClose();
		window.close();
	};
	
	function sendClose() {
		var insert_form = document.getElementById('link-insert-form');
		if (insert_form == undefined) { return; }
		
		var tb = window.opener.the_toolbar;
		var data = tb.elements.link.data;
		
		data.href = tb.stripBaseURL(insert_form.elements.href.value);
		data.title = insert_form.elements.title.value;
		data.hreflang = insert_form.elements.hreflang.value;
		tb.elements.link.fncall[tb.mode].call(tb);
	}
	}
});