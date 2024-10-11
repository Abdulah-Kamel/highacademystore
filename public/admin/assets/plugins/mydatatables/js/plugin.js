$(document).ready(function() {
	var table= $('#myTable').DataTable( {

        colReorder: true,
        responsive: true
	} );
/*
 to creat input search for every column
    $('#addSearchInputs').click( function () {
       $('.datatabel thead tr:eq(0) ').css("display" ,"table-row");
      // Setup - add a text input to each footer cell
    $('.datatabel thead tr:eq(0) th').each( function () {
        var title = $('.datatabel thead tr:eq(1) th').eq( $(this).index() ).text();

        $(this).html( '<input type="text" placeholder="Search '+title+'" style="max-width:60px;"/>' );
    } );
      // Apply the filter
    table.columns().every(function (index) {
        $('.datatabel thead tr:eq(0) th:eq(' + index + ') input').on('keyup change', function () {
            table.column($(this).parent().index() + ':visible')
                .search(this.value)
                .draw();
        });
    });
  });*/

    
   

    $('#addSearchInputs').click( function () {
$('#myTable thead tr:eq(0) ').css("display" ,"table-row");

    $("#myTable thead tr:eq(0) th").each( function ( i ) {
        var select = $('<select class="selectpicker form-control"  data-show-subtext="true" data-live-search="true"><option value="">اختر</option></select>')
            .appendTo( $(this).empty() )
            .on( 'change', function () {
                table.column( i )
                    .search( $(this).val() )
                    .draw();
            } );
 
        table.column( i ).data().unique().sort().each( function ( d, j ) {
            select.append( '<option value="'+d+'">'+d+'</option>' )
        } );
        $('#myTable thead tr:eq(0) th:first-child').html("");
         $('#myTable thead tr:eq(0) th:last-child').html("");

    } );
});





	$('#myTable tbody').on( 'click', 'tr', function () {
        if ( $(this).hasClass('selected') ) {
            $(this).removeClass('selected');
        }
        else {
           table.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
        }
    } );
 
    $('#Delete-Record').click( function () {
        table.row().remove().draw( false );
         $('.modal').modal('toggle');
         showLoading();
         showAlertDelete();

    } );


    function showAlertDelete() {

        $('.top-line').html('<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button><strong>تم المسح !</strong> لقد قمت بحذف بعض العناصر ...</div>'
).fadeIn().delay(4000).fadeOut();
   
}


function showLoading() {

$('#preloading').html('<div id="load"></div>');
window.setTimeout(function(){
     $('#load').css("display", "none");
    $('#Main-content').css("display", "block");
  }, 1000);
}
	/*$('.datatabel tbody').on( 'click', 'tr', function () {
        $(this).toggleClass('selected');
    } );*/
 

} );






