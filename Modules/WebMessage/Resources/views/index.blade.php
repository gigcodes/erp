<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
	<title>Whatsapp</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css">
	<link rel="stylesheet" href="/css/web-message.css">
</head>

<body>
	
	<div class="container-fluid" id="main-container">
		<div class="row h-100">
			<div class="col-12 col-sm-5 col-md-4 d-flex flex-column" id="chat-list-area" style="position:relative;">

				<!-- Navbar -->
				<div class="row d-flex flex-row align-items-center p-2" id="navbar">
					<img alt="Profile Photo" class="img-fluid rounded-circle mr-2" style="height:50px; cursor:pointer;" onclick="showProfileSettings()" id="display-pic">
					<div class="text-white font-weight-bold" id="username"></div>
					<div class="nav-item dropdown ml-auto">
						<a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v text-white"></i></a>
						<div class="dropdown-menu dropdown-menu-right">
							<a class="dropdown-item" href="#">New Group</a>
							<a class="dropdown-item" href="#">Archived</a>
							<a class="dropdown-item" href="#">Starred</a>
							<a class="dropdown-item" href="#">Settings</a>
							<a class="dropdown-item" href="#">Log Out</a>
						</div>
					</div>
				</div>

				<!-- Chat List -->
				<div class="row" id="chat-list" style="overflow-y:scroll;height: 550px;"></div>

				<!-- Profile Settings -->
				<div class="d-flex flex-column w-100 h-100" id="profile-settings">
					<div class="row d-flex flex-row align-items-center p-2 m-0" style="background:#009688; min-height:65px;">
						<i class="fas fa-arrow-left p-2 mx-3 my-1 text-white" style="font-size: 1.5rem; cursor: pointer;" onclick="hideProfileSettings()"></i>
						<div class="text-white font-weight-bold">Profile</div>
					</div>
					<div class="d-flex flex-column" style="overflow:auto;">
						<img alt="Profile Photo" class="img-fluid rounded-circle my-5 justify-self-center mx-auto" id="profile-pic">
						<input type="file" id="profile-pic-input" class="d-none">
						<div class="bg-white px-3 py-2">
							<div class="text-muted mb-2"><label for="input-name">Your Name</label></div>
							<input type="text" name="name" id="input-name" class="w-100 border-0 py-2 profile-input">
						</div>
						<div class="text-muted p-3 small">
							This is not your username or pin. This name will be visible to your WhatsApp contacts.
						</div>
						<div class="bg-white px-3 py-2">
							<div class="text-muted mb-2"><label for="input-about">About</label></div>
							<input type="text" name="name" id="input-about" value="" class="w-100 border-0 py-2 profile-input">
						</div>
					</div>
					
				</div>
			</div>

			<!-- Message Area -->
			<div class="d-none d-sm-flex flex-column col-12 col-sm-7 col-md-8 p-0 h-100" id="message-area">
				<div class="w-100 h-100 overlay"></div>

				<!-- Navbar -->
				<div class="row d-flex flex-row align-items-center p-2 m-0 w-100" id="navbar">
					<div class="d-block d-sm-none">
						<i class="fas fa-arrow-left p-2 mr-2 text-white" style="font-size: 1.5rem; cursor: pointer;" onclick="showChatList()"></i>
					</div>
					<a href="#"><img src="https://via.placeholder.com/400x400" alt="Profile Photo" class="img-fluid rounded-circle mr-2" style="height:50px;" id="pic"></a>
					<div class="d-flex flex-column">
						<div class="text-white font-weight-bold" id="name"></div>
						<div class="text-white small" id="details"></div>
					</div>
					<div class="d-flex flex-row align-items-center ml-auto">
						<a href="#"><i class="fas fa-search mx-3 text-white d-none d-md-block"></i></a>
						<a href="#"><i class="fas fa-paperclip mx-3 text-white d-none d-md-block"></i></a>
						<a href="#"><i class="fas fa-ellipsis-v mr-2 mx-sm-3 text-white"></i></a>
					</div>
				</div>

				<!-- Messages -->
				<div class="d-flex flex-column" id="messages"></div>

				<!-- Input -->
				<div class="d-none justify-self-end align-items-center flex-row" id="input-area">
					<a href="#"><i class="far fa-smile text-muted px-3" style="font-size:1.5rem;"></i></a>
					<input type="text" name="message" id="input" placeholder="Type a message" class="flex-grow-1 border-0 px-3 py-2 my-3 rounded shadow-sm">
					<i class="fas fa-paper-plane text-muted px-3" style="cursor:pointer;" onclick="sendMessage()"></i>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		let contactList = JSON.parse('<?php echo json_encode($jsonCustomer) ?>');
		let messages = JSON.parse('<?php echo json_encode($jsonMessage) ?>');
	</script>
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
	<script src="/js/web-message/datastore.js"></script>
	<script src="/js/web-message/date-utils.js"></script>
	<script src="/js/web-message/script.js"></script>
</body>

</html>