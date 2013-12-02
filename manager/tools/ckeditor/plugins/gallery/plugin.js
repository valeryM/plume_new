// gallery/plugin.js
CKEDITOR.plugins.add('gallery',   //nom du plugin
    {   
    requires: ['dialog'], // besoin d'une fenetre de dialogue*
    beforeInit: function(editor) {
        editor.config.menu_groups = editor.config.menu_groups + ',' + 'gallery';
    },    
    init:function(editor) {
        var b="gallery";
        var c=editor.addCommand(b,new CKEDITOR.dialogCommand(b));
        //c.modes={wysiwyg:1,source:1};
        c.canUndo=true;
         
        // ajout d'un bouton pour CKEditor
        editor.ui.addButton(b,
            {
            label:'Gallery carrousel',
            command:b,
            icon:this.path+"gallery.png",
            /*toolbar: 'tools',*/
            });
        // emplacement de la boite de dialogue
        CKEDITOR.dialog.add(b,this.path+"dialogs/gallery.js");
        
        //register doubleclick
        editor.on('doubleclick', function(evt) {
            var element = evt.data.element;
            var parent = element.getParent();
            var isGalleryElement = (element.hasClass('gallery') || parent.hasClass('gallery'));
            if (element && !element.isReadOnly() && isGalleryElement /*element.getAttribute('alt') == 'gallery'*/)
                evt.data.dialog = b;
        });
       /*
		//register menu item
		if (editor.addMenuItems) {
		    editor.addMenuItems(
		        {
		            gallery:
		            {
		                label:'Gallery carrousel',
		                command: b,
		                group: b,
		                icon: this.path+'gallery.png'
		            }
		        } );
		}
		*/
		//register contextmenu
		if (editor.contextMenu) {
		    editor.addMenuGroup( 'tools' );
		    editor.addMenuItem( 'gallery', {
		        label: 'Gallery carrousel',
		        icon: this.path+'gallery.png',
		        command: b,
		        group: 'tools'
		    });
		} 
		//register contextmenu
		if (editor.contextMenu) {
		    editor.contextMenu.addListener(function(element, selection) {
		    	var parent = element.getParent();
		    	var isGalleryElement = (element.hasClass('gallery') || parent.hasClass('gallery'));
		        if (!element || element.isReadOnly() || !isGalleryElement )
		            return null;
		        return {gallery: CKEDITOR.TRISTATE_OFF};
		    } );
		}
    }

});