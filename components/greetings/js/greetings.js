$(document).ready(function(){
$('td:has(p.novalid)').children('input').addClass('novalidfield');
$('td:has(p.novalid)').children('textarea').addClass('novalidfield');
$("#description").focusin(function(){
    $(this).removeClass('novalidfield');
    $('#descriptioncheck').html('');
});
})

function sendGreetings(){
if($('#description').attr('value').length < 10){
alert('Ваше поздравление слишком короткое!');	
} else {
document.greetingsform.submit();	
}	
}