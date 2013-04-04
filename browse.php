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
				die("Corrupt identity!");
				delete_secure_cookie();
			}else{
				$_SESSION['primaryId'] = $decCookie[0];
				$_SESSION['userIdentity'] = $decCookie[1]; // used for authorization
				$_SESSION['firstName'] = $data['firstName'];
				$_SESSION['lastName'] = $data['lastName'];
				$_SESSION['userEmail'] = $data['userEmail'];
				$_SESSION['level'] = $data['level'];
				dbclose();
			}
		}
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
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
<?php
// look for default parameters
if(isset($_POST['isSearch']) && $_POST['isSearch']==1){
	$docTitle = "Search results for "; // SEARCH TERM COMES HERE!
	
	// search and retrieve data with given parameters
	
}else{
	$docTitle = "Browse music scores";
	// retrieve all data by default
	echo('
		<script>
		$.ajax({
			url: "retrieve.php",
			data: {"mode": 0 },
			success: function(data){
				jdata = eval("("+data+")");
				
				$("#numAll").text(jdata[1]["scoreNum"]); // all
				$("#numGenre").text(jdata[1]["genreNum"]); // genre
				$("#numComp").text(jdata[1]["composerNum"]); // composer
				$("#numCompYear").text(jdata[1]["composeYearNum"]); // compose year
				$("#numPubYear").text(jdata[1]["publishYearNum"]); // publishe year
				$("#numInst").text(jdata[1]["instrumentationNum"]); // instrumentation
			}
		});
		</script>
	');
}
?>
<title><?=$docTitle;?></title>
</head>

<body>
<div class="page">
	<div class="top_nav">
    	<div class="topmenus">
        	<ul>
            	<li>About</li>
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
        	                <form id="Form_keepSigned" method="get" data-ajax="false">
                	        	<input type="checkbox" name="keepSignedIn" value=1 id="keepSignedIn" data-role="none">
                    	        <input type="hidden" name="loginType" value=1> <!-- value 1 for openID Login, value 2 for traditional Login -->
                        		<label for="keepSignedIn">Keep me signed in</label>
            	            </form>
 		                    </div>
                			</div>
							
							<li id="loginWN">Sign in</li>
			                <div id="loginWindowN">
		                	<div id="formComponentsN">
        	                <form id="Form_keepSignedN" method="post" data-ajax="false">
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
						echo("<li id='userPanel'>".$_SESSION['firstName']."</li>");
					}
				?>
                
        	</ul>
        </div>
        <div id="notification_main"></div>
    </div>
    <div class="main_layout">
	    <div class="main_left">
    	    <div class="catList">
        	    <span class="catListHeader">Browse scores by</span>
	           	<ul>
	               	<li>All (<span id="numAll"></span>)</li>
	                <li>Genre (<span id="numGenre"></span>)</li>
	                <li>Composer (<span id="numComp"></span>)</li>
	                <li>Compose year (<span id="numCompYear"></span>)</li>
	                <li>Publish year (<span id="numPubYear"></span>)</li>
                    <li>Instrumentation (<span id="numInst"></span>)</li>
	            </ul>
	        </div> 
	    </div>
        <div class="main_right">
        	<div class="srchbox">
	            <div id="category" data-role="fieldcontain">
            	<select data-inline="true" id="srchType"  class="cat">
                    <option value="all">All</option>
                	<option value="genre">Genre</option>
                    <option value="genre">Composer</option>
                    <option value="genre">Tag</option>
            	</select>
                </div>
                <div id="textinput" data-role="fieldcontain">
	                <input data-inline="true" type="text" id="keyword" name="keyword" value="Type in keywords here" class="keyword">
    	            <a data-role="button" onclick="alert('submit function')" id="submit">Search</a>
                </div>
            </div>
            <div class="itemList">
            items appear here
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
var availableTags, signUpWidth;

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
	$('.srchbox #textinput').css({'width': (innerWidth - 500)+'px'});
	$('.srchbox #textinput .ui-input-text').css({'width': (innerWidth - 625)+'px'});
});

$(document).ready(function(){
	$('#keyword').click(function(){
		if($(this).val() == 'Type in keywords here'){
			$(this).css({'color': '#000000'}).val('');
		}
	});
	
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
	$('.main_right').css('width', (innerWidth - 317)+'px');
	$('.srchbox #textinput').css({'width': (innerWidth - 500)+'px'});
	$('.srchbox #textinput .ui-input-text').css({'width': (innerWidth - 625)+'px'});

	
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
		location.href='logout.php';
	});

	// on keydown on the keyword area, initiate the tag retrieval process, and once the process is done, launch the autocomplete process
	
	// test
	$('#keyword').autocomplete({
		source: availableTags
	});
	
});
</script>
</body>
</html>