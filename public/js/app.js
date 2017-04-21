
$(document).ready(function(){

})

var app = {
	isJson:function(str) {
	    try {
	        JSON.parse(str)
	    } catch (e) {
	        return false
	    }
	    return true
	},
	upload:function(obj){
			
			$("#message_upload").html("");
			$("#message_upload").removeClass('success');
			$("#message_upload").removeClass('fail');

			var inputFileImage = document.getElementById(obj);
			var file = inputFileImage.files[0];

			var loading = $("<img>").attr({"src":"/imgs/default/loading.gif","class":"loading"});
			$(".sub-content-popup").append(loading);
			app.popup();

			if(file!==undefined && file!==null){

				var formData = new FormData();
				formData.append('avatar',file);
				formData.append('service','user');
				formData.append('method','uploadAvatar');
				
	            $.ajax({
	                url: '/services',  //server script to process data
	                type: 'POST',
					dataType:'json',
	                success: function(r) {
	                	
	                	app.popup();

	                	$("form[name=profile] b[name=message]").removeClass("error")

	                	if(r.success){
	                		$("form[name=profile] img[name=preview]").attr("src","/imgs/users/"+r.data.id+"/avatar-temp."+r.data.ext)
	                		$("form[name=profile] b[name=message]").text("")
	                	}else{
	                		$("form[name=profile] b[name=message]").text(r.message);
	                		$("form[name=profile] b[name=message]").addClass("error");
	                		
	                	}
        			
	                },
	                error: errorHandler = function() {

	                },
	                // Form data
	                data: formData,
	                // Options to tell jQuery not to process data or worry about the content-type
	                cache: false,
	                contentType: false,
	                processData: false
	            }, 'json');
	            

			}else{

			}

	},
	popup:function(){

		if( $("#global-popup").hasClass("invisible") ) $("#global-popup").removeClass("invisible");
		else{
			$("#global-popup").addClass("invisible");
			$("#global-popup .sub-content-popup").empty();
		}
	}
}

var brocolis = {
	page : function(i){

		$.post( '/services' , {'service':'User','method':'paginator','page':i}).done(function(data){
			if(app.isJson(data)){
				data = JSON.parse(data)
				$(".content-list").empty()
				$(".content-list").append(data.list)	
			}
		}).fail(function(jqXHR, exception) {
		    console.log(jqXHR.responseText)
		})

	},
	psearch: function(){
		
		if( $( "#s-users-list" ).hasClass( "invisible" ) ){
			$("#s-users-list").removeClass("invisible");
			$('form[name=search-brocoli] input[name=nalias]').keyup(function(e){
				brocolis.search(e.key)
			})
			$('form[name=search-brocoli] input[name=nalias]').keyup(function(e){ if(e.keyCode == 8) brocolis.search() })
			$( "select[name=idcountry]" ).change(brocolis.search)
		}else{
			$("#s-users-list").addClass("invisible");
			$( "select[name=idcountry]" ).unbind( "change" )
			$('form[name=search-brocoli] input[name=nalias]').unbind("keyup")
			$('form[name=search-brocoli] input[name=nalias]').unbind("keyup")
		}

	},
	search : function(word){

		var nalias = $('form[name=search-brocoli] input[name=nalias]').val() ,
			country = $('form[name=search-brocoli] select[name=idcountry]').val()

		//if(word!==undefined) nalias += word

		console.log(nalias)
		brocolis.sendSearch(nalias,country);

	},
	sendSearch : function(nalias,country){

		$.post( '/services' , {'service':'User','method':'search','nalias':nalias,'idcountry':country }).done(function(data){
			console.log(data);

			if(app.isJson(data)){
				data = JSON.parse(data)
				$(".content-list").empty()
				$(".content-list").append(data.list)	
			}
			

		}).fail(function(jqXHR, exception) {
		    console.log(jqXHR.responseText)
		})

	},
	reset : function(){
		brocolis.sendSearch(null,null);
	},
	reverse: function(){

		$.post( '/services' , {'service':'User','method':'reverse'}).done(function(data){

			if(app.isJson(data)){
				data = JSON.parse(data)
				$(".content-list").empty()
				$(".content-list").append(data.list)	
			}
			
		}).fail(function(jqXHR, exception) {
		    console.log(jqXHR.responseText)
		})
	}

}
