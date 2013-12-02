// pdfviewer/plugin.js
CKEDITOR.plugins.add('pdfviewer',   //nom du plugin
    {   
    requires: ['dialog'], // besoin d'une fenetre de dialogue*
    beforeInit: function(editor) {
        editor.config.menu_groups = editor.config.menu_groups + ',' + 'pdfviewer';
    },    
    init:function(editor) {
        var b="pdfviewer";
        var c=editor.addCommand(b,new CKEDITOR.dialogCommand(b));
        c.modes={wysiwyg:1,source:1};
        c.canUndo=true;
         
        // ajout d'un bouton pour CKEditor
        editor.ui.addButton(b,
            {
            label:'Pdf-Viewer',
            command:b,
            icon:this.path+"pdfviewer.png",
            });
        // emplacement de la boite de dialogue
        CKEDITOR.dialog.add(b,this.path+"dialogs/pdfviewer.js");
        
        //register doubleclick
        editor.on('doubleclick', function(evt) {
            var element = evt.data.element;
            var parent = element.getParent();
	    	var isPdfViewerElement = (element.hasClass('pdfviewer') || parent.hasClass('pdfviewer'));
            if (element && !element.isReadOnly() && isPdfViewerElement )
                evt.data.dialog = b;
        });

		//register contextmenu
		if (editor.contextMenu) {
		    editor.addMenuGroup( 'tools' );
		    editor.addMenuItem( 'pdfviewer', {
		        label: 'Pdf-Viewer',
		        icon: this.path+'pdfviewer.png',
		        command: b,
		        group: 'tools'
		    });
		} 
		if (editor.contextMenu) {
		    editor.contextMenu.addListener(function(element, selection) {
		    	var parent = element.getParent();
		    	var isPdfViewerElement = (element.hasClass('pdfviewer') || parent.hasClass('pdfviewer'));
		        if (!element || element.isReadOnly() || !isPdfViewerElement )
		            return null;
		        return {pdfviewer: CKEDITOR.TRISTATE_OFF};
		    } );
		}
    }

});