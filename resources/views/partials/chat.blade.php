<style>
.chat{
	margin-top: auto;
	margin-bottom: auto;
}


.card_chat{
	height: 500px !important;
	border-radius: 15px !important;
	position: relative;
    display: -ms-flexbox;
    display: flex;
    -ms-flex-direction: column;
    flex-direction: column;
    min-width: 0;
    word-wrap: break-word;
    background-color: #fff;
    background-clip: border-box;
	border: 1px solid #e5e9f2;
	-webkit-box-orient: vertical;
    -webkit-box-direction: normal;
}
.contacts_body{
	background-color: white !important;
	padding:  0.75rem 0 !important;
	overflow-y: auto;
	white-space: nowrap;
}
.msg_card_body{
	background: #f5f6fa;
	overflow-y: auto;
}
.card-header{
	border-radius: 15px 15px 0 0 !important;
	border-bottom: 0 !important;
}
.card-footer{
border-radius: 0 0 15px 15px !important;
	border-top: 0 !important;
	background: #f1f1f1 !important;
}
.container{
	align-content: center;
}

.type_msg{
	background-color: white !important;
	border:0 !important;
	color:black !important;
	height: 60px !important;
	overflow-y: auto;
}
	.type_msg:focus{
	color : black !important;	
		box-shadow:none !important;
	outline:0px !important;
}
.attach_btn{
border-radius: 15px 0 0 15px !important;
background-color: rgba(0,0,0,0.3) !important;
	border:0 !important;
	color: white !important;
	cursor: pointer;
}
.send_btn{
border-radius: 0 15px 15px 0 !important;
background-color: rgba(0,0,0,0.3) !important;
	border:0 !important;
	color: white !important;
	cursor: pointer;
}
.search_btn{
	border-radius: 0 15px 15px 0 !important;
	background-color: rgba(0,0,0,0.3) !important;
	border:0 !important;
	color: white !important;
	cursor: pointer;
}
.contacts{
	list-style: none;
	padding: 0;
}
.contacts li{
	width: 100% !important;
	padding: 5px 10px;
	margin-bottom: 15px !important;
}
.active_chat{
	background-color: rgba(0,0,0,0.3);
}
.user_img{
	height: 40px;
	width: 40px;
	border:1.5px solid #f5f6fa;

}
.user_img_msg{
	height: 40px;
	width: 40px;
	border:1.5px solid #f5f6fa;

}
.img_cont{
	position: relative;
	height: 40px;
	width: 40px;
}
.img_cont_msg{
	height: 40px;
	width: 40px;
}
.online_icon{
position: absolute;
height: 15px;
width:15px;
background-color: #4cd137;
border-radius: 50%;
bottom: 0.2em;
right: 0.4em;
border:1.5px solid white;
}
.offline{
background-color: #c23616 !important;
}
.user_info{
margin-top: auto;
margin-bottom: auto;
margin-left: 15px;
}
.user_info span{
font-size: 14px;
color: rgba(0, 0, 0, 0.5);
}
.user_info p{
font-size: 10px;
color: currentColor;
}
.video_cam{
margin-left: 50px;
margin-top: 5px;
}
.video_cam span{
color: white;
font-size: 20px;
cursor: pointer;
margin-right: 20px;
}
.msg_cotainer{
margin-top: auto;
margin-bottom: auto;
margin-left: 10px;
border-radius: 25px;
background-color: #f1f1f1;
padding: 10px;
position: relative;
}
.msg_cotainer_send{
margin-top: auto;
margin-bottom: auto;
margin-right: 10px;
border-radius: 25px;
background-color: #f1f1f1;
padding: 10px;
position: relative;
}
.msg_time{
position: absolute;
left: 0;
bottom: -15px;
color: rgba(0, 0, 0, 0.9);
font-size: 10px;
}
.msg_time_send{
position: absolute;
right:0;
bottom: -15px;
color: rgba(255,255,255,0.5);
font-size: 10px;
}
.msg_head{
position: relative;
}
#action_menu_btn{
position: absolute;
right: 10px;
top: 10px;
color: white;
cursor: pointer;
font-size: 20px;
}
.action_menu{
z-index: 1;
position: absolute;
padding: 15px 0;
background-color: rgba(0,0,0,0.5);
color: white;
border-radius: 15px;
top: 30px;
right: 15px;
display: none;
}
.action_menu ul{
list-style: none;
padding: 0;
margin: 0;
}
.action_menu ul li{
width: 100%;
padding: 10px 15px;
margin-bottom: 5px;
}
.action_menu ul li i{
padding-right: 10px;

}
.action_menu ul li:hover{
cursor: pointer;
background-color: rgba(0,0,0,0.2);
}
.new_message_icon{
height: 15px;
width: 15px;
background-color: skyblue;
border-radius: 50%;
bottom: 0.2em;
right: 0.4em;
border: 1.5px solid white;
}
@media(max-width: 576px){
.contacts_card{
margin-bottom: 15px !important;
}
}


