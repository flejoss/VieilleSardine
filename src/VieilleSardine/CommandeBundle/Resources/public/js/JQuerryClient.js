// JavaScript Document
 $(document).ready(function(e) {
     // Autocomplétion de tous les champs en fonction de l'ID du client
    $("#form_idClient").autocomplete({
         source:tabClients,
         select: function( event, ui ) {
            $( "#form_idClient" ).val( ui.item.label );
            $("#form_nom").val(htmlEntities(ui.item.desc));
            $("#form_pays").val(ui.item.value);
            $("#form_prenom").val(ui.item.prenom);
            $("#form_numeroVoie").val(ui.item.numeroVoie);
            $("#form_typeVoie").val(ui.item.typeVoie);
            $("#form_codePostal").val(ui.item.codePostal);
            $("#form_ville").val(ui.item.ville);
        
            
            return false;
          }
    }).data( "ui-autocomplete" )._renderItem = function( ul, item ) {
      return $( "<li>" )
        .append( "<a style=\"font-size:12px;\">" + item.label + " - " + item.desc + "</a>" )
        .appendTo( ul );
    };
    
            
        function htmlEntities(str) {
    return String(str).replace('\'', '&#039;');
        
    }


}); 
