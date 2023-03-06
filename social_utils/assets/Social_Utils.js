$(function(){
	var MyDailyChartSelector=$('#MyDailyChart'),
		MyHourlyBreakdownChartSelector=$('#MyHourlyBreakdownChart'),
		
		
		GroupSpaceDataLoading="<div class='mycentertext margbothalf myita myturquoiseDark MyDataLoading GroupSpaceDataLoading'>Please wait for data to load..</div>",
		MyHourlyBreakdownChart,
		
		MyTestText='', 
		MyBR='<br>', 
		GDSelector='',
		
		MyCurrentGetURL,
		MyNewGetURL,
		MyFetchURL;
	
	MyCurrentGetURL=window.location.search; 
	/*
	if(MyCurrentGetURL.length){MyNewGetURL=MyCurrentGetURL+'&'; }
	else{MyNewGetURL='./?'; }
	*/
	
	var SpacesSelectPost=$.post(
		MyCurrentGetURL,
		{ListAllSpaces:'Yes'},
		function(SSData){
			$('.TheSpacesSelect').html(SSData); 
			},
		"text"
		); 
	SpacesSelectPost.done(function(){
		$('.SpacesDataReady').removeClass('MyDataLoading').hide(); 
		}); 
	
	
	var GroupSelectPost=$.post(
		MyCurrentGetURL,
		{ListAllGroups:'Yes'},
		function(GSData){
			$('.TheGroupSelect').html(GSData); 
			},
		"text"
		); 
	
	CheckGroupSpaceSelect=function(){
		var TheSpacesSelect=$('.TheSpacesSelect'), 
			TheSpacesSelectVal=TheSpacesSelect.val(),
			TheGroupSelect=$('.TheGroupSelect'), 
			TheGroupSelectVal=TheGroupSelect.val(); 
		TheSpacesSelect.removeClass('SelectError'); 
		TheGroupSelect.removeClass('SelectError'); 
		if(TheSpacesSelectVal==0){
			TheSpacesSelect.addClass('SelectError'); 
			}
		if(TheGroupSelectVal==0){
			TheGroupSelect.addClass('SelectError'); 
			}
		if(TheSpacesSelectVal!=0&&TheGroupSelectVal!=0){
			$('.TheGroupSpaceMembersReport').fadeIn(); 
			$('.GroupSpaceDataLoading').show(); 
			console.log('TheSpacesSelectVal: '+TheSpacesSelectVal+'; TheGroupSelectVal: '+TheGroupSelectVal); 
			$.post(
				MyCurrentGetURL,
				{GetGroupSpaceMembers:'Yes',TheSpacesSelectVal:TheSpacesSelectVal,TheGroupSelectVal:TheGroupSelectVal},
				function(GSMData){
					$('.GroupPopulation').text(GSMData['GroupPopulation']); 
					$('.NotYetMembers').text(GSMData['NotYetMembers']); 
					$('.GroupSpaceMembers').text(GSMData['GroupSpaceMembers']);
					$('.GroupSpaceInvites').text(GSMData['GroupSpaceInvites']); 
					$('.GroupSpaceTotal').text(GSMData['GroupSpaceTotal']); 
					$('.GroupSpaceDataLoading').hide(); 
					},
				"json"
				); 
			return true; 
			}
		else{
			return false; 
			}
		}; 
	
	GroupSpaceInviteOrMember=function(IoM){
		var TheSpacesSelect=$('.TheSpacesSelect'), 
			TheSpacesSelectVal=TheSpacesSelect.val(),
			TheGroupSelect=$('.TheGroupSelect'), 
			TheGroupSelectVal=TheGroupSelect.val(); 
		TheSpacesSelect.removeClass('SelectError'); 
		TheGroupSelect.removeClass('SelectError'); 
		if(TheSpacesSelectVal==0){
			TheSpacesSelect.addClass('SelectError'); 
			}
		if(TheGroupSelectVal==0){
			TheGroupSelect.addClass('SelectError'); 
			}
		if(TheSpacesSelectVal!=0&&TheGroupSelectVal!=0){
			$('.GroupSpaceDataLoading').show(); 
			console.log('TheSpacesSelectVal: '+TheSpacesSelectVal+'; TheGroupSelectVal: '+TheGroupSelectVal); 
			$.post(
				MyCurrentGetURL,
				{InviteOrAddMembers:'Yes',TheSpacesSelectVal:TheSpacesSelectVal,TheGroupSelectVal:TheGroupSelectVal,IoM:IoM},
				function(IoAData){
					var UsersAffected=IoAData['UsersAffected'], 
						ActionTaken=IoAData['ActionTaken']; 
					$('.GroupSpaceDataLoading').hide(); 
					$('.SocUtilResult').text(UsersAffected+' users '+ActionTaken); 
					CheckGroupSpaceSelect(); 
					},
				"json"
				); 
			return true; 
			}
		else{
			return false; 
			}
		}; 
	
	KickThemAll=function(IoM){
		var TheSpacesSelect=$('.TheSpacesSelect'), 
			TheSpacesSelectVal=TheSpacesSelect.val(),
			TheGroupSelect=$('.TheGroupSelect'), 
			TheGroupSelectVal=TheGroupSelect.val(); 
		TheSpacesSelect.removeClass('SelectError'); 
		TheGroupSelect.removeClass('SelectError'); 
		if(TheSpacesSelectVal==0){
			TheSpacesSelect.addClass('SelectError'); 
			}
		if(TheGroupSelectVal==0){
			TheGroupSelect.addClass('SelectError'); 
			}
		if(TheSpacesSelectVal!=0&&TheGroupSelectVal!=0){
			$('.GroupSpaceDataLoading').show(); 
			console.log('TheSpacesSelectVal: '+TheSpacesSelectVal+'; TheGroupSelectVal: '+TheGroupSelectVal); 
			var KickConf=0; 
			if(confirm('Are you sure you want to remove them?')){
				/**/
				KickConf=1; 
				$.post(
					MyCurrentGetURL,
					{KickThemAll:'Yes',TheSpacesSelectVal:TheSpacesSelectVal,TheGroupSelectVal:TheGroupSelectVal,KickConf:KickConf},
					function(KickData){
						var UsersAffected=KickData['UsersAffected']; 
						$('.GroupSpaceDataLoading').hide(); 
						$('.SocUtilResult').text(UsersAffected+' users Removed'); 
						CheckGroupSpaceSelect(); 
						},
					"json"
					); 
				
				}
			else{
				$('.GroupSpaceDataLoading').hide(); 
				return false; 
				}
			return true; 
			}
		else{
			return false; 
			}
		}; 
	
	$('.InviteThemAll').click(function(){
		if(GroupSpaceInviteOrMember(1)){
			//$(this).addClass('MyDataLoading'); 
			//CheckGroupSpaceSelect(); 
			} 
		
		}); 
	
	$('.MakeThemMembers').click(function(){
		if(GroupSpaceInviteOrMember(3)){
			//$(this).addClass('MyDataLoading'); 
			//CheckGroupSpaceSelect(); 
			} 
		
		}); 
	
	$('.KickThemAll').click(function(){
		if(KickThemAll()){
			//$(this).addClass('MyDataLoading'); 
			//CheckGroupSpaceSelect(); 
			} 
		
		}); 
	
	$('.TheSpacesSelect, .TheGroupSelect').on('change',function(){
		var TheVal=$(this).val(); 
		if(TheVal!=0){
			$(this).removeClass('SelectError'); 
			CheckGroupSpaceSelect(); 
			}
		else{$(this).addClass('SelectError'); }
		}); 
	
	}); 
