/*
Copyright (c) 2003-2009, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.addTemplates('default',
{imagesPath:CKEDITOR.getUrl(CKEDITOR.plugins.getPath('templates')+'templates/images/'),
	templates:[{
		title:'A la Une',
		image:'template_cadre0.gif',
		description:'Une image et la légende',		
		html:'<div style="width:557px;height:260px;vertical-align:middle;align:center;" ><img height="240px" src="/plume/manager/themes/default/images/noimage.png" alt="placer içi une image hauteur 240px max" /></div><div class="titreAlaUne" style="width:557px;">Mettre la légende</div>'
	},	
		{
		title:'Image et Titre',
		image:'template_cadre1.gif',
		description:'Un tableau à 2 colonnes avec 1 image à gauche.',		
		html:'<table border="0" cellspacing="0" cellpadding="0" style="width:600px"><tr><td height="230px" valign="middle" align="center" width="200px"><img width="190px" src="/plume/manager/themes/default/images/noimage.png" alt="image" /></td><td valign="top"><h3>Type the title here</h3><br/>Content</td></tr></table>'
	},
		{
		title:'Image et Titre',
		image:'template4.gif',
		description:'Un titre et un tableau à 3 colonnes.',		
		html:'<h3>Type the title here</h3><table border="0" cellspacing="0" cellpadding="0" style="width:100%"><tr><td style="width:50px">colonne 1</td><td>colonne 2</td><td>colonne 3</td></tr><tr><td style="width:50px">colonne 1</td><td>colonne 2</td><td>colonne 3</td></tr></table>'
	},
		{title:'Image et Titre',
		image:'template5.gif',
		description:'Un titre et un tableau à 2 colonnes.',		
		html:'<h3>Type the title here</h3><table border="0" cellspacing="0" cellpadding="0" style="width:100%"><tr><td>colonne 1</td><td>colonne 2</td></tr><tr><td>colonne 1</td><td>colonne 2</td></tr></table>'
	},
		{title:'Image et Titre',
		image:'template1.gif',
		description:'Une image principale avec un titre et du texte autour.',		
		html:'<h3>Type the title here</h3><img style="margin-right: 10px" height="100" width="100" align="left"/><p>Type the text here</p>'
	},
		{title:'Image and Titre (2)',
		image:'template1b.gif',
		description:'Une image principale avec un titre et du texte autour.',		
		html:'<h3>Type the title here</h3><p>Type the text here<img style="margin-right: 10px" height="100" width="100" align="left"/></p>'
	},
		{title:'Strange Template',
		image:'template2.gif',
		description:'A template that defines two colums, each one with a title, and some text.',
		html:'<table cellspacing="0" cellpadding="0" style="width:100%" border="0"><tr><td style="width:50%"><h3>Title 1</h3></td><td></td><td style="width:50%"><h3>Title 2</h3></td></tr><tr><td>Text 1</td><td></td><td>Text 2</td></tr></table><p>More text goes here.</p>'
	},
		{title:'Text and Table',
		image:'template3.gif',
		description:'A title with some text and a table.',
		html:'<div style="width: 80%"><h3>Title goes here</h3><table style="float: right" cellspacing="0" cellpadding="0" style="width:150px" border="1"><caption style="border:solid 1px black"><strong>Table title</strong></caption></tr><tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr><tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr><tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr></table><p>Type the text here</p></div>'
	},
		{title:'Table and Text',
		image:'template3b.gif',
		description:'A title with some text and a table.',
		html:'<div style="width: 80%"><h3>Title goes here</h3><table style="float: left" cellspacing="0" cellpadding="0" style="width:150px" border="1"><caption style="border:solid 1px black"><strong>Table title</strong></caption></tr><tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr><tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr><tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr></table><p>Type the text here</p></div>'
	}]
});
