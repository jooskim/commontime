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
if(isset($_GET['keyword'])){
	$srchType = $_GET['srchType'];
	$keyword = $_GET['keyword'];
	if($_GET['keyword'] != ''){
		$docTitle = "Search results for ".htmlentities($keyword);
	}else{
		$docTitle = "Browse music scores";
	}
	
	
	// search and retrieve data with given parameters
	dbconnect();
	if($srchType != 'tag'){
		if($srchType == 'genre'){
			$tempQuery = "SELECT id FROM CT_Genre WHERE genre LIKE '%".mysql_real_escape_string($keyword)."%';";
			$tempResult = mysql_query($tempQuery, $connect);
			$fetchQuery = "SELECT * FROM CT_Score WHERE ";
		
			while($tempData = mysql_fetch_array($tempResult)){
				$fetchQuery .= "genre = ".$tempData['id']." OR ";
			}
			if(mysql_num_rows($tempResult) == 0){
				$fetchQuery = "SELECT id FROM CT_Score WHERE 0;";
			}else{
				$fetchQuery = substr($fetchQuery, 0, strlen($fetchQuery)-3);
				$fetchQuery .= ";";
			}
		}else{
			$fetchQuery = "SELECT * FROM CT_Score WHERE ".mysql_real_escape_string($srchType)." LIKE '%".mysql_real_escape_string($keyword)."%' LIMIT 0, 10;";
		}
	}else if($srchType == 'tag'){
		$tempQuery = "SELECT DISTINCT refScore FROM CT_ScoreTag WHERE tag LIKE '%".mysql_real_escape_string($keyword)."%';";
		$tempResult = mysql_query($tempQuery, $connect);
		$fetchQuery = "SELECT * FROM CT_Score WHERE ";
		
		while($tempData = mysql_fetch_array($tempResult)){
			$fetchQuery .= "id = ".$tempData['refScore']." OR ";
		}
		if(mysql_num_rows($tempResult) == 0){
			$fetchQuery = "SELECT id FROM CT_Score WHERE 0;";
		}else{
			$fetchQuery = substr($fetchQuery, 0, strlen($fetchQuery)-3);
			$fetchQuery .= ";";
		}
	}
	$fetchResult = mysql_query($fetchQuery, $connect);
	
}else{
	$docTitle = "Browse music scores";
	
	// gets the 10 items and display on the display
	dbconnect();
	$fetchQuery = "SELECT * FROM CT_Score LIMIT 0, 10;";
	$fetchResult = mysql_query($fetchQuery, $connect);
	
}
?>
<title><?=$docTitle;?></title>
</head>

