 //verifying both passwords on registration
$('#cpwd').keyup(function(){
		var pwd = $('#pwd').val();
		if($(this).val()!=pwd){
			$('#note').html('password do not match');
			alert('wrong');
		}
		else{
			$('#note').html('');
		}
	});
	
 /*$('#username').keyup(function (){
	var username = $(this).val();
	$('#username_status').text('searching....');
	
	if(username != ''){
		$.post('functions.php',{username: username},function(data){
			$('#username_status').text(data);
		});
	}
	else{
		$('#username_status').text('');
	}
});

 $(document).ready(function (){
 	$('table tr:odd').addclass('even');
 	
 });*/

$(document).ready(function (){

 	//styling all report tables 
 	$('.table tr:even').css('background-color','#c1cddb');
 	$('.table tr:even').css('color','black');
 	$('.table tr:odd').css('background-color','white');
 	$('.table tr:odd').css('color','black');

 	//styling all input elemets
 	$('input[type="text"],input[type="password"]').css('width','200px');

 	$('input[type="text"],input[type="password"]').css('border-radius','5px');
 	$('input[type="text"],input[type="password"]').css('line-height','25px');
 	$('input').css('line-height','25px');
 	$('select').css('width','200px');
 	$('select').css('height','25px');
 	$('select').css('border-radius','5px');
	$('.report ul li').hide();

 });

$('.report>a').click(function(){
	
	$('.report ul li').show();
});