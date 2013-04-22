<?php
session_start();
require_once("cookie.php");
require_once("functions.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title></title>
<script src="./assets/libraries/jquery-1.9.1.min.js"></script>
<script src="http://code.jquery.com/mobile/1.3.0/jquery.mobile-1.3.0.min.js"></script>
<script src="http://code.jquery.com/ui/1.10.2/jquery-ui.js"></script>
<script src="./ct.js"></script>
<script src="assets/libraries/select/select2.js"></script>
<link href='http://fonts.googleapis.com/css?family=Kotta+One|Raleway:400|Average' rel='stylesheet' type='text/css'/>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css" />
<link rel="stylesheet" href="./assets/style.css">
<link rel="stylesheet" href="./assets/libraries/select/select2.css">
<link rel="stylesheet" href="http://code.jquery.com/mobile/1.3.0/jquery.mobile-1.3.0.min.css" />

</head>

<body>
<div id="bg"></div>

<div class="page">
	<div class="top_nav">
    	<div class="logo_top" onclick="location.href='index.php';">
        	<img src="assets/images/logo.png"/>
        </div>
    	<div class="topmenus">
        	<ul>
            	<li id="aboutPage">About</li>
                <div id="signUp">
					<div id="signUp_header"><br>Sign Up</div>
                    <div id="signUp_body">
                        <form id="FormSignUp" method="post" action="signup.php" data-ajax="false">
                        <div id="notifier">Notifier</div>
	                        <input type="text" name="new_firstName" id="new_firstName" data-mini="true" placeholder="Your first name"><input type="text" data-mini="true" id="new_lastName" name="new_lastName" placeholder="Your last name">

                        
                        <input type="text" data-mini="true" id="new_Email" name="new_Email" placeholder="Enter your email address">
						<input type="password" data-mini="true" id="new_Pw" name="new_Pw" placeholder="Set your password">
                        <input type="password" data-mini="true" id="new_Pw_verify" name="new_Pw_verify" placeholder="Enter your password again">
                        <input type="hidden" name="loginType" value=2>
                        <input type="submit" id="FSU_submit" value="Sign up" ><input type="button" value="Cancel" onclick='$("#signUp").fadeOut(300);'>
                        </form>
                    </div>
				</div>
                <?php
					if(!isset($_SESSION['primaryId']) || $_SESSION['primaryId'] == ''){
						echo('
							<li id="loginW">Sign in via Google</li>
			                <div id="loginWindow">
		                	<div id="formComponents">
        	                <form id="Form_keepSigned" action="index.php" method="get" data-ajax="false">
                	        	<input type="checkbox" name="keepSignedIn" value=1 id="keepSignedIn" data-role="none">
                    	        <input type="hidden" name="loginType" value=1> <!-- value 1 for openID Login, value 2 for traditional Login -->
                        		<label for="keepSignedIn">Keep me signed in</label>
            	            </form>
 		                    </div>
                			</div>
							
							<li id="loginWN">Sign in</li>
			                <div id="loginWindowN">
		                	<div id="formComponentsN">
        	                <form id="Form_keepSignedN" action="index.php" method="post" data-ajax="false">
								<input type="text" name="userEmail" placeholder="Email"><input type="password" placeholder="Password" name="userPw">
                	        	<input type="checkbox" name="keepSignedInN" value=1 id="keepSignedInN" data-role="none">
								<label for="keepSignedInN">Keep me signed in</label>
                    	        <input type="hidden" name="loginType" value=2> <!-- value 1 for openID Login, value 2 for traditional Login -->
								<div data-role="fieldcontain">
									<input type="submit" id="normal_signin" value="Sign in"><input type="button" id="normal_signup" value="Sign up"><input type="button" id="normal_help" value="Help!">
									
									
								</div>
                        		
            	            </form>
 		                    </div>
                			</div>
							
							
						');
					}else{
						echo("<li id='logOut'>Log out</li>");
						echo("<li id='userPanel'>".$_SESSION['firstName']."</li>");
					}
				?>
                
        	</ul>
        </div>
        <div id="notification_main"></div>
    </div>
    <div class="main_layout">
	    <div class="main_left">
            <div class="catList" style="text-align: left;">
	            <span class="catListHeader">Friends</span>
    	        <?php
				// get the list of friends
				dbconnect();
				$query = "SELECT * FROM CT_Friends WHERE refUser = ".$_SESSION['primaryId'].";";
				$result = mysql_query($query, $connect);
				$totalNum = mysql_num_rows($result);
				
				while($data = mysql_fetch_array($result)){
					$avatar = getSpecific("CT_User", "avatarPic", "id=".mysql_real_escape_string($data["targetUser"]));
					$firstName = getSpecific("CT_User", "firstName", "id=".mysql_real_escape_string($data["targetUser"]));
					$lastName = getSpecific("CT_User", "lastName", "id=".mysql_real_escape_string($data["targetUser"]));
					echo('
						<div class="friendProfile" id='.$data["targetUser"].' style="width: 127px; height: 127px;">
							<img width=131 src="'.$avatar.'">
							<div class="name">'.htmlentities($firstName).' '.htmlentities($lastName).'</div>
							<div class="btns" data-link='.$data["targetUser"].' style="cursor: pointer; display: none;width: 131px; height: 131px; position: relative; float: left; background-color: rgba(0,0,0,0.7); top: -153px;">
								<div class="unfriend" style="position: relative; text-shadow: 2px 2px 0px #000000; display: inline-block; top: 14px; color: #900000; font-size: 20px; padding-left: 20px; padding-right: 20px; padding-top: 40px; padding-bottom: 40px;">
								Unfriend
								</div>

							</div>
						</div>
					');
				}
				?>
        	    <script>
				$('.friendProfile').mouseover(function(){
					$(this).find('.btns').fadeIn(300);
				});
				
				$('.friendProfile').mouseleave(function(){
					$(this).find('.btns').fadeOut(300);
				});
				
				$('.btns').click(function(){
					var targetId = $(this).attr("data-link");
					
					$.ajax({
						url: 'retrieve.php',
						data: {'mode': 5, 'refUser': <?php echo($_SESSION['primaryId']); ?>, 'targetUser': targetId},
						success: function(data){
							if(data == 1){		
								$('.friendProfile[id='+targetId+']').fadeOut(300);
								$('.myScore .number:eq(2)').text(parseInt($('.myScore .number:eq(2)').text())-1);
							}
						}
					});
				});
				</script>
					
            </div>
	    </div>

        <div class="main_right">
            <div class="itemList">
				<br><div class="detailTitle">Upload Score</div>
					<form id="FormUpload" enctype="multipart/form-data" method="post" action="upload_ok.php" data-ajax="false">
					<div class="scoreDetail">
						<span class="subTitle">General Information</span><br>
						<div class="txt">
							<table>
								<tr><td><span class="key">Title</span></td><td><span class="value2"><input type="text" name="txtTitle" id="txtTitle"></span></td></tr>
								<tr><td><span class="key">Composer</span></td><td><span class="value2"><input type="text" name="txtComposer" id="txtComposer"></span></td></tr>
								<tr><td><span class="key">Compose Year</span></td><td><span class="value2"><input type="text" name="txtComposeYear" id="txtComposeYear"></span></td></tr>
								<tr><td><span class="key">Genre</span></td><td><span class="value2"><input type="text" name="txtGenre" id="txtGenre"></span></td></tr>
								<tr><td><span class="key">Opus Number</span></td><td><span class="value2"><input type="text" name="txtOpusNumber" id="txtOpusNumber"></span></td></tr>
								<tr><td><span class="key">Key</span></td><td><span class="value2"><input type="text" name="txtKey" id="txtKey"></span></td></tr>
								<tr><td><span class="key">Language</span></td><td><span class="value2"><input type="text" name="txtLanguage" id="txtLanguage"></span></td></tr>
								<tr><td><span class="key">Piece Style</span></td><td><span class="value2"><input type="text" name="txtPieceStyle" id="txtPieceStyle"></span></td></tr>
								<tr><td><span class="key">Instrumentation</span></td><td><span class="value2"><input type="text" name="txtInstrumentation" id="txtInstrumentation"></span></td></tr>
							</table>
						</div>
		            </div>
					<div class="scoreDetail">
						<span class="subTitle">Music Score Information</span><br>
						<div class="txt">
							<table>
								<tr><td><span class="key">Copyright</span></td><td><input type="radio" name="txtCopyright" id="txtCopyright" value="0">Under Copyright <input type="radio" name="txtCopyright" id="txtCopyright" value="1">Public Domain</td></tr>
								<tr><td><span class="key">Publish Year</span></td><td><span class="value2"><input type="text" name="txtPublishYear" id="txtPublishYear"></span></td></tr>
								<tr><td><span class="key">Score Image</span></td><td><span class="value2"><input type="file" name="txtScoreImage" id="txtScoreImage"></span></td></tr>
								<tr><td><span class="key">Description</span></td><td><span class="value2"><textarea name="txtDescription" id="txtDescription" wrap="physical"></textarea></span></td></tr>
							</table>
						</div>
					</div>
					<div class="scoreDetail">
						<span class="subTitle">Tags</span><br>
						<div class="txt"><input type="text" name="txtTags" id="txtTags"></div>
					</div>
					<input type="submit">
					</form>	            
            </div>
        </div>
    </div>

    <div class="footer">
    
    </div>
</div>
<script>
// should be exported to a separate JS file
var availableTags, signUpWidth;
var comments = [];

$('#FSU_submit').click(function(e){
	var firstName = $('#new_firstName').val();
	var lastName = $('#new_lastName').val();
	var Email = $('#new_Email').val();
	var Pw = $('#new_Pw').val();
	var Pw_verify = $('#new_Pw_verify').val();
	
	if(firstName == ''){ $('#notifier').html('First name field is empty!').show(); $('#new_firstName').focus(); e.preventDefault(); }
	if(lastName == ''){ $('#notifier').html('Last name field is empty!').show(); $('#new_lastName').focus(); e.preventDefault(); }
	if(Email == ''){ $('#notifier').html('Email field is empty!').show(); $('#new_Email').focus(); e.preventDefault(); }
	if(Pw == ''){ $('#notifier').html('Password field is empty!').show(); $('#new_Pw').focus();  e.preventDefault(); }
	
	if(firstName && lastName && Email && Pw){
		if(Pw !== Pw_verify){
			$('#notifier').html('Password fields do not match!').show(); $('#new_Pw_verify').focus(); e.preventDefault();
		}else{
			$('#FormSignUp').submit();
			e.preventDefault();
		}
	}
});

$(window).resize(function(){
	signUpWidth = $('#signUp').css('width').toString().substr(0,$('#signUp').css('width').toString().length-2);
	signUpHeight = $('#signUp').css('height').toString().substr(0,$('#signUp').css('height').toString().length-2);
	signUpWidth = parseInt(signUpWidth);
	signUpHeight = parseInt(signUpHeight);
	$('#signUp').css({'right': ((window.innerWidth - signUpWidth-50)/2)+'px', 'top': ((window.innerHeight - signUpHeight)/2)+'px'});
	
	/* added in browser.php */
	$('.main_right').css('width', (innerWidth - 317)+'px');
	$('.main_right .textInfo').css({'width': (innerWidth - 500)+'px'});
});

$(document).ready(function(){
	// retrieve the number of all data implemented
		$.ajax({
			url: "retrieve.php",
			data: {"mode": 0 },
			success: function(data){
				jdata = eval("("+data+")");		
				$("#numAll").text(jdata[0]["scoreNum"]); // all
				$("#numGenre").text(jdata[0]["genreNum"]); // genre
				$("#numComp").text(jdata[0]["composerNum"]); // composer
				$("#numCompYear").text(jdata[0]["composeYearNum"]); // compose year
				$("#numPubYear").text(jdata[0]["publishYearNum"]); // publishe year
				$("#numInst").text(jdata[0]["instrumentationNum"]); // instrumentation
			}
		});
	/*
	$('#keyword').click(function(){
		if($(this).val() == 'Type in keywords here'){
			$(this).css({'color': '#000000'}).val('');
		}
	});
	*/
	
	/* logo title */
	$('#logotitle').click(function(){
		location.href="index.php";
	});
	
	/* popup context menu handlers */
	
	$('#loginW').on('mouseenter', function(){
		$('#loginWindow').show();
		$('#loginWindowN').hide();
	});
	
	$('#loginW').on('mouseleave', function(){
		$('#loginWindow').mouseenter(function(){
			$('#loginWindow').show();
		});
		
		$('.top_nav').mouseleave(function(){
			$('#loginWindow').hide();
		});
	});
	
	$('#loginWN').on('mouseenter', function(){
		$('#loginWindowN').show();
		$('#loginWindow').hide();
	});
	
	$('#loginWN').on('mouseleave', function(){
		$('#loginWindowN').mouseenter(function(){
			$('#loginWindowN').show();
		});
		
		$('.top_nav').mouseleave(function(){
			$('#loginWindowN').hide();
		});
	});
	
	/* adjusts the location of the signup window based on the current size of the window */
	
	signUpWidth = $('#signUp').css('width').toString().substr(0,$('#signUp').css('width').toString().length-2);
	signUpHeight = $('#signUp').css('height').toString().substr(0,$('#signUp').css('height').toString().length-2);
	signUpWidth = parseInt(signUpWidth);
	signUpHeight = parseInt(signUpHeight);
	$('#signUp').css({'right': ((window.innerWidth - signUpWidth-50)/2)+'px', 'top': ((window.innerHeight - signUpHeight)/2)+'px'});
	
	/* added in browser.php */
	$('.main_right').css('width', (innerWidth - 417)+'px');
	$('.main_right .textInfo').css({'width': (innerWidth - 500)+'px'});

	
	/* sign in/up and help event handlers */
	
	$('#loginW').mouseup(function(){
		$('#Form_keepSigned').submit();
	});
	
	$('#Form_keepSignedN').submit(function(e){
		$(this).submit();
		e.preventDefault();
	});
	
	$('#normal_signup').click(function(){
		$('#signUp').fadeIn(300);
		$('#loginWindowN').hide();
	});
	
	$('#aboutPage').click(function(){
		location.href='about.php';
	});
	
	$('#userPanel').click(function(){
		location.href='mypage.php';
	});
	
	$('#logOut').click(function(){
		location.href='logout.php';
	});
	
	/* category links */
	$('#br_all').click(function(){
		location.href='browse.php';
	});
	
	
	$('.friend').click(function(){
		$('.friendProfileContainer').fadeIn(300);
	});
	
});
</script>
</body>
</html>