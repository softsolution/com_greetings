function greetingPage(page, module_id){
    $('.loading').show();
    $.post('/modules/mod_greetings/ajax/latest.php', {'module_id': module_id, 'page':page}, function(data){
        $('div#module_ajax_'+module_id).html(data);
        $('.loading').hide();
    });
}