// pdfviewer/dialogs/pdfviewer.js
// (inspiré du plugin image)
CKEDITOR.dialog.add("pdfviewer",function(e)
    {
    return{
    	
        title:'Afficher un fichier PDF',
        resizable : CKEDITOR.DIALOG_RESIZE_BOTH,
        minWidth:300, minHeight:150,
        onShow : function()
            {
        	var editor = this.getParentEditor();
        	var element = editor.getSelection().getSelectedElement();
        	var selectedElement;
        	if (element) {
        		selectedElement=element.getParent();
          	
        	this.definition.dialog.getContentElement('main','url').setValue($(selectedElement).attr('data-pdfviewer-url'));
        	this.definition.dialog.getContentElement('main','width').setValue($(selectedElement).attr('data-pdfviewer-width'));
        	this.definition.dialog.getContentElement('main','height').setValue($(selectedElement).attr('data-pdfviewer-height'));
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
        		href = this.getValueOf('main','url');

        	var span ='<img src="'+editor.plugins.pdfviewer.path+'pdfviewer-sample.png" style="border:1px solid silver; width:'+width+'px; height:'+height+'px; display:block;" />';
        	
            if(!selectedElement)  {
            	out = '<div class="pdfviewer" data-pdfviewer-url="'+href+'" '
                 	+ 'data-pdfviewer-height="'+height+'" '
                 	+ 'data-pdfviewer-width="'+width+'" style="width:'+width+'px;height:'+height+'px; display:block;">'
                 	+ span
                 	+ '</div>';          	
            	editor.insertHtml(out);
            } else {    	
            	selectedElement.setAttributes({
            	    'class' : 'pdfviewer',
            	    'data-pdfviewer-url':href,
                    'data-pdfviewer-width':width,
                    'data-pdfviewer-height':height,
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
                	id:'url',
	                name:'url',
	                type:'text',
	                label:'Url du fichier PDF',
	                required: true,
	                validate: CKEDITOR.dialog.validate.notEmpty(),
	                'default': 'url du fichierPDF',
	                setup: function(element) {
	                    this.setValue(element.getAttribute('data-pdfviewer-url'));
	                	},
                }, {
	                id:'width',
	                name:'width',
	                type:'text',
	                label:'largeur',
	                required: false,
	                validate: CKEDITOR.dialog.validate.notEmpty(),
	                setup: function(element) {
	                    this.setValue(element.getAttribute('data-pdfviewer-width'));
	                	},
                }, {
	                id:'height',
	                name:'height',
	                type:'text',
	                label:'hauteur',
	                required: false,
	                validate: CKEDITOR.dialog.validate.notEmpty(),
	                setup: function(element) {
	                    this.setValue(element.getAttribute('data-pdfviewer-height'));
	                	},
                }],
            }],
        buttons:[
        CKEDITOR.dialog.okButton,
        CKEDITOR.dialog.cancelButton],
        };
    });
