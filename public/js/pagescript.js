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

var trataTexto = function()
{
    $('.post-conteudo').each(function(index){
        var conteudo = $(this).html();
        var res = conteudo.replace(/\*{2}(.*)\*{2}/g, '<span class="spoiler">$1</span>'); // add spoiler
        res = res.replace(/~{2}(.*)~{2}/g, '<s>$1</s>'); // add traço
        res = res.replace(/'{3}(.*)'{3}/g, '<b>$1</b>'); // add negrito
        res = res.replace(/'{2}(.*)'{2}/g, '<i>$1</i>'); // add itálico
        res = res.replace(/={2}(.*)={2}/g, '<span class="vermelhotexto">$1</span>'); // add texto vermelho
        res = res.replace(/&gt;(.+)\n?/g, '<span class="green-text">&gt;$1</span><br>'); // add texto verde
        res = res.replace(/&gt;&gt;([0-9]+)/g, '<a href="#$1">&gt;&gt;$1</a>'); // add ref-posts
        res = res.replace(/((([A-Za-z]{3,9}:(?:\/\/)?)(?:[-;:&=\+\$,\w]+@)?[A-Za-z0-9.-]+|(?:www.|[-;:&=\+\$,\w]+@)[A-Za-z0-9.-]+)((?:\/[\+~%\/.\w-_]*)?\??(?:[-\+=&;%@.\w_]*)#?(?:[\w]*))?)/g, '<a href="https://href.li/?$1" ref="nofollow" target="_blank">$1</a>'); // add <a> nos links
        res = res.replace(/\n/g, '<br>'); // salta linhas
        $(this).html(res);
    });
};

var criaÇandom = function()
{
    $('body').css('background-image', 'url(/storage/res/çandom.gif)')
    var $audioElement = $("<audio>");
    $audioElement.attr({
        'src': '/storage/res/çandom.mp3',
        'autoplay':'autoplay',
        'loop':'loop'
    });
}

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

    if( $('#id-board-ç').length ){
        criaÇandom()
    }
    
    trataTexto();
});
