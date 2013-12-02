// gallery/dialogs/gallery.js
// (inspiré du plugin image)
CKEDITOR.dialog.add("gallery",function(e)
    {
    var carH = 800, carV = 600; // dimension maxi du carrousel à ajuster
    //
    var out, uri, des, tit, wid, hei, previewPreloader, originalElement;
    var selectedElement;
    var resetSize = function( dialog ) // mise à jour height et width
        {
        var oImageOriginal = dialog.originalElement;
        if ( oImageOriginal.getCustomData( 'isReady' ) == 'true' )
            {
            wid = dialog.getContentElement( 'info', 'txtWidth' );
            hei = dialog.getContentElement( 'info', 'txtHeight' );
            //wid && wid.setValue( oImageOriginal.$.width );
            //hei && hei.setValue( oImageOriginal.$.height );
            }
        };
        function r() {
            var a = arguments,
                b = this.getContentElement("advanced", "txtdlgGenStyle");
            b && b.commit.apply(b, a);
            this.foreach(function (b) {
                b.commit && "txtdlgGenStyle" != b.id && b.commit.apply(b, a);
            })
        };
        function i(a) {
            if (!s) {
                s = 1;
                var b = this.getDialog(),
                    d = b.imageElement;
                if (d) {
                    this.commit(f, d);
                    for (var a = [].concat(a), e = a.length, c, g = 0; g < e; g++)(c = b.getContentElement.apply(b, a[g].split(":"))) && c.setup(f, d);
                }
                s = 0;
            }
        };
    var onImgLoadEvent = function() // image ready
        {
        var original = this.originalElement;
        original.setCustomData( 'isReady', 'true' );
        original.removeListener( 'load', onImgLoadEvent );
        resetSize( this );
        };
    var f = 1,
        k = /^\s*(\d+)((px)|\%)?\s*$/i,
        v = /(^\s*(\d+)((px)|\%)?\s*$)|^$/i,
        o = /^\d+px$/,
        w = function () {
            var a = this.getValue(),
                b = this.getDialog(),
                d = a.match(k);
            d && ("%" == d[2] && l(b, !1), a = d[1]);
            b.lockRatio && (d = b.originalElement, "true" == d.getCustomData("isReady") && ("txtHeight" == this.id ? (a && "0" != a && (a = Math.round(d.$.width * (a / d.$.height))), isNaN(a) || b.setValueOf("info", "txtWidth", a)) : (a && "0" != a && (a = Math.round(d.$.height * (a / d.$.width))), isNaN(a) || b.setValueOf("info", "txtHeight", a))));
            g(b)
        }, g = function (a) {
            if (!a.originalElement || !a.preview) return 1;
            a.commitContent(4, a.preview);
            return 0
        }, s, l = function (a,b) {
            if (!a.getContentElement("info", "ratioLock")) return null;
            var d = a.originalElement;
            if (!d) return null;
            if ("check" == b) {
                if (!a.userlockRatio && "true" == d.getCustomData("isReady")) {
                    var e = a.getValueOf("info", "txtWidth"),
                        c = a.getValueOf("info", "txtHeight"),
                        d = 1E3 * d.$.width / d.$.height,
                        f = 1E3 * e / c;
                    a.lockRatio = !1;
                    !e && !c ? a.lockRatio = !0 : !isNaN(d) && !isNaN(f) && Math.round(d) == Math.round(f) && (a.lockRatio = !0)
                }
            } else void 0 != b ? a.lockRatio = b : (a.userlockRatio = 1, a.lockRatio = !a.lockRatio);
            e = CKEDITOR.document.getById(p);
            a.lockRatio ? e.removeClass("cke_btn_unlocked") : e.addClass("cke_btn_unlocked");
            e.setAttribute("aria-checked", a.lockRatio);
            CKEDITOR.env.hc && e.getChild(0).setHtml(a.lockRatio ? CKEDITOR.env.ie ? "■" : "▣" : CKEDITOR.env.ie ? "□" : "▢");
            return a.lockRatio;
        }, x = function (a) {
            var b = a.originalElement;
            if ("true" == b.getCustomData("isReady")) {
                var d = a.getContentElement("info", "txtWidth"),
                    e = a.getContentElement("info", "txtHeight");
                d && d.setValue(b.$.width);
                e && e.setValue(b.$.height)
            }
            g(a)
        }, y = function (a, b) {
            function d(a, b) {
                var d = a.match(k);
                return d ? ("%" == d[2] && (d[1] += "%", l(e, !1)), d[1]) : b
            }
            if (a == f) {
                var e = this.getDialog(),
                    c = "",
                    g = "txtWidth" == this.id ? "width" : "height",
                    h = b.getAttribute(g);
                h && (c = d(h, c));
                c = d(b.getStyle(g), c);
                this.setValue(c)
            }
        }, n = function (a) {
            return CKEDITOR.tools.getNextId() + "_" + a
        }, p = n("btnLockSizes"),
        u = n("btnResetSize"),
        m = n("ImagePreviewLoader"),
        A = n("previewLink"),
        z = n("previewImage");
    
    return{
        title:'Gallery carrousel - ' + carH + 'x' + carV,
        resizable : CKEDITOR.DIALOG_RESIZE_BOTH,
        minWidth:400, minHeight:150,
        onShow : function()
            {
            var editor = this.getParentEditor(),
	            sel = this.getParentEditor().getSelection(),
	            element = sel.getSelectedElement(), divGallery,
	            previewPreloader=new CKEDITOR.dom.element('img',editor.document);
            this.lockRatio = !0;
            this.userlockRatio = 0;
            this.dontResetSize = !1;
            if (element) {
            	divGallery= element.getParent();
            	selectedElement = divGallery;
            }
            // Set default value from seleted element
            if (divGallery) {
            	tit = $(divGallery).attr('data-gallery-title');
            	des = $(divGallery).attr('data-gallery-desc');
            	url = $(divGallery).attr('data-gallery-url');
            	uri = $(divGallery).attr('data-gallery-uri');
            	wid = $(divGallery).attr('data-gallery-width');
            	hei = $(divGallery).attr('data-gallery-height');
            }
            this.preview = CKEDITOR.document.getById(z);
            
            var a = this.getParentEditor();
            t = new CKEDITOR.dom.element("img", a.document);
            this.originalElement = a.document.createElement("img");
            this.originalElement.setAttribute("alt", "");
            this.originalElement.setCustomData("isReady", "false");
            
        	this.definition.dialog.getContentElement('info','txtid').setValue(uri);            	
        	this.definition.dialog.getContentElement('info','desid').setValue(des);
        	this.definition.dialog.getContentElement('info','titid').setValue(tit);
        	this.definition.dialog.getContentElement('info','txtWidth').setValue(wid);
        	this.definition.dialog.getContentElement('info','txtHeight').setValue(hei);
        	CKEDITOR.tools.trim(this.getValueOf("info", "txtid")) || (this.preview.removeAttribute("src"), this.preview.setStyle("display","none"));
            },
        onOk:function() {
        	var editor = this.getParentEditor();
        	
            if (wid/hei>carH/carV) // mise aux dimensions du carrousel
                { hei = Math.round(hei*carH/wid); wid = carH; }
            else { wid = Math.round(wid*carV/hei); hei = carV; }
         
            // HTML de sortie à parametrer
            var arrayUrl = new Array();
            arrayUrl = uri.split('/');
            arrayUrl.pop();
            var url = arrayUrl.join('/');

            if(!selectedElement)  {
                out = '<div class="gallery" data-gallery-url="'+url+'" data-gallery-uri="'+uri+'" ';
                out += 'data-gallery-desc="'+des+'" data-gallery-title="'+tit+'" data-gallery-width="'+wid+'" ';
                out += 'data-gallery-height="'+hei+'" style="width:'+wid+'px; height:'+hei+'px;display:block;" >';
                out += '<img src="'+uri+'" width="'+wid+'" height="'+hei+'" class="image0" />';
                out += '</div>';            	
            	editor.insertHtml(out);
            } else {
                out = '<img src="'+uri+'" width="'+wid+'" height="'+hei+'" class="image0" />';        	
            	selectedElement.setAttributes({
            	    'class' : 'gallery',
            	    'data-gallery-url':url,
            	    'data-gallery-uri':uri,
                    'data-gallery-desc':des,
                    'data-gallery-title':tit,
                    'data-gallery-width':wid,
                    'data-gallery-height':hei,
                    'style': 'width:'+wid+'px; height:'+hei+'px;display:block;'});
            	selectedElement.setHtml(out);
            }
        },
        contents:[
            {
            id:'info',
            name:'info',
            label:'Tab',
            elements:[
                {
                type : 'button',
                hidden : true,
                id : 'browse',
                filebrowser :
                    {
                    action:'Browse',
                    target:'info:txtid', // Mise à jour du champ
                    url: CKEDITOR.config.filebrowserImageBrowseUrl,
                    },
                    label : e.lang.common.browseServer,
                    style : 'float:right',
                },
                {
                id:'txtid',
                name:'txtid',
                type:'text',
                label:'Url',
                //onLoad: function() {this.setValue(uri);},
                validate:function(){uri=this.getValue();},
                onChange : function()
                    {
                    var dialog = this.getDialog(),
                    newUrl = this.getValue();
                    if ( newUrl.length > 0 )  {
                        dialog = this.getDialog();
                        var original = dialog.originalElement;
                        original.setCustomData( 'isReady', 'false' );
                        original.on( 'load', onImgLoadEvent, dialog );
                        original.setAttribute( 'src', newUrl );
                        t.setAttribute('src', newUrl);
                        dialog.preview.setAttribute("src", t.$.src);
                        g(dialog);
                        }
                    },
                },
                {
                id:'desid',
                name:'desid',
                type:'text',
                label:'Description',
                validate:function(){des=this.getValue();},
                },
                {
                id:'titid',
                name:'titid',
                type:'text',
                label:'Titre',
                style : 'visibility:visible;', // suivant besoins
                validate:function(){tit=this.getValue();},
                },
                {
                    type: "hbox",
                    children: [{
                        id: "basic",
                        type: "vbox",
                        children: [{
                            type: "hbox",
                            widths: ["50%", "50%"],
                            children: [{
                                type: "vbox",
                                padding: 1,
                                children: [{
                                    type: "text",
                                    width: "40px",
                                    id: "txtWidth",
                                    name:'txtWidth',
                                    label: e.lang.common.width,
                                    onKeyUp: w,
                                    onChange: function () {
                                        i.call(this, "advanced:txtdlgGenStyle")
                                    },
                                    validate: function () {
                                        var a = this.getValue().match(v);
                                        wid=this.getValue();
                                        (a = !! (a && 0 !== parseInt(a[1], 10))) || alert(e.lang.common.invalidWidth);
                                        return a
                                    },
                                    //setup: y,
                                    commit: function (a, b, d) {
                                        var c = this.getValue();
                                        //wid = this.getValue();
                                        a == f ? (c ? b.setStyle("width", CKEDITOR.tools.cssLength(c)) : b.removeStyle("width"), !d && b.removeAttribute("width")) : 4 == a ? c.match(k) ? b.setStyle("width", CKEDITOR.tools.cssLength(c)) : (a = this.getDialog().originalElement, "true" == a.getCustomData("isReady") && b.setStyle("width", a.$.width + "px")) : 8 == a && (b.removeAttribute("width"), b.removeStyle("width"))
                                    }
                                }, {
                                    type: "text",
                                    id: "txtHeight",
                                    name:'txtHeight',
                                    width: "40px",
                                    label: e.lang.common.height,
                                    onKeyUp: w,
                                    onChange: function () {
                                        i.call(this, "advanced:txtdlgGenStyle")
                                    },
                                    validate: function () {
                                        var a = this.getValue().match(v);
                                        hei=this.getValue();
                                        (a = !! (a && 0 !== parseInt(a[1],10))) || alert(e.lang.common.invalidHeight);
                                        return a
                                    },
                                    //setup: y,
                                    commit: function (a, b, d) {
                                        var c = this.getValue();
                                        //hei=this.getValue();                                        
                                        a == f ? (c ? b.setStyle("height", CKEDITOR.tools.cssLength(c)) : b.removeStyle("height"), !d && b.removeAttribute("height")) : 4 == a ? c.match(k) ? b.setStyle("height", CKEDITOR.tools.cssLength(c)) : (a = this.getDialog().originalElement, "true" == a.getCustomData("isReady") && b.setStyle("height", a.$.height + "px")) : 8 == a && (b.removeAttribute("height"), b.removeStyle("height"))
                                        
                                    }
                                }]
                            }, {
                                id: "ratioLock",
                                type: "html",
                                style: "margin-top:30px;width:40px;height:40px;",
                                onLoad: function () {
                                    var a = CKEDITOR.document.getById(u),
                                        b = CKEDITOR.document.getById(p);
                                    a && (a.on("click", function (a) {
                                        x(this);
                                        a.data && a.data.preventDefault()
                                    }, this.getDialog()), a.on("mouseover", function () {
                                        this.addClass("cke_btn_over")
                                    }, a), a.on("mouseout", function () {
                                        this.removeClass("cke_btn_over")
                                    }, a));
                                    b && (b.on("click", function (a) {
                                        l(this);
                                        var b = this.originalElement,
                                            c = this.getValueOf("info", "txtWidth");
                                        if (b.getCustomData("isReady") == "true" && c) {
                                            b = b.$.height / b.$.width * c;
                                            if (!isNaN(b)) {
                                                this.setValueOf("info",
                                                    "txtHeight", Math.round(b));
                                                g(this)
                                            }
                                        }
                                        a.data && a.data.preventDefault()
                                    }, this.getDialog()), b.on("mouseover", function () {
                                        this.addClass("cke_btn_over")
                                    }, b), b.on("mouseout", function () {
                                        this.removeClass("cke_btn_over")
                                    }, b))
                                },
                                html: '<div><a href="javascript:void(0)" tabindex="-1" title="' + e.lang.image.lockRatio + '" class="cke_btn_locked" id="' + p + '" role="checkbox"><span class="cke_icon"></span><span class="cke_label">' + e.lang.image.lockRatio + '</span></a><a href="javascript:void(0)" tabindex="-1" title="' + e.lang.image.resetSize + '" class="cke_btn_reset" id="' + u + '" role="button"><span class="cke_label">' + e.lang.image.resetSize + "</span></a></div>"
                            }]
                        }],
                    }, {
                        type: "vbox",
                        height: "250px",
                        children: [{
                            type: "html",
                            id: "htmlPreview",
                            style: "width:100%;",
                            html: '<div style="overflow:none;">' + CKEDITOR.tools.htmlEncode(e.lang.common.preview) 
                            	+ '<br/><div id="' + m + '" class="ImagePreviewLoader" style="display:none">'
                            	+'<div class="loading">&nbsp;</div></div><div class="ImagePreviewBox" style="overflow:none;">'
                            	+'<table><tr><td><a href="javascript:void(0)" target="_blank" onclick="return false;" id="' + A + '">'
                            	+'<img id="' + z + '" alt="" style="width:90%;height:85%" /></a></td></tr></table></div></div>'
                        }]
                    }],
                },
                ]
            }],
        buttons:[
        CKEDITOR.dialog.okButton,
        CKEDITOR.dialog.cancelButton],
        };
    });
