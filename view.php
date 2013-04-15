<?php
session_start();
require_once("cookie.php");
require_once("functions.php");
if(isset($_GET['id'])){
	$scoreNum = $_GET['id'];
}else{
	$scoreNum = -1;
}

dbconnect();
$query = "SELECT * FROM CT_Score WHERE id = ".mysql_real_escape_string($scoreNum).";";
$result = mysql_query($query, $connect);
$numOfResult = mysql_num_rows($result);

if(!isset($_GET['id']) || $numOfResult == 0){
	$_SESSION['error'] = "The score you tried to access does not exist!";
	header("Location: browse.php");
}else{
	// get data from the database
	$fetchResult = mysql_query($query, $connect);
}
dbclose();
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
						$query_scoresOfSubcat = "SELECT COUNT(*) FROM CT_Genre WHERE genre = '".$data["genre"]."';";
						$result_scoresOfSubcat = mysql_query($query_scoresOfSubcat, $connect);
						$numOfScoresOfSubcat = mysql_fetch_array($result_scoresOfSubcat);
						echo('<li class="subGenre" data-link="browse.php?srchType=genre&keyword='.$data["genre"].'">'.htmlentities($data["genre"]).' ('.$numOfScoresOfSubcat[0].')</li>');
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
						echo('<li class="subComposer" data-link="browse.php?srchType=composer&keyword='.$data["composer"].'">'.htmlentities($data["composer"]).' ('.$numOfScoresOfSubcat[0].')</li>');
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
						echo('<li class="subComposeYear" data-link="browse.php?srchType=composeYear&keyword='.$data["composeYear"].'">'.htmlentities($data["composeYear"]).' ('.$numOfScoresOfSubcat[0].')</li>');
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
						echo('<li class="subPublishYear" data-link="browse.php?srchType=publishYear&keyword='.$data["publishYear"].'">'.htmlentities($data["publishYear"]).' ('.$numOfScoresOfSubcat[0].')</li>');
					}
					dbclose();
					?>
                    <li id="br_instrumentation" onclick="$('.subInstrumentation').toggle();">Instrumentation (<span id="numInst"></span>)</li>
                    <?php
					dbconnect();
					$query_Cat = "SELECT DISTINCT instrumentation FROM CT_Instrumentation;";
					$result_Cat = mysql_query($query_Cat, $connect);
					while($data = mysql_fetch_array($result_Cat)){
						$query_scoresOfSubcat = "SELECT COUNT(*) FROM CT_Instrumentation WHERE instrumentation = '".$data["instrumentation"]."';";
						$result_scoresOfSubcat = mysql_query($query_scoresOfSubcat, $connect);
						$numOfScoresOfSubcat = mysql_fetch_array($result_scoresOfSubcat);
						echo('<li class="subInstrumentation" data-link="browse.php?srchType=instrumentation&keyword='.$data["instrumentation"].'">'.htmlentities($data["instrumentation"]).' ('.$numOfScoresOfSubcat[0].')</li>');
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
                    <option value="composeYear">Compose year</option>
                    <option value="publishYear">Publish year</option>
                    <option value="instrumentation">Instrumentation</option>
                    <option value="tag">Tag</option>
            	</select>
                </div>
                <div id="textinput" data-role="fieldcontain">
	                <input data-inline="true" type="text" id="keyword" name="keyword" placeholder="Type in keywords here" class="keyword">
    	            <a data-role="button" id="submit">Search</a>
                </div>
            </div>
            <div class="itemList">
            	<?php
				$data = mysql_fetch_array($fetchResult);
				
				// uploaded by
				dbconnect();
				$up_Query = "SELECT firstName FROM CT_User WHERE id = ".$data['uploadedBy'].";";
				$up_Result = mysql_query($up_Query, $connect);
					
				// only for genre data
				$genre_Query = getSQLFromID("genre", $data['id']);
				$genre_Result = mysql_query($genre_Query, $connect);
				
				// only for style data
				$style_Query = getSQLFromID("style", $data['style']);
				$style_Result = mysql_query($style_Query, $connect);
				
				// only for instrumentation data
				$instr_Query = getSQLFromID("instrumentation", $data['id']);
				$instr_Result = mysql_query($instr_Query, $connect);
				
				// only for tag data
				$tag_Query = getSQLFromID("tag", $data['id']);
				$tag_Result = mysql_query($tag_Query, $connect);
				
				// only for comments data
				$comments_Query = getSQLFromID("comment", $data['id']);
				$comments_Result = mysql_query($comments_Query, $connect);
				
				// only for flagHistory data
				$flag_Query = getSQLFromID("flagHistory", $data['id']);
				$flag_Result = mysql_query($flag_Query, $connect);
				dbclose();
				
				echo('
					<div class="detailTitle">'.htmlentities($data["title"]).'<div class="actions">
							<span id="backToList" ><img src="assets/images/back.png"><br>Back to list</span><span id="addToList" ><img src="assets/images/add.png"><br>Add to mylist</span><span id="download"><img src="assets/images/download.png"><br>Download this score</span><span id="like"><img src="assets/images/like.png"><br>Like this score</span><span id="flag"><img src="assets/images/flag.png"><br>Report this score</span>
						
						</div><div class="flagItContainer">
							<div class="flagItHeader">Report this score</div>
							<div class="flagIt"><span class="warning">You are about to report the following score.<br></span><span style="color: #455F7B; font-weight: bold; font-size: 16px; margin-top: 7px; display: inline-block; margin-bottom: 7px;">'.htmlentities($data["title"]).'</span><span class="warning"><br>Please make sure that you have proper sources as you describe the reason(s) for this claim in the text field below. This report will be reviewed by the operators as soon as it is submitted. Also, by reporting this score as problematic, you agree that other users can participate in the rebuttal process to prevent, if any, misjudgements.</span><hr style="width: 99%;" color="#BBBBBB" size="1" no-shade></hr><form id="flagForm" data-ajax="false"><textarea data-role="none" rows=9 cols=77 id="flagDesc" name="flagDesc"></textarea><br>'); if(isset($_SESSION['primaryId'])){ echo('<input type="hidden" id="flagTo" name="flagTo" value='.htmlentities($data['id']).'><input type="hidden" id="flagBy" name="flagBy" value='.$_SESSION['primaryId'].'>');} echo('<input type="submit" value="Report" id="submitFlag"><input type="button" value="Cancel" id="cancelButton" onclick=$(".flagItContainer").fadeOut(300)></form></div>
						</div></div>
					
					<div class="scoreDetail">
						<span class="subTitle">General Information</span><br><div id="scoreimg"></div>
						<div class="txt">
							<span class="key">Composer</span><span class="value">'.htmlentities($data["composer"]).'</span><br>
							<span class="key">Compose Year</span><span class="value">'.htmlentities($data["composeYear"]).'</span><br>
							<span class="key">Genre</span><span class="value">');
							while($dataGenre = @mysql_fetch_array($genre_Result)){
								echo("<span class='genres' data-link='browse.php?srchType=genre&keyword=".htmlentities($dataGenre["genre"])."'>".htmlentities($dataGenre["genre"])." </span>");
							}
							echo('</span><br>
							<span class="key">Opus Number</span><span class="value">'.htmlentities($data["opusNum"]).'</span><br>
							<span class="key">Key</span><span class="value">'.htmlentities($data["key"]).'</span><br>
							<span class="key">Language</span><span class="value">'.htmlentities($data["language"]).'</span><br>
							<span class="key">Piece Style</span><span class="value">');
							while($dataStyle = @mysql_fetch_array($style_Result)){
								echo(htmlentities($dataStyle[0].", "));
							}
							echo('</span><br>
							<span class="key">Instrumentation</span><span class="value">');
							while($dataInstr = @mysql_fetch_array($instr_Result)){
								echo("<span class='genres' data-link='browse.php?srchType=instrumentation&keyword=".htmlentities($dataInstr["instrumentation"])."'>".htmlentities($dataInstr["instrumentation"])." </span>");
							}
							echo('</span><br>
						</div>
		            </div>
					<div class="scoreDetail">
						<span class="subTitle">Music Score Information</span><br>
						<div class="txt">
							<span class="key">Publish Year</span><span class="value">'.htmlentities($data["publishYear"]).'</span><br>
							<span class="key">Uploaded by</span><span class="value">');
							$dataUp = mysql_fetch_array($up_Result);
							echo(htmlentities($dataUp[0]).'</span><br>
							<span class="key">Description</span><span class="value">'.htmlentities($data["description"]).'</span><br>
						</div>
					</div>
					<div class="scoreDetail">
						<span class="subTitle">Tags</span><br>
						<div class="txt">');
							while($dataTag = @mysql_fetch_array($tag_Result)){
								echo("<span class='tags' data-link='browse.php?srchType=tag&keyword=".htmlentities($dataTag[0])."'>".htmlentities($dataTag[0])." </span>");
							}
							echo('
						</div>
					</div>
					<div class="scoreDetail">
						<span class="subTitle">Comments</span><br>
						<div class="txt">
						<form id="msgForm" data-ajax="false">'); if(isset($_SESSION['firstName'])){ echo('<input type="text" data-inline="true" name="msgContent" id="msgContent"><input type="hidden" value='.$_SESSION['primaryId'].' id="msgBy" name="msgBy">');} echo('<input type="hidden" value='.$data["id"].' id="msgTo" name="msgTo"></form><div class="commentArea">');
							while($dataComments = @mysql_fetch_array($comments_Result)){
								$tempData = idToValue("firstName", "CT_User", $dataComments['commentBy']);

								echo("<div class='msgItem' id=".htmlentities($dataComments['id'])."><span class='name'>".htmlentities($tempData[0])."</span><span class='time'>(".date("M d, Y H:i:s",htmlentities($dataComments['timestamp'])).")</span><span class='msg'>".htmlentities($dataComments['comment'])."</span></div>");
							}
							echo('
						</div></div>
					</div>
					<div class="scoreDetail">
						<span class="subTitle">Flag History</span><br>
						<div class="txt">
							<div class="commentArea">');
							if(mysql_num_rows($flag_Result) == 0){
								echo("<span style='color: #aeaeae;' class='msg'>No flag history</span>");
							}
							
							while($dataFlag = @mysql_fetch_array($flag_Result)){
								$tempData = idToValue("firstName", "CT_User", $dataFlag['flagBy']);
								echo("<div class='msgItem' id=".htmlentities($dataFlag['id'])."><span class='name'>".htmlentities($tempData[0])."</span><span class='time'>(".date("M d, Y H:i:s",htmlentities($dataFlag['timestamp'])).")</span><span class='msg'>".htmlentities($dataFlag['description'])."</span>"); if($dataFlag['isResolved'] == 0){ echo('<span class="revoke">resolve</span>');}else{ echo('<span class="time">resolved</span>');} echo("</div>");
							}
							echo('
							</div>
						</div>
					</div>
					');
				
					// if isFlagged = 1, make the download button unable to click
					if($data['isFlagged'] == 1){
						echo("<script>
								$('#download').html('<img src=assets/images/download.png><br>Reported');
								$('#download').addClass('inDispute');
								$('.actions span[id=flag]').remove();
							  </script>");
					}
					
					$likeList = explode(",", $data["likeList"]);
					if(isset($_SESSION['userEmail']) && in_array($_SESSION['userEmail'], $likeList)){
						echo("<script>
								$('#like').html('<img src=assets/images/like2.png><br>Liked this score');
								$('#like').addClass('liked');
							  </script>");
					}

					echo("
						<script>document.title='".htmlentities($data['title'])."';</script>
						");
				?>
	            
            </div>
        </div>    </div>
    <div class="footer">
    
    </div>
</div>
<script>
// should be exported to a separate JS file
var availableTags, signUpWidth;
var comments = [];

$('#msgForm').submit(function(e){
	$.ajax({
		url: 'retrieve.php',
		data: {'mode': 2, 'msgTo': $('#msgForm #msgTo').val(), 'msgBy': $('#msgForm #msgBy').val(), 'msgContent': $('#msgForm #msgContent').val()},
		success: function(data){
			location.href='view.php?id='+$('#msgForm #msgTo').val();
		}
	});
	e.preventDefault();

});

$('#flagForm').submit(function(e){
	$.ajax({
		url: 'retrieve.php',
		data: {'mode': 3, 'flagBy': $('#flagForm #flagBy').val(), 'flagTo': $('#flagForm #flagTo').val(), 'flagDesc': $('#flagForm #flagDesc').val()},
		success: function(data){
			location.href='view.php?id='+$('#flagForm #flagTo').val();
		}
	});
	e.preventDefault();
});
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
	$('.srchbox #textinput').css({'width': (innerWidth - 530)+'px'});
	$('.srchbox #textinput .ui-input-text').css({'width': (innerWidth - 665)+'px'});
	$('.main_right .textInfo').css({'width': (innerWidth - 500)+'px'});
	$('.flagItContainer').css({'left': (innerWidth - 1000)/2+'px'});
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
	$('.main_right').css('width', (innerWidth - 317)+'px');
	$('.srchbox #textinput').css({'width': (innerWidth - 530)+'px'});
	$('.srchbox #textinput .ui-input-text').css({'width': (innerWidth - 665)+'px'});
	$('.main_right .textInfo').css({'width': (innerWidth - 500)+'px'});
	$('.flagItContainer').css({'left': (innerWidth - 1000)/2+'px'});

	
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
	
	// back button
	$('#backToList').click(function(){
		history.back(1);
	});

	// like button
	$('#like').click(function(){
		var isLoggedIn = <?php if(isset($_SESSION['primaryId'])){ echo 1; }else { echo 0; } ?>;
		if(isLoggedIn == 1){
			$.ajax({
				url: 'retrieve.php',
				data: {'mode': 4, 'likeTo': $('#flagForm #flagTo').val()},
				success: function(data){
					location.href='view.php?id='+$('#flagForm #flagTo').val();
				}
			});
		}else{
			alert('You have to log in to like a score!');
		}
	});

	
	// resolve part
	$('#flag').click(function(){
		var isLoggedIn = <?php if(isset($_SESSION['primaryId'])){ echo 1; }else { echo 0; } ?>;
		if(isLoggedIn == 1){
			$('.flagItContainer').fadeIn(300);
		}else{
			alert('You have to log in to report a score!');
		}
	});
	
	$('.revoke').click(function(){
		$('.flagItContainer .flagItHeader').text('Resolve the reported status');
		$('.flagItContainer .flagIt').css('height', '230px');
		$('.flagItContainer .flagIt').html('<span class="warning">You can rebut the current hold status by sending us the reason(s) this score is not problematic. Once submitted, this resolve request will be reviewed by the operators. </span><hr style="width: 99%;" color="#BBBBBB" size="1" no-shade></hr><textarea data-role="none" rows=9 cols=77 id="flagDesc" name="flagDesc"></textarea><br><input type="button" value="Report" onclick=alert("REQUEST_SENT");location.href="view.php?id=<?php echo(htmlentities($data['id'])); ?>"><input type="button" value="Cancel" id="cancelButton" onclick=$(".flagItContainer").fadeOut(300)>');
		$('.flagItContainer').fadeIn(300);
	});
	
	$('.tags').click(function(){
		location.href=$(this).attr('data-link');
	});
	
	$('.genres').click(function(){
		location.href=$(this).attr('data-link');
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
	
	$('.subGenre').click(function(){
		location.href=$(this).attr('data-link');
	});
	
	$('.subComposer').click(function(){
		location.href=$(this).attr('data-link');
	});
	
	$('.subComposeYear').click(function(){
		location.href=$(this).attr('data-link');
	});
	
	$('.subPublishYear').click(function(){
		location.href=$(this).attr('data-link');
	});
	
	$('.subInstrumentation').click(function(){
		location.href=$(this).attr('data-link');
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