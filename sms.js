var initial_window_height=325;
var initial_textarea_height=21*4;

var window_inner_height=286;
var window_inner_width=284;

var window_width=300;
var button_spacing=10;

var height_diff=window.outerHeight-window.innerHeight;
var wanted_outer_height=window_inner_height+height_diff;

function resize()
{
	window.resizeTo(window_width,wanted_outer_height);
	buttonwidth();
	document.getElementById('message').style.height = initial_textarea_height + "px";
}
function buttonwidth() //Set correct send and log off button width
{
	width=document.getElementById('content').scrollWidth; //Available width

	halfwidth=width/2;
	halfwidth=halfwidth-(button_spacing/2);

	document.getElementById('button_send').style.width=halfwidth+'px';
	document.getElementById('button_logoff').style.width=halfwidth+'px';
	document.getElementById('button_logoff').style.marginLeft=button_spacing+'px';

}

function count_chars()
{
	message=document.getElementById('message');
	document.getElementById('charcount').textContent="Antall tegn: "+message.value.length; //Show character count

	if(message.scrollHeight>initial_textarea_height) //Only change size if higher than initial size
		message.style.height = message.scrollHeight + "px"; //Set the textarea height to scroll height

	var newheight=initial_window_height+message.scrollHeight-initial_textarea_height; //Calculate new window height

	if(newheight>initial_window_height) //Resize window
		window.resizeTo(window_width,newheight);

	if(message.value.length==0) //Reset size when message is empty
	{
		message.style.height = initial_textarea_height + "px"; //Set the textarea height to scroll height
		window.resizeTo(window_width,initial_window_height);
	}
}
function providerfield()
{
	if(document.getElementById('sms_provider').value=='pswincom')
	{
		var span=document.getElementById('provider_fields');
		var input=document.createElement('input');
		input.setAttribute('name','sms_sender');
		input.setAttribute('placeholder','Avsender');
		span.appendChild(input);
	}
}