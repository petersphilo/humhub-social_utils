<?php

/**
 * Peter Zieseniss
 * Copyright (C) 2022
 * 
 * Please consider making a donation using the button found on the main page of this module; it would help me greatly..
 *
 * This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
 */


/* use Yii; */
use humhub\modules\admin\permissions\ManageModules;

use yii\helpers\Url;
use yii\helpers\Html;
use yii\base\Application;
use yii\db\Connection; 
/* use yii\base\Module;  */
use yii\web\AssetBundle;


use humhub\modules\social_utils; 

if (!\Yii::$app->user->can(ManageModules::class)) {
	return; 
	}

$MyBR='<br>'; 

?>
<div class="panel panel-default">
	<div class="panel-heading"><strong>Social Utils</strong></div>
	<div class="panel-body">
		<style>
			.myjustify {text-align: justify; }
			.mycentertext {text-align: center; }
			.myrighttext {text-align: right !important; }
			.myunderline {text-decoration: underline; }
			.nounderline {text-decoration: none; }
			.mybold {font-weight: bold; }
			.myita {font-style: italic; }
			.mynoita {font-style: normal; }
			.margbotfull {margin-bottom: 1em !important; }
			.margbothalf {margin-bottom: 0.5em !important; }
			.margbotquart {margin-bottom: 0.25em !important; }
			.myjustify {text-align: justify; }
			.mysixteenpix {font-size: 1.2em; line-height: 1.4em; }
			.myfifteenpix {font-size: 1.1em; line-height: 1.3em; }
			.mySmallerText {font-size: 0.8em; line-height: 1em; }
			.mySlightlySmallerText {font-size: 0.9em; line-height: 1.2em; }
			.myfont, .myfont div, .myfont span {font-family: "Open Sans", "Helvetica Neue", Helvetica Neue, "Helvetica", Helvetica, Verdana, sans serif !important; }
			.myturquoiseDark, .myturquoiseDark a, .myturquoiseDark a:visited, a.myturquoiseDark, a.myturquoiseDark:visited {color: #1a8285; } /* 0070C0 orig: 014539 */
			.myturquoiseDark a:hover, a.myturquoiseDark:hover {color: #215868; }
			.myRed, .myRed a, .myRed a:visited, a.myRed, a.myRed:visited {color: #ff0000; } /* 0070C0 orig: 014539 */
			.SlideyBlock {display: inline-block; margin: 0.5em 1em 0.5em 0em; min-width: 16em; vertical-align: top; }
			.PeriodSelect, .MyPointer {cursor: pointer; }
			.TheGroupSpaceMembersReport, .GroupSpaceDataLoading {display: none; }
			.SelectError {background-color: #ff0000; }
			.myFullWidth{width: 100%; margin: 0 auto; min-height: 500px; height: 60%; }
			.MyDataLoading {
				text-shadow: 0px 0px 1px rgba(255,255,255,1), 0px 0px 7px rgba(90,255,90,0.75), 0px 0px 14px rgba(60,255,60,0.6), 0px 0px 22px rgba(90,255,90,0.5); color: rgba(26,130,133,1); 
				animation: GlowyGlowAnim 2s linear infinite; 
				}
			@keyframes GlowyGlowAnim {
				0% {text-shadow: 0px 0px 1px rgba(255,255,255,1), 0px 0px 7px rgba(90,255,90,0.75), 0px 0px 14px rgba(60,255,60,0.6), 0px 0px 22px rgba(90,255,90,0.5); color: rgba(26,130,133,1); }
				50% {text-shadow: 0px 0px 1px rgba(255,255,255,0.1), 0px 0px 5px rgba(90,255,90,0.1), 0px 0px 9px rgba(60,255,60,0.1), 0px 0px 18px rgba(90,255,90,0.1); color: rgba(26,130,133,0.75); }
				100% {text-shadow: 0px 0px 1px rgba(255,255,255,1), 0px 0px 7px rgba(90,255,90,0.75), 0px 0px 14px rgba(60,255,60,0.6), 0px 0px 22px rgba(90,255,90,0.5); color: rgba(26,130,133,1); }
				}
		</style>
		
		<div style='margin: 1em 1em 0 1em; ' id='' class='myjustify myfont'>
	
			<h4>&#x1F39B; Social Utils! &#x1F4C2; &#x1F9F0; &#x1F6E0;</h4>
			
			<br><br>
			<!-- Logins, Posts, Comments, Likes, Follows -->
	
			<div class='margbothalf'>
				<div class='myjustify margbotfull mysixteenpix mybold myunderline'>
					General:
				</div>
				<div class='mycentertext margbotfull myita myturquoiseDark SpacesDataReady MyDataLoading'>Please wait for data to load..</div>
				<div class='myjustify margbothalf'>
					<span class='myfifteenpix myturquoiseDark'>Choose the Space: </span> 
					<select class='TheSpacesSelect'></select> 
				</div>
				<div class='myjustify margbotfull'>
					<span class='myfifteenpix myturquoiseDark'>Choose the Group: </span>
					<select class='TheGroupSelect'></select>
				</div>
				<div class='myjustify margbotfull'>
					<div class='myfifteenpix TheGroupSpaceMembersReport'>There are currently..<br>
						<div class='myjustify margbotfull'>
							<span class='myfifteenpix myturquoiseDark GroupPopulation'>..</span> Total Group Population (any space); <br>
							<span class='myfifteenpix myturquoiseDark NotYetMembers'>..</span> from this group are Not Yet Members; <br>
							<span class='myfifteenpix myturquoiseDark GroupSpaceMembers'>..</span> members from this group; <br>
							<span class='myfifteenpix myturquoiseDark GroupSpaceInvites'>..</span> invites for this group; <br>
							<span class='myfifteenpix myturquoiseDark GroupSpaceTotal'>..</span> total people from this group already with this Space<br>
						</div>
						<br>
						<span class='myfifteenpix myturquoiseDark InviteThemAll MyPointer'>Invite Them</span> ... 
						or ... <span class='myfifteenpix myturquoiseDark MakeThemMembers MyPointer'>Make them full members</span> ... 
						or ... <span class='myfifteenpix myRed KickThemAll MyPointer'>Un-Invite Them!</span>
						<span class='myita myturquoiseDark MyDataLoading GroupSpaceDataLoading'>Loading..</span>
						<br><span class='SocUtilResult'></span>
					</div>
				</div>
				<br>
			</div>
			<hr>
		</div>
		
		<br><br>
		
		<?php
			use humhub\modules\social_utils\Assets; 
			$MyAssets=humhub\modules\social_utils\Assets::register($this,'POS_BEGIN');
		?>
		<!-- <script src='<?php echo $MyAssets->baseUrl.'/'.$MyAssets->js[0]; ?>'></script> -->
		
		
		
		<span class='mySmallerText'>Process Time: <span class='ProcessTime'>...</span></span>
		
		<br>
		
		<span class='testarea'></span>
		<br>
		
		<!-- Begin desperation
		<div class='mycentertext margbothalf mySlightlySmallerText'>
			if you find this module useful, please consider a donation<br>
			it'd really, really, really, really, <br>
			really, really, really help..
		</div>
		<div id="donate-button-container" class='mycentertext'>
			<div id="donate-button"></div>
			<script src="https://www.paypalobjects.com/donate/sdk/donate-sdk.js" charset="UTF-8"></script>
			<script>
				PayPal.Donation.Button({
					env:'production',
					hosted_button_id:'AEA7Q4V5RMY4S',
					image: {
						src:'https://www.paypalobjects.com/en_US/FR/i/btn/btn_donateCC_LG.gif',
						alt:'Donate with PayPal button',
						title:'Please consider making a donation; it would help me greatly..',
						}
					}).render('#donate-button');
			</script>
		</div>
		 -->
		<!-- i wish i could say that's the end of it.. -->
	</div>
</div>

