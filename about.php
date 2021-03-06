<?php
session_start();
require_once("cookie.php");
require_once("functions.php");

// gets the cookie for auto login feature and decrypt it for use
if(!isset($_SESSION['primaryId']) && isset($_COOKIE[$CFG->cookiename]) && isset($CFG->cookiepad) && $CFG->cookiepad !== false){
	$encCookie = $_COOKIE[$CFG->cookiename];
	$decCookie = extract_secure_cookie($encCookie);
	if($decCookie === false ) {
        die('Decryption failed:'.$encCookie);
        delete_secure_cookie();
    }
	
	$cookie_primaryId = $decCookie[0]+0;
	if($cookie_primaryId < 0){
		$cookie_primaryId = false;
		$decCookie = false;
		die("Auto login information is corrupt");
	}else{
		// check if the p_primary is the same as the one in the db
		require_once("connect.php");
		dbconnect();
		$isThisValidId = mysql_real_escape_string($cookie_primaryId);
		$query = "SELECT * FROM CT_User WHERE id = $isThisValidId;";
		$result = mysql_query($query, $connect);
		if(!$result){
			die("No match found. This user no longer exists.");
			delete_secure_cookie();
		}else{
			$data = mysql_fetch_array($result);
			if($decCookie[1] != md5($data['userPw'])){
			}else{
				$_SESSION['primaryId'] = $decCookie[0];
				$_SESSION['userIdentity'] = $decCookie[1]; // used for authorization
				$_SESSION['firstName'] = $data['firstName'];
				$_SESSION['lastName'] = $data['lastName'];
				$_SESSION['userEmail'] = $data['userEmail'];
				$_SESSION['level'] = $data['level'];
				$_SESSION['joinDate'] = $data['joinDate'];
				$_SESSION['id'] = $data['id'];
				dbclose();
			}
		}
	}
}

// finds out which login mode is used and send appropriate parameters to login.php
if(isset($_GET['loginType'])){
	$loginType = $_GET['loginType'];
	if($_GET['loginType'] == 1){
		if(isset($_GET['keepSignedIn'])){
			 $kSI = $_GET['keepSignedIn'];
		}else{
			$kSI = 0;
		}
	}
	echo("<script>location.href='login.php?loginType=".$loginType."&keepSignedIn=".$kSI."';</script>");
}
if(isset($_POST['loginType'])){
	$loginType = $_POST['loginType'];
	if($_POST['loginType'] == 2){
		if(isset($_POST['keepSignedInN'])){
			$kSI = $_POST['keepSignedInN'];
		}else{
			$kSI = 0;
		}
	}
	$_SESSION['inputEmail'] = $_POST['userEmail'];
	$_SESSION['inputPw'] = md5($_POST['userPw']);
	echo("<script>location.href='login.php?loginType=".$loginType."&keepSignedIn=".$kSI."';</script>");
}

?>

<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Common Time - Sharing the worlds' public domain music</title>
<script src="./assets/libraries/jquery-1.9.1.min.js"></script>
<script src="http://code.jquery.com/mobile/1.3.0/jquery.mobile-1.3.0.min.js"></script>
<script src="http://code.jquery.com/ui/1.10.2/jquery-ui.js"></script>
<script src="./ct.js"></script>
<link href='http://fonts.googleapis.com/css?family=Kotta+One|Raleway:400|Average' rel='stylesheet' type='text/css'/>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css" />
<link rel="stylesheet" href="./assets/style.css">
<link rel="stylesheet" href="http://code.jquery.com/mobile/1.3.0/jquery.mobile-1.3.0.min.css" />

</head>
<body>

