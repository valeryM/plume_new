// Victor Hugo Herrera Maldonado
//
// Terms of Use
//
// This software is licensed under Apache License, Version 2.0 License and is copyrighted
// (C)2009 by Victor Hugo Herrera Maldonado
//
// For details, visit http://www.apache.org/licenses/LICENSE-2.0
//

/*
 * TODO: 
 *       Simplify distribution.
 *
 */


(function($){
    
    var methods={
        init: function(optionsAccesor, pluginOptions) {
            return this.each(function(index, element){
                makePathSelector(element, pluginOptions, optionsAccesor);
            });
        },
        value: function(parts) {
            if(parts){
                return this.each(function(index, element){
                    var pathSelector=$(this).parents(".pathSelector").get(0);
                    if(isString(parts)){
                        pathSelector.setParts(parts.split($(pathSelector).data("pathSelectorData").options.separator));
                    }else{
                        pathSelector.setParts(parts);
                    }
                });
            }else{
                return this.each.val();
            }
        },
        parts: function(parts){
            var pathSelector=$(this).parents(".pathSelector").get(0);
            if(parts){
                return this.each(function(index, element){
                    pathSelector.setParts(parts);
                });
            }else{
                return pathSelector.getParts();
            }
        }
    };

    $.fn.pathSelector = function(method) {
        $.fn.pathSelector.defaults={
            separator: "."
        };
        if(methods[method]){
            return methods[method].apply(this, Array.prototype.slice.call( arguments, 1 ));
        }else{
            return methods.init.apply(this, arguments);
        }
    };
	

    function makePathSelector(input, pluginOptions, optionsAccesor) {
        if($(input).data("pathSelectorData")){
            return;
        }

        if(input.name == "input"){
            return;
        }
        
        pluginOptions = $.extend({}, $.fn.pathSelector.defaults, pluginOptions);


        /* Apply the html changes to wrap the input element, insert the selector and the options menu */
        var pathSelector=
            $(input).wrap("<span class='pathSelector ui-widget ui-state-default ui-corner-all'></span>")
                    .hide()
                    .parents(".pathSelector").get(0);
        var pathSelectorData={};
        $(input).parents(".pathSelector").data("pathSelectorData", pathSelectorData);
        $(pathSelector).append("<ul class='contextMenu ui-widget ui-state-default ui-corner-all'></ul>");
        //$(pathSelector).find(".level, .arrowButton, .contextMenu a").live("mouseover", "", function(){$(this).addClass("ui-state-hover")});
        $(pathSelector).on("mouseover", ".level, .arrowButton, .contextMenu a", "", function(){$(this).addClass("ui-state-hover"); });
//        //$(pathSelector).find(".level, .arrowButton, .contextMenu a").live("mouseout", "", function(){$(this).removeClass("ui-state-hover")});
        $(pathSelector).on("mouseout", ".level, .arrowButton, .contextMenu a", "",  function(){$(this).removeClass("ui-state-hover"); });
//        //$(pathSelector).find("a").live("mousedown", "", function(){$(this).addClass("ui-state-active")});
        $(pathSelector).on("mousedown", "a", "",  function(){$(this).addClass("ui-state-active"); });
//        //$(pathSelector).find("a").live("mouseup", "", function(){$(this).removeClass("ui-state-active")});
        $(pathSelector).on("mouseup", "a", "",  function(){$(this).removeClass("ui-state-active"); });
        
        /* Define the internal functions of the plugin. */
        

        /* Model functions */
        pathSelector.setParts=function(parts){
            $(this).find(".arrowButton").remove();
            $(this).find(".level").remove();
            var valueString="";
            for(var i=0; i < parts.length; i++){
                var wrappedValue=wrapValue(parts[i]);
                this.addOptionsExpander();
                this.addValuePart(wrappedValue);
                if(i > 0){
                    valueString += $(this).data("pathSelectorData").options.separator;
                }
                valueString += wrappedValue.value;
            }
            $(this).data("pathSelectorData").values=parts;
            $(this).find("input").val(valueString);
            $(this).find("input").trigger("valueChanged", valueString);
            pathSelector.fetchOptions(valueString, function(menuOptions){
                if(menuOptions.length > 0){
                   pathSelector.addOptionsExpander();
                }
            });
        };

        pathSelector.getParts=function(){
            var parts=[];
            $(this).find(".level").each(function(index, element){
                parts.push({
                    value: $(element).data("value"),
                    label: $(element).html()
                });
            });
            return parts;
        };

        pathSelector.addValue=function(value){
            var wrappedValue=wrapValue(value);
            this.addValuePart(wrappedValue);
            $(this).data("pathSelectorData").values.push(value);
            var valueString=$(this).find("input").val();
            if(valueString.length > 0){
                valueString += $(this).data("pathSelectorData").options.separator;
            }
            valueString += wrappedValue.value;
            $(this).find("input").val(valueString);
            $(this).find("input").trigger("valueChanged", valueString);
            this.fetchOptions(valueString, function(menuOptions){
                if(menuOptions.length > 0){
                   pathSelector.addOptionsExpander();
                }
            });
        };
        
        pathSelector.getValue=function(level){
            var pathSelector=this;
            var valueString="";
            $(this).find(".level").each(function(index, element){
                if(index < level){
                    if(index > 0){
                        valueString += $(pathSelector).data("pathSelectorData").options.separator;
                    }
                    valueString += $(element).data("value");
                    return true;
                }else{
                    return false;
                }
            });
            return valueString;
        };

        pathSelector.removeLastLevels=function(levelCount){
            var pathSelector=this;
            var level=$(pathSelector).find(".level").length-levelCount;
            $(pathSelector).find(".level").each(function(index, el){
                if(index >= level){
                    $(el).remove();
                }
            });
            $(pathSelector).find(".arrowButton").each(function(index, el){
                if(index > level){
                    $(el).remove();
                }
            });
            var valueString="";
            $(pathSelector).find(".level").each(function(index, element){
                if(index > 0){
                    valueString += $(pathSelector).data("pathSelectorData").options.separator;
                }
                valueString += $(element).data("value");
            });
            $(pathSelector).find("input").val(valueString);
            $(this).find("input").trigger("valueChanged", valueString);
        };

        /* UI */

        pathSelector.addValuePart=function(o){
            var level=$(pathSelector).find(".level").length;
            $("<a href='javascript:void(0)' class='level'></a>")
                .appendTo(this)
                .data("value", o.value)
                .data("level", level)
                .html(o.label);
        };
        
        pathSelector.addOptionsExpander=function(){
            var button=$("<span class='arrowButton' level=''><a href='javascript:void(0)' class='ui-icon ui-icon-triangle-1-e'></a></span>");
            $(this).append(button);
            configureExpanderButton(this, button.get(0));
        };

        pathSelector.fetchOptions=function(value, callbackWhenFetched){
            var pathSelectorData=$(this).data("pathSelectorData");
            var menuOptions=null;
            if(pathSelectorData.cache[value]){
                menuOptions=pathSelectorData.cache[value];
                callbackWhenFetched(menuOptions);
            }else{
                if(isString(pathSelectorData.optionsAccesor)){
                    $.getJSON(pathSelectorData.optionsAccesor, {
                            value: value
                        }, function(options){
                            menuOptions=options;
                            pathSelectorData.cache[value]=menuOptions;
                            callbackWhenFetched(menuOptions);
                    });
                }else if(isFunction(pathSelectorData.optionsAccesor)){
                    /* Transform the options if needed */
                    var subvalues;
                    if(value == ""){
                        subvalues=[];
                    }else{
                        subvalues=value.split(pathSelectorData.options.separator);
                    }
                    var options=pathSelectorData.optionsAccesor(value, subvalues);
                    menuOptions=options != null ? options: [];
                    pathSelectorData.cache[value]=menuOptions;
                    callbackWhenFetched(menuOptions);
                }
            }
        };

        pathSelector.showOptionsMenu=function(menuOptions){
            var html="";
            $.each(menuOptions, function(index, option){
                var wrappedValue=wrapValue(option);
                html+="<li><a href='#"+wrappedValue.value+"'>"+wrappedValue.label+"</a></li>\n";
            });
            $(this).find(".contextMenu").html(html);
            $(this).find(".contextMenu").get(0).proccessHTML();
        };
        /*
        $(pathSelector).find(".level").live("click", function(){
            pathSelector.removeLastLevels(($(pathSelector).find(".level").length -1) - $(this).data("level"));
        });
        */               
        $(pathSelector).on("click", ".level", "",  function(){
            pathSelector.removeLastLevels(($(pathSelector).find(".level").length -1) - $(this).data("level"));
        });

        pathSelectorData.options=pluginOptions;
        pathSelectorData.cache=new Object();
        if(isFunction(optionsAccesor) || isString(optionsAccesor)){
            pathSelectorData.optionsAccesor=optionsAccesor;
        }else{
            pathSelectorData.optionsAccesor=function(value, subvalues){
                return optionsAccesor[subvalues.length];
            };
        }

        /* Set the init value (empty value) */
        pathSelectorData.values=[];
        if(pathSelectorData.options.initValue){
            pathSelector.setParts(pathSelectorData.options.initValue);
        }else{
            pathSelector.setParts([]);
        }
    }

    function isFunction(o){
        return typeof o == "function";
    }

    function isString(o){
        return typeof o == "string";
    }

    function wrapValue(o){
        if(o.value){
            return o.label ? o : {value: o.value, label: o.value};
        }else{
            return {value: o, label: o.toString()};
        }
    }

    function configureExpanderButton(pathSelector, expanderButton){
        $(expanderButton).contextMenu(
        {
            menu: $(pathSelector).find(".contextMenu").get(0),
            afterHiding: function(){
                $(pathSelector).find("a").removeClass("pressed");
            },
            menuShown:function(arrowElement){
                var level=getLevelOfArrow(arrowElement);
                var menuOptions=$(pathSelector).data("pathSelectorData").cache[pathSelector.getValue(level)];
                if(! menuOptions){
                    pathSelector.fetchOptions(pathSelector.getValue(level), function(menuOptions2){
                        pathSelector.showOptionsMenu(menuOptions2);
                    });
                }else{
                    pathSelector.showOptionsMenu(menuOptions);
                }
            }
        },
        function(option, el, position){
            var level=getLevelOfArrow($(el));
            pathSelector.removeLastLevels($(pathSelector).find(".level").length-level);
            pathSelector.addValue(option);
        }
        );
    }

    function getLevelOfArrow(jArrowButton){
        var level=jArrowButton.prev(".level").data("level");
        if(!level && level != 0){
            level=-1;
        }
        level=level+1;
        return level;
    }

    function valueChanged(input, propName, value){
        /* Get options */
        $(input).parent().get(0).fetchOptions(value, function(options){
            if(options.length > 0){
                $(input).parent().get(0).appendLevelSelector();
            }
        });
        
        /* Fire jQuery Event */
        $(input).trigger("valueChanged", value);
    }

})(jQuery);