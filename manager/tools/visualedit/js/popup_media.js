chainHandler(window,'onload',function() {
	var toolBar = window.opener.the_toolbar.textarea;
	
	chainHandler(multiPartPage.prototype,'updateDivision',function(index,div)
	{
		if (index == 0 && !div.shown) {
			var insert_ok = document.getElementById('media-insert-ok');
			var insert_cc = document.getElementById('media-insert-cancel');
			
			insert_cc.onclick = function() {
				window.close();
			};
			
			insert_ok.onclick = function() {
				sendClose();
				window.close();
			};
		}
	});
	
	function sendClose() {
		var insert_form = document.getElementById('media-insert-form');
		if (insert_form == undefined) { return; }
		
		var tb = window.opener.the_toolbar;
		tb.elements.img_select.data.src = tb.stripBaseURL(getRadioValue(insert_form.elements.src));
		tb.elements.img_select.data.alignment = getRadioValue(insert_form.elements.alignment);
		tb.elements.img_select.data.link =  getRadioValue(insert_form.elements.insertion) == 'link';
		tb.elements.img_select.data.title =  insert_form.elements.title.value;
		tb.elements.img_select.data.url = tb.stripBaseURL(insert_form.elements.url.value);
		tb.elements.img_select.fncall[tb.mode].call(tb);
	}
});