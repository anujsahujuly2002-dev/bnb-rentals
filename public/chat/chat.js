const chatBoxs = document.querySelector('.msg-body');
$(document).on('keyup','.input-field',function(){
    if($(this).val() != ""){
        $('button').removeAttr("disabled");
    }else{
        $('button').attr('disabled', 'disabled');
    }

})


sendMessage = async (e)=>{
  try {
    e.preventDefault();
    // let formData = new FormData(form);
    let data = {
        "reciverId":$(".reciver_id").val(),
        'msg':$(".input-field").val()
    };
    const response = await fetch(site_url+'/insert-chat',{
        method: "POST",
        body: JSON.stringify(data),
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            'Content-Type': 'application/json',
        }
    });
    const results = await response.json();
    if(results.status){
        $(".input-field").val('');
        scrollToBottom();
    }
  } catch (error) {
    console.log(error.message);
  }
    
}


setInterval(async()=>{
	let data = {
		'id':$(".reciver_id").val()
	};
	const response = await fetch(site_url+'/get-chat',{
        method: "POST",
        body: JSON.stringify(data),
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            'Content-Type': 'application/json',
        }
    });
	const results = await response.json();
	if(results.status){
		$("#msg-body").html(results.data);
        if($('button').is('[disabled=disabled]')){
            scrollToBottom();
        }
        
	}
},500)


function scrollToBottom(){
    $("#msg-body").animate({ scrollTop: $('#msg-body').prop("scrollHeight")}, 1000);
  }