<div class="page">
<div id="bg"></div>
	<div class="top_nav">
    	<div class="logo_top" onclick="location.href='index.php';">
        	<img src="assets/images/logo.png"/>
        </div>
    	<div class="topmenus">
        	<ul>
            	<li><span id="about">About</span></li>
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
						echo("<li id='logout'>Log out</li><li id='userPanel'>".$_SESSION['firstName']."</li>");
						echo("<li id='upload' onclick=location.href='upload.php'>Upload a score</li>");
					}
				?>
                
        	</ul>
        </div>
        <div id="notification_main"></div>
    </div>
   <div class="main_layout">
        <div class="about">
            <div class="aboutHeader">Musical Sheet Library</div>
            <div class="aboutPra"> 
                Digital is synonymous with immediate these days. People expect their gadgets to work instantly, the information they need to be available at their fingertips, and want access to their music and videos at all times. Stuck somewhere between digital music libraries and e-books, sheet music needs a bit of catching up to do in terms of availing itself in the digital world. With both the increasing digitization efforts from paper to electronic (mainly PDF) and the growing use of music composition and notation software, there is a niche to be served through a platform that allows people to reap the full benefit of digitized or born-digital sheet music. We are proposing a web-based platform fill that need.
                <br></br>
                The goal of the project is to develop a web-based platform that supports three main purposes: storage, access, and sharing of sheet music, and to do so in a way that it can be open to new types of usage and collaboration efforts. For instance, we will be exploring the IMSLP Petrucci Music Library both as a potential resource for metadata and copyright- free sheet music, and as the benchmark for sheet music deposit and sharing.
            </div>
            <div class="aboutHeader">Team Member</div>
            <div class="aboutPra">
                <div class="aboutImg">
                    Jamin Koo<br>
                    <img src="assets/images/jamin.jpg"/>
                    
                </div>  
                <div class="aboutImg">
                    Joosung Kim<br>
                    <img src="assets/images/joosung.jpg"/>
                    
                </div>
                <div class="aboutImg">
                    Jiyoung Kim<br>
                    <img src="assets/images/jiyoung.jpg"/>
                    
                </div>
                <div class="aboutImg">
                    Jaeho Jeong<br>
                    <img src="assets/images/jay.jpg"/>
                    
                </div>
        </div>     
    </div>
    <div class="footer">
    
    </div>
</div>

<?php
/* displayes errors */
if(isset($_SESSION['error'])){
	echo("<script>
	$('#notification_main').removeClass('success');
	$('#notification_main').html('".$_SESSION['error']."').show();
	setTimeout(function(){
		$('#notification_main').fadeOut(300);
	}, 2000);
	</script>");
	unset($_SESSION['error']);
}
if(isset($_SESSION['success'])){
	echo("<script>
	$('#notification_main').addClass('success');
	$('#notification_main').html('".$_SESSION['success']."').show();
	setTimeout(function(){
		$('#notification_main').fadeOut(300);
	}, 2000);
	</script>");

	unset($_SESSION['success']);
}
?>
<script>
// should be exported to a separate JS file
var signUpWidth;

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
});

$(document).ready(function(){
	var availableTags = [];
	//// TEMP ////
	$('#pop ul li:first').click(function(){
		location.href='browse.php';
	});
	$('#pop ul li:first').css('cursor', 'pointer');
	///
	
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
		
		$('.main_layout').mouseenter(function(){
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
		
		$('.main_layout').mouseenter(function(){
			$('#loginWindowN').hide();
		});
	});
	
	/* adjusts the location of the signup window based on the current size of the window */
	
	signUpWidth = $('#signUp').css('width').toString().substr(0,$('#signUp').css('width').toString().length-2);
	signUpHeight = $('#signUp').css('height').toString().substr(0,$('#signUp').css('height').toString().length-2);
	signUpWidth = parseInt(signUpWidth);
	signUpHeight = parseInt(signUpHeight);
	$('#signUp').css({'right': ((window.innerWidth - signUpWidth-50)/2)+'px', 'top': ((window.innerHeight - signUpHeight)/2)+'px'});
	
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
	
	$('#userPanel').click(function(){
		location.href='mypage.php';
	});
	
	$('#logout').click(function(){
		location.href='logout.php';
	});
	
	$('#about').click(function(){
		location.href='about.php';
	});
	
	$('.tags').click(function(){
		location.href=$(this).attr('data-link');
	});
	

	
});
</script>

</body>
</html>