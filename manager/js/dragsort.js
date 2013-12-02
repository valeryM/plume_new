/* Drag and drop tools by Tim Taylor. Code is under public license.
http://tool-man.org/examples/sorting.html */

// TODO: refactor away duplicationg in DragSort and DragSortX
var dragSort = {
	getSortableItems : function(e) {
		var items = e.getElementsByTagName('div');
		var res = Array();
		for (var i = 0; i < items.length; i++) {
			if (items[i].className.match(/(^|\s)sort(\s|$)/)) {
				res.push(items[i]);
			}
		}
		return res;
	},
	
	getOrder : function(e) {
		var items = dragSort.getSortableItems(e);
		var res = '';
		for (var i = 0; i< items.length; i++) {
			res += items[i].id+';';
		}
		
		return res.replace(/;$/,'');
	},
	
	makeElementSortable : function(e) {
		var items = dragSort.getSortableItems(e);
		for (var i = 0; i < items.length; i++) {
			dragSort.makeItemSortable(items[i]);
		}
	},
	
	prepareItem : function(e) {
		for (var i=0; i < e.childNodes.length; i++) {
			if (e.childNodes[i].nodeName == 'P' &&
			e.childNodes[i].className == 'nojsfield') {
				e.removeChild(e.childNodes[i]);
			}
		}
		e.className += ' sortJS';
	},
	
	makeItemSortable : function(item) {
		Drag.makeDraggable(item);
		
		dragSort.prepareItem(item);
		
		item.setDragThresholdY(5);
		item.style.position = 'relative';
		item.onDragStart = dragSort.onDragStart;
		item.onDrag = dragSort.onDrag;
		item.onDragEnd = dragSort.onDragEnd;
	},
	
	onDragStart : function(nwPosition, sePosition, nwOffset, seOffset) {
		var items = dragSort.getSortableItems(this.parentNode);
		var minOffset = Coordinates.northwestOffset(items[0], true);
		var maxOffset = Coordinates.northwestOffset(items[items.length - 1], true);
		this.constrain(minOffset, maxOffset);
	},

	onDrag : function(nwPosition, sePosition, nwOffset, seOffset) {
		var parent = this.parentNode;

		var item = this;
		var next = DragUtils.nextItem(item);
		while (next != null && this.offsetTop >= next.offsetTop - 2) {
			var item = next;
			var next = DragUtils.nextItem(item);
		}
		if (this != item) {
			DragUtils.swap(this, next);
			return;
		}

		var item = this;
		var previous = DragUtils.previousItem(item);
		while (previous != null && this.offsetTop <= previous.offsetTop + 2) {
			var item = previous;
			var previous = DragUtils.previousItem(item);
		}
		if (this != item) {
			DragUtils.swap(this, item);
			return;
		}
	},

	onDragEnd : function(nwPosition, sePosition, nwOffset, seOffset) {
		this.style["top"] = "0px";
		this.style["left"] = "0px";
		
		if (dragSort.dest != null) {
			dragSort.dest.value = dragSort.getOrder(this.parentNode);
		}
	}
};

var DragUtils = {
	swap : function(item1, item2) {
		var parent = item1.parentNode;
		parent.removeChild(item1);
		parent.insertBefore(item1, item2);

		item1.style["top"] = "0px";
		item1.style["left"] = "0px";
	},

	nextItem : function(item) {
		var sibling = item.nextSibling;
		while (sibling != null) {
			if (sibling.nodeName == item.nodeName) return sibling;
			sibling = sibling.nextSibling;
		}
		return null;
	},

	previousItem : function(item) {
		var sibling = item.previousSibling;
		while (sibling != null) {
			if (sibling.nodeName == item.nodeName) return sibling;
			sibling = sibling.previousSibling;
		}
		return null;
	}
};
