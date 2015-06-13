$(function(){
  greetings = {
    deleteItem: function(item_id, csrf_token) {
        core.confirm('Вы действительно хотите удалить это поздравление?', null, function(){
            $.post('/greetings/delete'+item_id+'.html', {csrf_token: csrf_token}, function(result){
                if(result.error == false){
                    window.location.href = result.redirect;
                }
            }, 'json');
        });
    },
    showCollection: function() {
        $('#collection_block').slideUp('fast');
        $('#collection_block').slideDown('fast');
        $('#collection_link').hide();
    },
    hideCollection: function() {
        $('#collection_block').slideDown('fast');
        $('#collection_block').slideUp('fast');
        $('#collection_link').show();
    },
    selectDefault: function() {
        $('#file').val('default.jpg');
        $('#choose_img').attr('src', '/upload/greetings/small/default.jpg');
        greetings.hideCollection();
        $('#select_image').show();
        $('#collist a').removeClass('selected');
    }
  }
});