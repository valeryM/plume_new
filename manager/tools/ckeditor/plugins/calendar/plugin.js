// gallery/plugin.js
CKEDITOR.plugins.add('calendar',   //nom du plugin
    {   
    requires: ['dialog'], // besoin d'une fenetre de dialogue*
    beforeInit: function(editor) {
        editor.config.menu_groups = editor.config.menu_groups + ',' + 'calendar';
    },    
    init:function(editor) {
        var b="calendar";
        var c=editor.addCommand(b,new CKEDITOR.dialogCommand(b));
        c.modes={wysiwyg:1,source:1};
        c.canUndo=true;
         
        // ajout d'un bouton pour CKEditor
        editor.ui.addButton(b,
            {
            label:'Calendrier',
            command:b,
            icon:this.path+"calendar.png",
            });
        // emplacement de la boite de dialogue
        CKEDITOR.dialog.add(b,this.path+"dialogs/calendar.js");
        
        //register doubleclick
        editor.on('doubleclick', function(evt) {
            var element = evt.data.element;
            var parent = element.getParent();
	    	var isCalendarElement = (element.hasClass('fullcalendar') || parent.hasClass('fullcalendar'));
            if (element && !element.isReadOnly() && isCalendarElement /*element.getAttribute('alt') == 'gallery'*/)
                evt.data.dialog = b;
        });

		//register contextmenu
		if (editor.contextMenu) {
		    editor.addMenuGroup( 'tools' );
		    editor.addMenuItem( 'calendar', {
		        label: 'Calendrier',
		        icon: this.path+'calendar.png',
		        command: b,
		        group: 'tools'
		    });
		} 
		if (editor.contextMenu) {
		    editor.contextMenu.addListener(function(element, selection) {
		    	var parent = element.getParent();
		    	var isCalendarElement = (element.hasClass('fullcalendar') || parent.hasClass('fullcalendar'));
		        if (!element || element.isReadOnly() || !isCalendarElement )
		            return null;
		        return {calendar: CKEDITOR.TRISTATE_OFF};
		    } );
		}
    }

});