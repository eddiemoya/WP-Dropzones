var wmbWidgets;
(function($) {
    wmbWidgets = {


        init : function() {
            $('form.dropzone-settings').submit(function(e){
                e.preventDefault();
                var form = $(this).closest(".dropzone-settings");
                
                var data = {
                    action: 'widgetpress_dropzone_settings',
                    id : $('.dropzone_id', form).val(),
                    type : $('.dropzone_type', form).val(),
                    span: $('.widgetpress_dropzone_span', form).val()
                }

                $.post(ajaxurl, data, function(r){
                   // console.log(r);
                });
                console.log(data);
            })

            var rem, sidebars = $('div.widgets-sortables'), isRTL = !! ( 'undefined' != typeof isRtl && isRtl ),
            margin = ( isRtl ? 'marginRight' : 'marginLeft' ), the_id;

            $('#widgets-right').children('.widgets-holder-wrap').children('.sidebar-name').click(function(){
                var c = $(this).siblings('.widgets-sortables'), p = $(this).parent();
                if ( !p.hasClass('closed') ) {
                    c.sortable('disable');
                    p.addClass('closed');
                } else {
                    p.removeClass('closed');
                    c.sortable('enable').sortable('refresh');
                }
            });

            $('#widgets-left').children('.widgets-holder-wrap').children('.sidebar-name').click(function() {
                $(this).parent().toggleClass('closed');
            });

            sidebars.each(function(){
                if ( $(this).parent().hasClass('inactive') )
                    return true;

                var h = 50, H = $(this).children('.widget').length;
                h = h + parseInt(H * 48, 10);
                $(this).css( 'minHeight', h + 'px' );
            });

            $('a.widget-action').live('click', function(){
                var css = {}, widget = $(this).closest('div.widget'), inside = widget.children('.widget-inside'), w = parseInt( widget.find('input.widget-width').val(), 10 );

                if ( inside.is(':hidden') ) {
                    if ( w > 250 && inside.closest('div.widgets-sortables').length ) {
                        css['width'] = w + 30 + 'px';
                        if ( inside.closest('div.widget-liquid-right').length )
                            css[margin] = 235 - w + 'px';
                        widget.css(css);
                    }
                    wmbWidgets.fixLabels(widget);
                    inside.slideDown('fast');
                } else {
                    inside.slideUp('fast', function() {
                        widget.css({
                            'width':'', 
                            margin:''
                        });
                    });
                }
                return false;
            });

            $('input.widget-control-save').live('click', function(){
                wmbWidgets.save( $(this).closest('div.widget'), 0, 1, 0 );
                return false;
            });

            $('a.widget-control-delete').live('click', function(){
                wmbWidgets.deleteWidget( $(this).closest('div.widget') );
                return false;
            });

            $('a.widget-control-remove').live('click', function(){
                wmbWidgets.removeWidget( $(this).closest('div.widget') );
                return false;
            });

            $('a.widget-control-close').live('click', function(){
                wmbWidgets.close( $(this).closest('div.widget') );
                return false;
            });

            sidebars.children('.widget').each(function() {
                wmbWidgets.appendTitle(this);
                if ( $('p.widget-error', this).length )
                    $('a.widget-action', this).click();
            });
            $('body').live('mouseover', function(){
            $('#widget-list').children('.widget').draggable({
                connectToSortable: 'div.widgets-sortables',
                handle: '> .widget-top > .widget-title',
                distance: 2,
                helper: 'clone',
                zIndex: 5,
                containment: 'document',
                start: function(e,ui) {
                    ui.helper.find('div.widget-description').hide();
                    the_id = this.id;
                },
                stop: function(e,ui) {
                    if ( rem )
                        $(rem).hide();

                    rem = '';
                }
            });
        

            sidebars.sortable({
                placeholder: 'widget-placeholder',
                items: '> .widget',
                handle: '> .widget-top > .widget-title',
                cursor: 'move',
                distance: 2,
                containment: 'document',
                start: function(e,ui) {
                    ui.item.children('.widget-inside').hide();
                    ui.item.css({
                        margin:'', 
                        'width':''
                    });
                },
                stop: function(e,ui) {
                    var helper = $(ui.helper);
                    var item   = $(ui.item);
                    var type = $(this).data('type');

                    if(type == 'layout'){
                        add_action = 'widgetpress_add_dropzone';
                    } else {
                        add_action = 'widgetpress_add_widget';
                    }
                    //Only do this if this had no ID (aka, is a new widget, not a resort)
                    if(item.find('.widget_ID').val() == ""){
                        var data = {
                            action:     add_action,
                            widget_ID:  item.find('.widget_ID').val(),
                            widget_class: item.find('.widget-class').val(),
                            dropzone_type : $(this).data('type'),
                            dropzone_id : $(this).data('id')
                        };

                        //console.log(data)
                        $.post( ajaxurl, data, function(r){

                            if(type == 'dropzone'){
                                ui.item.find('.widget_ID').val(r) 
                                wmbWidgets.save( ui.item, 0, 1, 0 ); // save widget
                            }
                            //console.log(r)

                        //ui.item.replaceWith(item);/               
                        });
                    }
                    //console.log(ui.item);
                    var dropzone_type = $(this).data('type');
                    var dropzone_id = $(this).data('id');

                    sb = $('.widget', this).sortable('toArray');
                    //console.log(sb)
                    sb.each(
                        function(index){
                            var orderdata = {
                                action:     'widgetpress_sort_widget', 
                                dropzone_type : dropzone_type,
                                dropzone_id : dropzone_id,
                                widget_ID:  $('.widget_ID', this).val(),
                                order:    index
                            }
                            //console.log(index, orderdata)
                            $.post( ajaxurl, orderdata, 
                                function(r){
                                    //console.log(r)
                                }          
                            );
                        }
                    );

                    if ( ui.item.hasClass('ui-draggable') && ui.item.data('draggable') )
                        ui.item.draggable('destroy');

                    if ( ui.item.hasClass('deleting') ) {
                        wmbWidgets.save( ui.item, 1, 0, 1 ); // delete widget
                        ui.item.remove();
                        return;
                    }

                    var add = ui.item.find('input.add_new').val(),
                    n = ui.item.find('input.multi_number').val(),
                    id = the_id,
                    sb = $(this).attr('id');

                    ui.item.css({
                        margin:'', 
                        'width':''
                    });
                    the_id = '';

                    if ( add ) {
                        if ( 'multi' == add ) {
                            ui.item.html( ui.item.html().replace(/<[^<>]+>/g, function(m){
                                return m.replace(/__i__|%i%/g, n);
                            }) );
                            ui.item.attr( 'id', id.replace('__i__', n) );
                            n++;
                            $('div#' + id).find('input.multi_number').val(n);
                        } else if ( 'single' == add ) {
                            ui.item.attr( 'id', 'new-' + id );
                            rem = 'div#' + id;
                        }
                        wmbWidgets.save( ui.item, 0, 0, 1 );
                        ui.item.find('input.add_new').val('');
                        ui.item.find('a.widget-action').click();
                        return;
                    }
                                
                    wmbWidgets.saveOrder(sb);
                },
                receive: function(e, ui) {
                    var sender = $(ui.sender);

                    if ( !$(this).is(':visible') || this.id.indexOf('orphaned_widgets') != -1 )
                        sender.sortable('cancel');

                    // if ( sender.attr('id').indexOf('orphaned_widgets') != -1 && !sender.children('.widget').length ) {
                    //     sender.parents('.orphan-sidebar').slideUp(400, function(){
                    //         $(this).remove();
                    //     });
                    // }
                }
            }).sortable('option', 'connectWith', 'div.widgets-sortables').parent().filter('.closed').children('.widgets-sortables').sortable('disable');

            $('#available-widgets').droppable({
                tolerance: 'pointer',
                accept: function(o){
                    return $(o).parent().attr('id') != 'widget-list';
                },
                drop: function(e,ui) {
                    
                    ui.draggable.addClass('deleting');
                    $('#removing-widget').hide().children('span').html('');
                },
                over: function(e,ui) {
                    ui.draggable.addClass('deleting');
                    $('div.widget-placeholder').hide();

                    if ( ui.draggable.hasClass('ui-sortable-helper') )
                        $('#removing-widget').show().children('span')
                        .html( ui.draggable.find('div.widget-title').children('h4').html() );
                },
                out: function(e,ui) {
                    ui.draggable.removeClass('deleting');
                    $('div.widget-placeholder').show();
                    $('#removing-widget').hide().children('span').html('');
                }
            });
            });
        },

        saveOrder : function(sb) {
            //console.log(sb);
            if ( sb )
                $('#' + sb).closest('div.widgets-holder-wrap').find('img.ajax-feedback').css('visibility', 'visible');

            var b = {
                action: 'widgets-order',
                savewidgets: $('#_wpnonce_widgets').val(),
                sidebars: []
            };

            $('div.widgets-sortables').each( function() {
                if ( $(this).sortable )
                    b['sidebars[' + $(this).attr('id') + ']'] = $(this).sortable('toArray').join(',');
            });

            $.post( ajaxurl, b, function() {
                $('img.ajax-feedback').css('visibility', 'hidden');
            });

            this.resize();
            //wmbWidgets.refreshMetabox();
            
        },
        //add_widget : function(data)
        save : function(widget, del, animate, order) {

            $('.ajax-feedback', widget).css('visibility', 'visible');

            var dz = widget.closest('div.widgets-sortables');
            var form = widget.find('form .widget-content input, form .widget-content select, form .widget-content textarea').serialize();

            var data = {
                action: 'widgetpress_save_widget',
                savewidgets: $('#_wpnonce_widgets').val(),

                widget_ID: $('.widget_ID', widget).val(),
                widget_class: $('.widget-class' , widget).val(),
                widget_span: $('.widgetpress_span', widget).val(),
                post_id : $('#post_ID').val(),
                dropzone_type: $(dz).data('type'),
                dropzone_id: $(dz).data('id'),
                meta: form,
                //type : dztype
            };

            $.post( ajaxurl, data, function(r){
                $('.ajax-feedback').css('visibility', 'hidden');
                if ( r && r.length > 2 ) {                
                    $('div.widget-content', widget).html(r);
                    wmbWidgets.appendTitle(widget);
                    wmbWidgets.fixLabels(widget);
                }                  
            });
                    
        },

        //Delete the widget post object
        deleteWidget: function(widget){

            $('.ajax-feedback', widget).css('visibility', 'visible');

            var data = {
                action: 'widgetpress_delete_widget',
                widget_ID: $('.widget_ID', widget).val(),
            }

            $.post( ajaxurl, data, function(r){
                $('.ajax-feedback').css('visibility', 'hidden');
                 widget.slideUp('fast', function(){
                    $(this).remove();
                });                  
            });
        },

        //Remove a widget from a given dropzone taxonomy
        removeWidget: function(widget){
            $('.ajax-feedback', widget).css('visibility', 'visible');

            var dz = widget.closest('div.widgets-sortables');

            var data = {
                action: 'widgetpress_remove_widget',
                widget_ID: $('.widget_ID', widget).val(),
                dropzone_type: $(dz).data('type'),
                dropzone_id: $(dz).data('id'),
            }

            $.post( ajaxurl, data, function(r){
                $('.ajax-feedback').css('visibility', 'hidden');
                 widget.slideUp('fast', function(){
                    $(this).remove();
                });         
            });
        },

        refreshMetabox: function(){
            
            var object = {
                action: 'refresh-metabox',
                post: $('#post_ID').val()
            }
            
            $.post(ajaxurl, object, function(r){
                $('#wpdz-metabox-sidebars .inside .wrap').replaceWith(r);
               // console.log(r);
            });
            wmbWidgets.init();
        },
        appendTitle : function(widget) {
            //console.log(widget);
            var title = $('input[id*="-title"]', widget).val() || '';

            if ( title )
                title = ': ' + title.replace(/<[^<>]+>/g, '').replace(/</g, '&lt;').replace(/>/g, '&gt;');

            $(widget).children('.widget-top').children('.widget-title').children()
            .children('.in-widget-title').html(title);

        },

        resize : function() {
            $('div.widgets-sortables').each(function(){
                if ( $(this).parent().hasClass('inactive') )
                    return true;

                var h = 50, H = $(this).children('.widget').length;
                h = h + parseInt(H * 48, 10);
                $(this).css( 'minHeight', h + 'px' );
            });
        },

        fixLabels : function(widget) {
            //console.log(widget);
            widget.children('.widget-inside').find('label').each(function(){
                var f = $(this).attr('for');
                if ( f && f == $('input', this).attr('id') )
                    $(this).removeAttr('for');
            });
        },

        close : function(widget) {
            //console.log(widget);
            widget.children('.widget-inside').slideUp('fast', function(){
                widget.css({
                    'width':'', 
                    margin:''
                });
            });
        }
    };

    $(document).ready(function($){
        wmbWidgets.init();
    });

})(jQuery);
    
    