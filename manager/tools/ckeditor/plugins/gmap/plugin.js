﻿/**
 * QR CODES plugin for CKEditor 3.x 
 * @Author: Cedric Dugas, http://www.position-absolute.com
 * @Copyright Cakemail
 * @Licence: MIT
 * @version:	 1.0
 */

CKEDITOR.plugins.add('gmap',   //nom du plugin
	    {   
	    requires: ['dialog'], // besoin d'une fenetre de dialogue
	    lang : ['en', 'fr'],
	    init:function(a) {
	        var b="gmap";
	        var c=a.addCommand(b,new CKEDITOR.dialogCommand(b));
	        c.modes={wysiwyg:1,source:0};
	        c.canUndo=false;	 
	        // ajout d'un bouton pour CKEditor
	        a.ui.addButton("gmap",
	            {
	            label:a.lang.gmap.title,
	            command:b,
	            icon:this.path+"gmap.png",
	            /*toolbar: 'tools',*/
	            });
	        // emplacement de la boite de dialogue
	        CKEDITOR.dialog.add(b,this.path+"dialogs/gmap.js")
	        }
	    });