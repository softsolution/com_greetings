function greetingPage(page, module_id){

    $.post('/modules/mod_greetings/ajax/latest.php', {'module_id': module_id, 'page':page}, function(data){
		$('div#module_ajax_'+module_id).html(data);
	});

}

function getForm(module_id){
    var formexist = $('input#formexist').val();	
    if (formexist){
        $('#add_greetingform').toggle();

    } else {
        $('#greeting_preloader').show();
        $.post('/modules/mod_greetings/ajax/getform.php', {'module_id': module_id}, function(data){
                    $('#add_greetingform').html(data);
                    $('#add_greetingform').show();
            });
        $('#greeting_preloader').hide();
    }
}

function hideGreetingForm(){
    $('#add_greetingform').hide();
}

function hideGreetingForm(){
    $('#add_greetingform').hide();
}

function sendGreetings(){
    if($('#mod_description').attr('value').length < 10){
        alert('Текст поздравления слишком короткий!');	
    } else {
	$('#greeting_preloader').show();
	$('#gosend').attr('disabled', 'disabled');
	$('#gosend').val('Загружаю');

        var sid = $('#sid').val();
        var atchimg = $('#attach_img').val();
        if(atchimg){
            loadImage('imageurl', sid);
        } else {
            sendForm();
			$('#greeting_preloader').hide();
			$('#gosend').attr('disabled', '');
			$('#gosend').val('Добавить');
        }
		
		
    }
}

function sendForm(){
    //отправляем форму
    var options = {success: showGreetings};
    $("form#greetingsform").ajaxSubmit(options);
}

function showGreetings(responseText)  { 
	$('#greeting_preloader').hide();
	if (responseText) {
            $('#add_greetingform').html(responseText);
            //status
			var status = $('input#status').val();
			if (status =='sendok'){
			//reload module
			var module_id = $('#module_id').attr('data-module-id');
			greetingPage(1, module_id);
			}
	}
}

function loadImage(field_id, session_id){

    $.ajaxFileUpload (
    {
        url:'/modules/mod_greetings/ajax/imginsert.php', 
        secureuri:false,
        fileElementId:'attach_img',
        dataType: 'json',
        success: function (data, status)
        {
            if(typeof(data.error) != 'undefined')
            {
                if(data.error != '')
                {
                    alert('Ошибка: '+data.error);
					$('#greeting_preloader').hide();
                }else
                {
                    imageLoaded(field_id, data.msg);
                }
            }
        },
        error: function (data, status, e)
        {
            alert('Ошибка! '+e);
			$('#greeting_preloader').hide();
        }
    }
    )
		
    return false;
}


function imageLoaded(field_id, data){
   var fileurl = '/upload/greetings/'+data;
   $('#imageurl').val(fileurl);
   sendForm(); 
   $('#gosend').attr('disabled', '');
   $('#gosend').val('Добавить');
}