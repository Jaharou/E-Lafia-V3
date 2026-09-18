$(document).ready(function(){


$('#invoice_table').DataTable({

processing:true,

serverSide:true,


ajax:{

url:
base_url+
"admin/invoice_list",

type:"POST"

},


order:[

[0,'desc']

],



columns:[


{
data:'invoice_id'
},


{
data:'invoice_number'
},



{
data:null,

render:function(data)
{

return data.name+
" "+
data.prenom;

}

},



{
data:'title'
},


{
data:'net_amount'
},


{
data:'creation_timestamp'
}



],



language:{


search:"Recherche:",

lengthMenu:
"Afficher _MENU_ lignes",


info:
"_START_ à _END_ sur _TOTAL_"



}



});



});