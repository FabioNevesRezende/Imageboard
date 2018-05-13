$(document).ready(function(){
    
    $('.btn-ban').on('click', function(){
        document.getElementById('idPostInput').value = $(this).data('id-post');
        
    });
    
    $('.btn-report').on('click', function(){
        document.getElementById('idPostReportInput').value = $(this).data('id-post');
        
    });
    
    $('.a-nro-post').on('click', function(){
        console.log('teste');
        document.getElementById('novo-post-conteudo').value += ">>" + $(this).text() + "\n";
        
    });
    
});