</style>


<script>

setInterval(function(){
 getChatsWithoutRefresh();
 getUserList();
 }, 5000);

function getChats(id){
	
	$.ajax({
    	url: "{{ route('livechat.get.message') }}",
    	type: 'POST',
    	dataType: 'json',
    	data: { id : id ,   _token: "{{ csrf_token() }}" },
    })
    .done(function(data) {
    	if(typeof data.data.message != "undefined" && data.length > 0 && data.data.length > 0) {
	        $('#message-recieve').empty().html(data.data.message);
	        $('#message-id').val(data.data.id);
			$('#new_message_count').text(data.data.count);
			$('#user_name').text(data.data.name);
			$("li.active").removeClass("active");
			$("#user"+data.data.id).addClass("active");
    	}
        console.log("success");
    })
    .fail(function() {
    	console.log("error");
    });
    

}


function getChatsWithoutRefresh(){
	var scrolled=0;
	$.ajax({
		url: "{{ route('livechat.message.withoutrefresh') }}",
		type: 'POST',
		dataType: 'json',
		data: { _token: "{{ csrf_token() }}" },
	})
	.done(function(data) {
		 $('#message-recieve').empty().html(data.data.message);
		 $('#message-id').val(data.data.id);
		 $('#new_message_count').text(data.data.count);
		 $('#user_name').text(data.data.name);
		 $("li .active").removeClass("active");
		 $("#user"+data.data.id).addClass("active");
		 scrolled=scrolled+300;
         $(".cover").animate({
			scrollTop:  scrolled
		 });
		console.log(data);
	})
	.fail(function() {
		console.log("error");
	});
}

function getUserList(){
	$.ajax({
		url: "{{ route('livechat.get.userlist') }}",
		type: 'POST',
		dataType: 'json',
		data: { _token: "{{ csrf_token() }}" },
	})
	.done(function(data) {
		 $('#customer-list-chat').empty().html(data.data.message);
		 $('#new_message_count').text(data.data.count);
		 console.log(data);
	})
	.fail(function() {
		console.log("error");
	});

}

function sendMessage(){
    id = $('#message-id').val();
	message = $('#message').val();
	var scrolled=0;
    $.ajax({
    	url: "{{ route('livechat.send.message') }}",
    	type: 'POST',
    	dataType: 'json',
    	data: { id : id ,
			message : message,
		   _token: "{{ csrf_token() }}" 
		   },
    })
    .done(function(data) {
       console.log(data);
		chat_message = '<div class="d-flex justify-content-end mb-4"><div class="msg_cotainer_send"><img src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg" class="rounded-circle user_img_msg"></div><div class="msg_cotainer">'+message+'<span class="msg_time"></span></div></div>';
		$('#message-recieve').append(chat_message);
		$('#message').val('');
		scrolled=scrolled+300;
        $(".cover").animate({
			scrollTop:  scrolled
		});
	})
    .fail(function() {
    	alert('Chat Not Active');
    });
}
//Send File
// function sendFile(){
//     id = $('#message-id').val();
// 	file = $('#imgupload').prop('files')[0];
// 	var fd = new FormData();
// 		fd.append("id", id);
// 		fd.append("file", file);
// 		fd.append("_token", "{{ csrf_token() }}" );
// 	var scrolled=0;
//     $.ajax({
//     	url: "{{ route('livechat.send.file') }}",
//     	type: 'POST',
//     	dataType: 'json',
//     	data: fd,
// 		cache: false,
//         contentType: false,
//         processData: false   
//     })
//     .done(function(data) {
//        console.log(data);
// 		chat_message = '<div class="d-flex justify-content-end mb-4"><div class="msg_cotainer_send"><img src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg" class="rounded-circle user_img_msg"></div><div class="msg_cotainer">'+message+'<span class="msg_time"></span></div></div>';
// 		$('#message-recieve').append(chat_message);
// 		$('#message').val('');
// 		scrolled=scrolled+300;
//         $(".cover").animate({
// 			scrollTop:  scrolled
// 		});
// 	})
//     .fail(function() {
//     	alert('Chat Not Active');
//     });
// }


// function sendImage() {
// 	$('#imgupload').trigger('click');
// }

</script>
