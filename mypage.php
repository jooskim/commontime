<?php
session_start();
require_once("cookie.php");
require_once("functions.php");

if(!isset($_SESSION['primaryId'])){
	echo("<script>alert('You have to log in to see the mypage page'); location.href='index.php';</script>");
}

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
            <div class="myPage">
                <div id="profileImg"></div>
                <div class="myPageTitle">
                  <?php  echo($_SESSION['firstName'] ) ; ?> 
                   <?php  echo($_SESSION['lastName']); ?>
                </div>
                <div class="myScore">
                    My Uploads <br>
                    <img src="assets/images/upload.png"/>
                    <div class="number">
                    <?php
                        dbconnect();
                        $myUpload_sql = mysql_query("SELECT COUNT(*) FROM CT_Score WHERE uploadedBy = ".mysql_real_escape_string($_SESSION['primaryId']).";");
                        /* $userID = $_SESSION['id'] = $data['id'];
                        $myUpload_sql = mysql_query("SELECT COUNT(*) FROM CT_Score WHERE id ='$userID';"); */
                        $myUpload = mysql_fetch_array($myUpload_sql);
                        echo(htmlentities($myUpload[0]));
	               	?>
                    </div>
                </div>
                <div class="myScore">
                    My Lists<br>
                    <img src="assets/images/clip.png"/>
                    <div class="number">
                    <?php
                        dbconnect();
						$mylistId = getSpecific("CT_Mylist", "id", "creator = ".$_SESSION['primaryId']);
                        $myUpload_sql = mysql_query("SELECT COUNT(*) FROM CT_MylistEntity WHERE refScrapbook = ".mysql_real_escape_string($mylistId).";");
                        /* $userID = $_SESSION['id'] = $data['id'];
                        $myUpload_sql = mysql_query("SELECT COUNT(*) FROM CT_Score WHERE id ='$userID';"); */
                        $myUpload = mysql_fetch_array($myUpload_sql);
                        echo(htmlentities($myUpload[0]));
	               	?>
                    </div>
                </div>
                <div class="myScore">
                    My Friends<br>
                    <img src="assets/images/group.png"/>
                    <div class="number">0
                    </div>
                </div>
            </div>
            <div class="myUpload">
                <span class="myUploadHeader">My Uploads<span class="more"> >> See more</span></span>
                
                <ul><?php
                        dbconnect();
                        $myId = $_SESSION['primaryId'];
                       /* $myUpload_result = mysql_query("SELECT title FROM CT_Score LIMIT 3 ORDER BY timestamp DESC;");
                        $userID = $_SESSION['id'] = $data['id'];
                        $myUpload_result = mysql_query("SELECT title FROM CT_Score WHERE id ='$userID' LIMIT 3 ;");*/
                        $myUpload_result = mysql_query("SELECT title FROM CT_Score WHERE uploadedBy = ".mysql_real_escape_string($myId)." LIMIT 3;");
                        $myUpload_time = mysql_query("SELECT timestamp FROM CT_Score WHERE uploadedBy = ".mysql_real_escape_string($myId)." LIMIT 3;");
                        $count = 0;
						if(mysql_num_rows($myUpload_result) == 0){
							echo("<li>No uploads</li>");
						}else
						{
                        	while ($myUpload = @mysql_fetch_row($myUpload_result)){
                            	$myUpload_t = @mysql_fetch_row($myUpload_time);
                           	  echo('<li>');
                          	  echo(htmlentities($myUpload[0]));
                              echo(' (');
                              echo(htmlentities($myUpload_t[0]));
                              echo(')</li>');
                        	}
						}
	               	?>
	            </ul>
            </div>
            <div class="myUpload">
                <span class="myUploadHeader">My Lists<span class="more"> >> See more</span></span>
                <ul>
	               	<?php
                        dbconnect();
                        $myLQuery = "SELECT * FROM CT_MylistEntity WHERE refScrapbook = ".$mylistId." LIMIT 3;";
						$myLResult = mysql_query($myLQuery, $connect);
						if(mysql_num_rows($myLResult) == 0){
							echo("<li>No item in mylist</li>");
						}else{
							while($dataMyL = @mysql_fetch_array($myLResult)){
								echo('<li>'.htmlentities(getSpecific("CT_Score","title", "id = ".$dataMyL['refScore'])).' <span class="feeds">(added at '.date("Y-m-d H:i:s",$dataMyL['timestamp']).')</span></li>');
							}
						}
						/*
						$myList_result = mysql_query("SELECT title FROM CT_Mylist WHERE creator = ".mysql_real_escape_string($myId)." LIMIT 3;");
                        $myList_time = mysql_query("SELECT timestamp FROM CT_Mylist WHERE creator = ".mysql_real_escape_string($myId)." LIMIT 3;");
                        
						if(mysql_num_rows($myList_result) == 0){
							echo("<li>No item in mylist</li>");
						}else
						{
							while( $myList = @mysql_fetch_row($myList_result)){
                            	$myList_t = @mysql_fetch_row($myList_time);
                            	echo('<li>');
                            	echo(htmlentities($myList[0]));
                             	echo(' (');
                            	echo(htmlentities($myList_t[0]));
                             	echo(')</li>');
                        	}
						}*/
	               	?>
	            </ul>
            </div>
            <div class="myUpload">
                <span class="myUploadHeader">User Setting</span>
                <ul>
	               	<li>Name :
	               	<?php  echo($_SESSION['firstName'] ) ; ?> 
                    <?php  echo($_SESSION['lastName']); ?>
	               	<span class="feeds">(since <?php  echo($_SESSION['joinDate']); ?>
	               	)</span> <span class="edit">Edit</span></li>
	               	<li>Email : 
	               	<?php  echo($_SESSION['userEmail'] ) ; ?> <span class="edit">Edit</span>
	               	</li>
	               	<li>Password : ******</span><span class="edit">Edit</span> </li>
	            </ul>
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
	
	$('#aboutPage').click(function(){
		location.href='about.php';
	});
	
	$('#logOut').click(function(){
		location.href='logout.php';
	});
	
	$('#about').click(function(){
		location.href='about.php';
	});
	
	$('.tags').click(function(){
		location.href=$(this).attr('data-link');
	});
	
	
	$('#profileImg').css('background', 'url("<?php
	dbconnect();
	$queryAva = "SELECT avatarPic FROM CT_User WHERE id = ".$_SESSION['primaryId'].";";
	$resultAva = mysql_query($queryAva,$connect);
	$dataAva = mysql_fetch_array($resultAva);
	if(is_file($dataAva[0])){
		echo $dataAva[0];
	}else{
		echo "assets/images/profile.jpg";
	}
	
	?>
	") no-repeat');
	
	$('.myScore .number:eq(2)').text('<?php echo($totalNum); ?>');

	
});
</script>

</body>
</html>