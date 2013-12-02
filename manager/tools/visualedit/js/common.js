//
// Common functions
//
function getElementsByNameAndClass(n,c,e)
{
	if (!document.getElementsByTagName) {
		return false;
	}
	
	var elements = new Array();
	
	if (e == undefined) {
		e = document;
	}
	
	var col = e.getElementsByTagName(n);
	if (col) {
		for (var i=0; i<col.length; i++) {
			var re = new RegExp("\\b" + c + "\\b");
			if (re.test(col[i].className)) {
				elements.push(col[i]);
			}
		}
	}
	
	return elements;
};


function checkBoxes(formid,sel)
{
	if (!document.getElementById) {
		return false;
	}
	
	var inputs = document.getElementById(formid).elements;
	
	
	for (var i=0; i<inputs.length; i++) {
		if (inputs[i].type == 'checkbox' && !inputs[i].disabled) {
			if (sel == 'invert') {
				inputs[i].checked = !inputs[i].checked;
			} else if (sel == 'none') {
				inputs[i].checked = false;
			} else {
				inputs[i].checked = true;
			}
		}
	}
	
	return false;
}

function getRadioValue(a)
{
	if (a == undefined) { return null; }
	
	for (var i=0; i<a.length; i++) {
		if (a[i].checked) {
			return a[i].value;
		}
	}
	return null;
}

//
// Lock lockable input field, hide info and display unlock icon
function hideLockable()
{
	var loc = getElementsByNameAndClass('div','lockable');
	for (var i=0; i<loc.length; i++)
	{
		var inputs = loc[i].getElementsByTagName('input');
		inputs[0].disabled = true;
		inputs[0].style.width = (inputs[0].clientWidth-14)+'px';
		
		var imgE = document.createElement('img');
		imgE.src = 'images/locker.png';
		imgE.style.position = 'absolute';
		imgE.style.top = '1.7em';
		imgE.style.left = (inputs[0].clientWidth+4)+'px';
		
		inputs[0].parentNode.style.position = 'relative';
		inputs[0].parentNode.insertBefore(imgE,inputs[0].nextSibling);
		
		var notes = getElementsByNameAndClass('p','form-note',loc[i]);
		for (var j=0; j<notes.length; j++) {
			notes[j].style.display = 'none';
		}
		
		imgE.onclick = function() {
			inputs[0].disabled = false;
			this.style.display = 'none';
			inputs[0].style.width = (inputs[0].clientWidth+14)+'px';
			for (var j=0; j<notes.length; j++) {
				notes[j].style.display = 'block';
			}
		}
	}
}

function helpWindow(url)
{
	window.open(url,'dchelp',
	'alwaysRaised=yes,dependent=yes,toolbar=no,height=420,width=380,'+
	'menubar=no,resizable=yes,scrollbars=yes,status=no');
	
	return false;
}

//
// ChainHandler, py Peter van der Beken
//
function chainHandler(obj, handlerName, handler) {
	obj[handlerName] = (function(existingFunction) {
		return function() {
			handler.apply(this, arguments);
			if (existingFunction)
				existingFunction.apply(this, arguments); 
		};
	})(handlerName in obj ? obj[handlerName] : null);
};

//
// On load
//
chainHandler(window,'onload',function() {
	
});