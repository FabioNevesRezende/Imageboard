$(document).ready(function(){
    
    $('.btn-ban').on('click', function(){
        document.getElementById('idPostInput').value = $(this).data('id-post');
        
    });
    
    $('.btn-report').on('click', function(){
        console.log("valor id post: ");
        console.log($(this).data('id-post'));
        document.getElementById('idPostReportInput').value = $(this).data('id-post');
        
    });
    
    $('.a-nro-post').on('click', function(){
        document.getElementById('novo-post-conteudo').value += ">>" + $(this).text() + "\n";
    });
    
    
    $('#select-board-catalogo').on('change', function(e){
        boardMostrar = $(this).find(":checked").val();
        
        $('.catalogo-post-div').css("display", "inline-table");
        if(boardMostrar !== 'todas')
        {
            $('.catalogo-post-div').not('.catalogo-post-div-board-' + boardMostrar).css("display", "none");   
        }
    });
});