$(document).ready(function(){
    $('.btn-ban').on('click', function(){
        document.getElementById('idPostInput').value = $(this).data('id-post');
        
    });
    
});
