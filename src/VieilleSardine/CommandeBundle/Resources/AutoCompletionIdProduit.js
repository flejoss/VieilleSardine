var cache = {};
 
$(document).ready(function(){
	$("input[data-id=idProduit]").autocomplete({
		source: function (request, response)
		{
			//Si la réponse est dans le cache
			if (request.term in cache)
			{
				response($.map(cache[request.term], function (item)
				{
					return {
						label: item.IdProduit,
						value: function ()
						{
							if ($(this).attr('data-id') == 'idProduit')
							{
								$('input[data-id=idProduit]').val(item.IdProduit);
								return item.IdProduit;
							}
						
						}
					};
				}));
			}
			//Sinon -> Requete Ajax
			else
			{
 
		            var objData = {};
		            var url = $(this.element).attr('data-url');
		            if ($(this.element).attr('data-id') == 'idProduit')
		            {
		            	objData = { idProduit: request.term };
		            }
		        
 
				$.ajax({
					url: url,
					dataType: "json",
					data : objData,
					type: 'POST',
					success: function (data)
					{
						//Ajout de reponse dans le cache
						cache[request.term] = data;
 
						response($.map(data, function (item)
						{
							return {
								label: item.IdProduit,
								value: function ()
								{
									if ($(this).attr('data-id') == 'idProduit')
									{
										$('input[data-id=idProduit]').val(item.IdProduit);
										return item.IdProduit;
									}
								
								}
							};
						}));
					},
					error: function (jqXHR, textStatus, errorThrown)
					{
						console.log(textStatus, errorThrown);
					}
				});
			}
		},
		minLength: 3,
		delay: 300
	});
});
