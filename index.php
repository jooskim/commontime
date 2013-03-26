<?php
session_start();
?>

<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Common Time - Sharing the worlds' public domain music</title>
<script src="./assets/libraries/jquery-1.9.1.min.js"></script>
<script src="http://code.jquery.com/mobile/1.3.0/jquery.mobile-1.3.0.min.js"></script>
<script src="http://code.jquery.com/ui/1.10.2/jquery-ui.js"></script>
<link href='http://fonts.googleapis.com/css?family=Kotta+One|Raleway:400|Average' rel='stylesheet' type='text/css'/>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css" />
<link rel="stylesheet" href="./assets/style.css">
<link rel="stylesheet" href="http://code.jquery.com/mobile/1.3.0/jquery.mobile-1.3.0.min.css" />

</head>
<body>

<div class="page">
	<div class="top_nav">
    	<div class="topmenus">
        	<ul>
            	<li>Login</li>
            	<li>About</li>
        	</ul>
        </div>
    </div>
    <div class="main_layout">
    	<div class="main_center">
    		<span id="logotitle">
        	Common Time</span><br><span id="logoheader">Sharing the world's public domain music<br><br></span>
            
            <form id="searchForm" method="get" data-ajax="false" >
            	<div class="t1">
            	<select id="srchType" data-inline="true" data-corners="false">
                    <option value="all">All</option>
                	<option value="genre">Genre</option>
                    <option value="genre">Composer</option>
                    <option value="genre">Tag</option>
            	</select>
                </div>
                <div class="t2">
                	<input data-inline="true" type="text" id="keyword" name="keyword" value="Type in keywords here">
                </div>
                <div class="t3">
                    <a data-role="button" onclick="alert('submit function')" id="submit">Search</a>
                </div>
            </form>
            <br>
            <div class="mostList">
            	<span class="mostListHeader">Most Popular</span>
                <ul>
                	<li>t1</li>
                    <li>t2</li>
                    <li>t3</li>
                    <li>t4</li>
                    <li>t5</li>
                </ul>
            </div>
            <div class="mostList">
            	<span class="mostListHeader">Most Recent</span>
            	<ul>
                	<li>t1</li>
                    <li>t2</li>
                    <li>t3</li>
                    <li>t4</li>
                    <li>t5</li>
                </ul>
            </div>
            <br><div class="tagList">
            	<span class="tagListHeader">Tags</span>
            	
            </div>
        </div>
        
    </div>
    <div class="footer">
    
    </div>
</div>

<script>
// should be exported to JS file
var availableTags;
	
$(document).ready(function(){
	$('#keyword').click(function(){
		if($(this).val() == 'Type in keywords here'){
			$(this).css({'color': '#000000'}).val('');
		}
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