<body>
<div class="page">
	<div class="top_nav">
    	<div class="logo_top" onclick="location.href='index.php';">
        	Common Time
        </div>
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
	               	<li id="br_all">All (<span id="numAll"></span>)</li>
	                <li id="br_genre" onclick="$('.subGenre').toggle();">Genre (<span id="numGenre"></span>)</li>
                    <?php
					dbconnect();
					$query_Cat = "SELECT * FROM CT_Genre;";
					$result_Cat = mysql_query($query_Cat, $connect);
					while($data = mysql_fetch_array($result_Cat)){
						$query_scoresOfSubcat = "SELECT COUNT(*) FROM CT_Score WHERE genre = '".$data["id"]."';";
						$result_scoresOfSubcat = mysql_query($query_scoresOfSubcat, $connect);
						$numOfScoresOfSubcat = mysql_fetch_array($result_scoresOfSubcat);
						echo('<li class="subGenre" onclick=location.href="browse.php?srchType=genre&keyword='.$data["genre"].'">'.htmlentities($data["genre"]).' ('.$numOfScoresOfSubcat[0].')</li>');
					}
					dbclose();
					?>
	                <li id="br_composer" onclick="$('.subComposer').toggle();">Composer (<span id="numComp"></span>)</li>
                    <?php
					dbconnect();
					$query_Cat = "SELECT composer FROM CT_Score WHERE composer IS NOT NULL;";
					$result_Cat = mysql_query($query_Cat, $connect);
					while($data = mysql_fetch_array($result_Cat)){
						$query_scoresOfSubcat = "SELECT COUNT(*) FROM CT_Score WHERE composer = '".$data["composer"]."';";
						$result_scoresOfSubcat = mysql_query($query_scoresOfSubcat, $connect);
						$numOfScoresOfSubcat = mysql_fetch_array($result_scoresOfSubcat);
						echo('<li class="subComposer" onclick=location.href="browse.php?srchType=composer&keyword='.$data["composer"].'">'.htmlentities($data["composer"]).' ('.$numOfScoresOfSubcat[0].')</li>');
					}
					dbclose();
					?>
	                <li id="br_composeyear" onclick="$('.subComposeYear').toggle();">Compose year (<span id="numCompYear"></span>)</li>
                    <?php
					dbconnect();
					$query_Cat = "SELECT composeYear FROM CT_Score WHERE composeYear IS NOT NULL;";
					$result_Cat = mysql_query($query_Cat, $connect);
					while($data = mysql_fetch_array($result_Cat)){
						$query_scoresOfSubcat = "SELECT COUNT(*) FROM CT_Score WHERE composeYear = '".$data["composeYear"]."';";
						$result_scoresOfSubcat = mysql_query($query_scoresOfSubcat, $connect);
						$numOfScoresOfSubcat = mysql_fetch_array($result_scoresOfSubcat);
						echo('<li class="subComposeYear">'.htmlentities($data["composeYear"]).' ('.$numOfScoresOfSubcat[0].')</li>');
					}
					dbclose();
					?>
	                <li id="br_publishyear" onclick="$('.subPublishYear').toggle();">Publish year (<span id="numPubYear"></span>)</li>
                    <?php
					dbconnect();
					$query_Cat = "SELECT publishYear FROM CT_Score WHERE publishYear IS NOT NULL;";
					$result_Cat = mysql_query($query_Cat, $connect);
					while($data = mysql_fetch_array($result_Cat)){
						$query_scoresOfSubcat = "SELECT COUNT(*) FROM CT_Score WHERE publishYear = '".$data["publishYear"]."';";
						$result_scoresOfSubcat = mysql_query($query_scoresOfSubcat, $connect);
						$numOfScoresOfSubcat = mysql_fetch_array($result_scoresOfSubcat);
						echo('<li class="subPublishYear">'.htmlentities($data["publishYear"]).' ('.$numOfScoresOfSubcat[0].')</li>');
					}
					dbclose();
					?>
                    <li id="br_instrumentation" onclick="$('.subInstrumentation').toggle();">Instrumentation (<span id="numInst"></span>)</li>
                    <?php
					dbconnect();
					$query_Cat = "SELECT * FROM CT_Instrumentation;";
					$result_Cat = mysql_query($query_Cat, $connect);
					while($data = mysql_fetch_array($result_Cat)){
						$query_scoresOfSubcat = "SELECT COUNT(*) FROM CT_Score WHERE instrumentation = '".$data["instrumentation"]."';";
						$result_scoresOfSubcat = mysql_query($query_scoresOfSubcat, $connect);
						$numOfScoresOfSubcat = mysql_fetch_array($result_scoresOfSubcat);
						echo('<li class="subInstrumentation">'.htmlentities($data["instrumentation"]).' ('.$numOfScoresOfSubcat[0].')</li>');
					}
					dbclose();
					?>
	            </ul>
	        </div> 
	    </div>
        <div class="main_right">
        	<div class="srchbox">
	            <div id="category" data-role="fieldcontain">
                <select data-inline="true" id="srchType"  class="cat">
                    <option value="title">Title</option>
                	<option value="genre">Genre</option>
                    <option value="composer">Composer</option>
                    <option value="tag">Tag</option>
            	</select>
                </div>
                <div id="textinput" data-role="fieldcontain">
	                <input data-inline="true" type="text" id="keyword" name="keyword" value="Type in keywords here" class="keyword">
    	            <a data-role="button" id="submit">Search</a>
                </div>
            </div>
            <div class="itemList">
            	<?php
				while($data = mysql_fetch_array($fetchResult)){
					$numOfResults = mysql_num_rows($fetchResult);
					
					// only for genre data
					dbconnect();
					$genre_Query = "SELECT genre FROM CT_Genre WHERE id = ".$data['genre'];
					$genre_Result = mysql_query($genre_Query, $connect);
					$genreData = mysql_fetch_array($genre_Result);
					$data['genre'] = $genreData[0];
					dbclose();
					
					if($numOfResults > 0){
						echo('
							<div class="scoreEntity">
                				<img src="assets/images/defaultalbumart.jpg" class="albumArt" />
		                   	<div class="textInfo">
		                   	 	<span class="title">'.htmlentities($data["title"]).'</span><br />
		                        <span class="key">Composer</span><span class="value">'.htmlentities($data["composer"]).'</span><br /> <!-- composer -->
		                        <span class="key">Genre</span><span class="value">'.htmlentities($data["genre"]).'</span><br /> <!-- genre -->
		                        <span class="key">Compose year</span><span class="value">'.htmlentities($data["composeYear"]).'</span><br /> <!-- compose year -->
		                        <span class="key">Publish year</span><span class="value">'.htmlentities($data["publishYear"]).'</span><br /> <!-- publish year -->
		                        <span class="key">Instrumentation</span><span class="value">'.htmlentities($data["instrumentation"]).'</span><br /> <!-- instrumentation -->
		                        <span class="key">Opus number</span><span class="value">'.htmlentities($data["opusNum"]).'</span><br /> <!-- opusnum -->
		                        <span class="key">Uploaded by</span><span class="value">'.htmlentities($data["uploadedBy"]).'</span><br /> <!-- uploaded by -->
		                    </div>
		                </div>
						');
					}else {
					}
				}
				?>
	            
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

if(isset($_GET['keyword'])){
	echo("
		<script>
		$('#keyword').css('color', '#000000');
		$('#srchType').val('".$_GET['srchType']."');
		$('#keyword').val('".$_GET['keyword']."');</script>
	");
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
	
	$('#userPanel').click(function(){
		location.href='logout.php';
	});
	
	$('#submit').click(function(){
		var srchType = $('#srchType').val();
		var keyword = $('#keyword').val();
		location.href="browse.php?srchType="+srchType+"&keyword="+keyword;
	});
	
	$('#keyword').keyup(function(){
		$.ajax({
			async: false,
			url: 'retrieve.php',
			data: {'mode': 1, 'srchType': $('#srchType').val(), 'keyword': $('#keyword').val()},
			success: function(data){
				availableTags = [];
				var jdata = eval("("+data+")");
				for(var i in jdata){
					availableTags.push(jdata[i][0]);
				}
			}
		});
	});
	
	/* category links */
	$('#br_all').click(function(){
		location.href='browse.php';
	});
	
$(document).ajaxSuccess(function(){
	$('#keyword').autocomplete({
		source: availableTags
	});
});
	
});
</script>
</body>
</html>