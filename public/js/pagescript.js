var addNovoInputFile = function(elem, max){
    fileInputs = $('.form-post-file-input');
    for(var i=0; i < fileInputs.length; i++)
    {
        if(fileInputs[i].files.length === 0)
            return;
    }
    
    if(fileInputs.length >= max || elem.files.length === 0)
        return;
    
    var novoInput = "<div class=\"form-post-file-input-box\">";
    novoInput += "<input class=\"novo-post-form-item form-post-file-input\" name=\"arquivos[]\" type=\"file\" onchange=\"addNovoInputFile(this, " + max + ")\">";
    novoInput += "Spoiler <input name=\"arquivos-spoiler-" + (fileInputs.length + 1) + "\" type=\"checkbox\" value=\"spoiler\">"
    novoInput += "</div>"
    
    $(novoInput).appendTo(".form-post #form-post-file-input-div");
};

$(document).ready(function(){
    $('.btn-ban').on('click', function(){
        $('#idPostInput').val($(this).data('id-post'));
        
    });
    
    $('.btn-report').on('click', function(){
        $('#idPostReportInput').val($(this).data('id-post'));
        
    });
    
    $('.btn-mover-post').on('click', function(){
        $('.idPostMover').val($(this).data('id-post'));
        
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