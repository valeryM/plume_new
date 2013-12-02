// gallery/dialogs/gallery.js
// (inspiré du plugin image)
CKEDITOR.dialog.add("calendar",function(e)
    {

    return{
    	
        title:'Afficher un calendrier',
        resizable : CKEDITOR.DIALOG_RESIZE_BOTH,
        minWidth:300, minHeight:150,
        onShow : function()
            {
        	var editor = this.getParentEditor();
        	var element = editor.getSelection().getSelectedElement();
        	var selectedElement;
        	if (element) {
        		selectedElement=element.getParent();
        	
        	this.definition.dialog.getContentElement('main','href').setValue($(selectedElement).attr('data-fullcalendar-href'));            	
        	this.definition.dialog.getContentElement('main','params').setValue($(selectedElement).attr('data-fullcalendar-params'));
        	this.definition.dialog.getContentElement('main','width').setValue($(selectedElement).attr('data-fullcalendar-width'));
        	this.definition.dialog.getContentElement('main','height').setValue($(selectedElement).attr('data-fullcalendar-height'));
        	}

            },
        onOk:function() {
        	var editor = this.getParentEditor();
        	var element = editor.getSelection().getSelectedElement();
        	var selectedElement;
        	if (element) selectedElement=element.getParent();
        	
            // HTML de sortie à parametrer
        	var width = this.getValueOf('main','width'),
        		height = this.getValueOf('main','height'),
        		href = this.getValueOf('main','href'),
        		params = this.getValueOf('main','params');
        	var span ='<img src="'+editor.plugins.calendar.path+'calendar-sample.png" style="border:1px solid silver; width:'+width+'px; height:'+height+'px; display:block;" />';
        	
            if(!selectedElement)  {
            	out = '<div class="fullcalendar" data-fullcalendar-href="'+href+'" '
                 	+ 'data-fullcalendar-params="'+params+'" data-fullcalendar-height="'+height+'" '
                 	+ 'data-fullcalendar-width="'+width+'" style="width:'+width+'px;height:'+height+'px; display:block;">'
                 	+ span
                 	+ '</div>';          	
            	editor.insertHtml(out);
            } else {    	
            	selectedElement.setAttributes({
            	    'class' : 'fullcalendar',
            	    'data-fullcalendar-href':href,
            	    'data-fullcalendar-params':params,
                    'data-fullcalendar-width':width,
                    'data-fullcalendar-height':height,
                    'style':'width:'+width+'px;height:'+height+'px; display:block;',
                    });
            	selectedElement.setHtml(span);
            }
        },
        contents:[
            {
            id:'main',
            label:'main',
            elements:[
                {
                	id:'href',
	                name:'href',
	                type:'text',
	                label:'Url du service',
	                required: true,
	                validate: CKEDITOR.dialog.validate.notEmpty(),
	                'default': 'url du service',
	                setup: function(element) {
	                    this.setValue(element.getAttribute('data-fullcalendar-href'));
	                	},
                }, {
	                id:'params',
	                name:'params',
	                type:'text',
	                label:'paramètres',
	                required: true,
	                validate: CKEDITOR.dialog.validate.notEmpty(),
	                setup: function(element) {
	                    this.setValue(element.getAttribute('data-fullcalendar-params'));
	                	},
	            }, {
	                id:'width',
	                name:'width',
	                type:'text',
	                label:'largeur',
	                required: false,
	                validate: CKEDITOR.dialog.validate.notEmpty(),
	                setup: function(element) {
	                    this.setValue(element.getAttribute('data-fullcalendar-width'));
	                	},
                }, {
	                id:'height',
	                name:'height',
	                type:'text',
	                label:'hauteur',
	                required: false,
	                validate: CKEDITOR.dialog.validate.notEmpty(),
	                setup: function(element) {
	                    this.setValue(element.getAttribute('data-fullcalendar-height'));
	                	},
                }],
            }],
        buttons:[
        CKEDITOR.dialog.okButton,
        CKEDITOR.dialog.cancelButton],
        };
    });
