console.log('Sascha');
(function($) {
	$( document ).ready(function() {
    	console.log( "ready!" );
    	// delete handler
    	$('.delete').on('click', function(event) {
    		event.stopPropagation();
    		todoItem = $(this).closest('.todo__item')[0];
    		todoItem = $(todoItem);
    		todoItem.remove();
    		itemId = todoItem.data('id');
    		console.log(itemId);
    		console.log('AJAX CALL!!!');
    		$.ajax({
			  	method: "POST",
			  	url: "../../todo/delete",
			  	data: { id: itemId },
			  	success: function(data) {
			        console.log(data);
			    },
			    error: function(data) {
			        console.log(data);
			   	}
			});
    	});
    	// done handler
    	$('.done').on('click', function(event) {
    		event.stopPropagation();
    		$this = $(this);
    		todoItem = $(this).closest('.todo__item')[0];
    		todoItem = $(todoItem);
    		//todoItem.remove();
    		itemId = todoItem.data('id');
    		console.log(itemId);
    		console.log('AJAX CALL!!!');
    		checked = $this.prop('checked');
    		$.ajax({
			  	method: "POST",
			  	url: "../../todo/done",
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

    	// todo expand handler
    	$('.list-group-item').on('click', function() {
    		$(this).find('.todo__body').slideToggle();
    	});

    	// form expand handler
    	$('.todo__add').on('click', function() {
    		$('.todo__form').slideToggle();
    	});

        // sortable
        $( "#todolist" ).sortable({
            update: function( event, ui ) {
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
                    url: "../../todo/sort",
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