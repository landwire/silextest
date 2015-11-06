(function($) {
    console.log('Sascha');
    var aborted;
    var deleteNotify;

	$( document ).ready(function() {
    	console.log( "ready!" );
        
        // abort handler for delete popup
        $('body').on('click', '.alert', function() {
            console.log( 'Aborted');
            aborted = true;
            console.log(aborted);
            deleteNotify.close();
            setTimeout(function(){
                $.notify({
                    // options
                    message: "<h4>Deletion aborted.</h4>",
                        },{
                        // settings
                        type: 'success',
                        allow_dismiss: false,
                        delay: 10,
                        showProgressbar: false,
                });
            }, 1000);
        });

    	// delete handler
    	$('.delete').on('click', function(event) {
    		event.stopPropagation();
            aborted = false;
            
            deleteNotify = $.notify({
            // options
            title: "<h4>Todo will be deleted</h4>",
            message: "Click this box to abort deletion."
                },{
                    // settings
                    type: 'danger',
                    allow_dismiss: true,
                    delay: 5000,
                    showProgressbar: true,
            });
            var that = $(this);
            setTimeout(function(){
                if (!aborted) {
                    var todoItem = that.closest('.todo__item')[0];
                    todoItem = $(todoItem);
                    todoItem.remove();
                    itemId = todoItem.data('id');
                    console.log(itemId);
                    console.log('AJAX CALL!!!');
                    $.ajax({
                        method: "POST",
                        url: "/todo/delete",
                        data: { id: itemId },
                        success: function(data) {
                            console.log(data);
                            $.notify({
                                // options
                                message: "<h4>Item deleted.</h4>",
                                    },{
                                    // settings
                                    type: 'success',
                                    allow_dismiss: false,
                                    delay: 10,
                                    showProgressbar: false,
                            });
                        },
                        error: function(data) {
                            console.log(data);
                        }
                    });
                }
            }, 7000);
            
    	});
    	// done handler
    	$('.done').on('click', function(event) {
    		event.stopPropagation();
    		$this = $(this);
    		todoItem = $(this).closest('.todo__item')[0];
    		todoItem = $(todoItem);
            todoItem.toggleClass('todo--done');
    		//todoItem.remove();
    		itemId = todoItem.data('id');
    		console.log(itemId);
    		console.log('AJAX CALL!!!');
    		checked = $this.prop('checked');
    		$.ajax({
			  	method: "POST",
			  	url: "/todo/done",
			  	data: { id: itemId, done: checked },
			  	success: function(data) {
			        console.log(data);
			    },
			    error: function(data) {
			        console.log(data);
			   	}
			});
    	});

        // stop todo from opening on click
        $('.edit').on('click', function(event) {
            event.stopPropagation();
        });

        // edit handler
        // nope proper link for now
        /*
        $('.edit').on('click', function(event) {
            event.stopPropagation();
            $this = $(this);
            todoItem = $(this).closest('.todo__item')[0];
            todoItem = $(todoItem);
            //todoItem.remove();
            itemId = todoItem.data('id');
            console.log(itemId);
            console.log('AJAX CALL!!!');
            $.ajax({
                method: "POST",
                url: "todo/edit",
                data: { id: itemId },
                success: function(data) {
                    console.log(data);
                },
                error: function(data) {
                    console.log(data);
                }
            });
        });*/

    	// todo item expand handler
    	$('.list-group-item').on('click', function() {
    		$(this).find('.todo__body').slideToggle();
            $(this).toggleClass('expanded');
    	});

    	// form expand handler
    	$('.todo__add').on('click', function() {
    		$('.todo__form').slideToggle();
    	});

        // sortable
        $( "#todolist" ).sortable({
            update: function( event, ui ) {
                event.stopPropagation();
                console.log(event.target);
                var ids = [];
                $(event.target).find('.todo__item').each(function() {
                    id = $(this).data('id');
                    console.log(id);
                    ids.push(id);
                });
                console.log(ids);
                $.ajax({
                    method: "POST",
                    url: "/todo/sort",
                    data: { order: ids },
                    success: function(data) {
                        console.log(data);
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });
            }
        });
        $( "#todolist" ).disableSelection();
    });
})(jQuery);