function hide(){
	if($('#right').width()==300){
		$('#right').css({'width':20}).css('float','right').css('marginLeft','-20px');
		$('#listcontent').hide();
		$('#shows').show();
		$('#hide').hide();
		$('#play').css({'width':$('.videobox').width()-$('#right').width()+"px"});
	}else{
		$('#right').css({'width':300}).css('float','left').css('marginLeft','-300px');
		$('#listcontent').show();
		$('#shows').hide();
		$('#hide').show();
		$('#play').css({'width':$('.videobox').width()-$('#right').width()+"px"});
	}
}
function main(id) {
	if (id == "1") {
		$('#ul1').show();
		$('#ul2').hide();
		$('#ul3').hide();
		$('#ul4').hide();
		$('#main1 i').addClass("dz");
		$('#main2 i').removeClass("dz");
		$('#main3 i').removeClass("dz");
		$('#main4 i').removeClass("dz");
		$('#main1').css('border-color','#00C300');
		$('#main2').css('border-color','#353535');
		$('#main3').css('border-color','#353535');
		$('#main4').css('border-color','#353535');
	} else if (id == "2") {
		$('#ul1').hide();
		$('#ul2').show();
		$('#ul3').hide();
		$('#ul4').hide();
		$('#main1 i').removeClass("dz");
		$('#main2 i').addClass("dz");
		$('#main3 i').removeClass("dz");
		$('#main4 i').removeClass("dz");
		$('#main1').css('border-color','#353535');
		$('#main2').css('border-color','#00C300');
		$('#main3').css('border-color','#353535');
		$('#main4').css('border-color','#353535');
	} else if (id == "3") {
		$('#ul1').hide();
		$('#ul2').hide();
		$('#ul3').show();
		$('#ul4').hide();
		$('#main1 i').removeClass("dz");
		$('#main2 i').removeClass("dz");
		$('#main3 i').addClass("dz");
		$('#main4 i').removeClass("dz");
		$('#main1').css('border-color','#353535');
		$('#main2').css('border-color','#353535');
		$('#main3').css('border-color','#00C300');
		$('#main4').css('border-color','#353535');
	} else if (id == "4") {
		$('#ul1').hide();
		$('#ul2').hide();
		$('#ul3').hide();
		$('#ul4').show();
		$('#main1 i').removeClass("dz");
		$('#main2 i').removeClass("dz");
		$('#main3 i').removeClass("dz");
		$('#main4 i').addClass("dz");
		$('#main1').css('border-color','#353535');
		$('#main2').css('border-color','#353535');
		$('#main3').css('border-color','#353535');
		$('#main4').css('border-color','#00C300');
	}
}

