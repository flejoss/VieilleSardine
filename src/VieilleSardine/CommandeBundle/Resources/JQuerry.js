// JavaScript Document
 $(document).ready(function(e) {
     // Autocomplétion de tous les champs en fonction de l'ID du produit
    $("#form_idProduit").autocomplete({
         source:tabProduits,
         select: function( event, ui ) {
            $( "#id_PrixProduit" ).val( ui.item.value );
            $("#id_LibelleProduit").val(htmlEntities(ui.item.desc));
            $("#form_idProduit").val(ui.item.label);
            return false;
          }
    }).data( "ui-autocomplete" )._renderItem = function( ul, item ) {
      return $( "<li>" )
        .append( "<a style=\"font-size:12px;\">" + item.label + " - " + item.desc + "</a>" )
        .appendTo( ul );
    };
    $("#id_PrixProduit").keyup(function(){
       majPrix(); 
    });
    $("#form_quantite").keyup(function(){
        majPrix();
    });

   
   $("#idFormCmdVPC").submit(function(){
       var ligne = $("<tr><td>"+"<input name =\"Produit\" type =\"hidden\" value=\""+$("#form_idProduit").val()+"\"/>"+$("#form_idProduit").val()+"</td><td>"+"<input name =\"LibProduit\" type =\"hidden\" value=\""+$("#id_LibelleProduit").val()+"\"/>"+$("#id_LibelleProduit").val()+"</td><td>"+"<input name =\"Qte\" type =\"hidden\" value=\""+$("#form_quantite").val()+"\"/>"+$("#form_quantite").val()+"</td><td>"+"<input name =\"Prix\" type =\"hidden\" value=\""+$("#id_PrixProduit").val()+"\"/>"+$("#id_PrixProduit").val()+"</td><td>"+"<input name =\"PrixTotal\" type =\"hidden\" value=\""+$("#id_PrixTotal").val()+"\"/>"+$("#id_PrixTotal").val()+"</td></tr>");
       ligne.hide();
       $("#ListeProduits").append(ligne);
       ligne.fadeIn("slow");    
       return false;
   });
}); 

// Fonction utilisée pour calculer le prix total automatiquement
function majPrix(){
            if($("#id_PrixProduit").val()!="" && $("#form_quantite").val()!="")
		$("#id_PrixTotal").val(parseInt($("#id_PrixProduit").val()) * parseInt($("#form_quantite").val()));
            else
                $("#id_PrixTotal").val("");
	}
        
        
        function htmlEntities(str) {
    return String(str).replace('\'', '&#039;');
        
    }
