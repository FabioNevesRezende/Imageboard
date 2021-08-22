<div id="modalMoverPost" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Mover postagem <span class="idPostMover" value=""></span></h4>
      </div>
        <form class="form-horizontal" role="form" method="POST" action="{{ route('posts.mover') }}">
            <div class="modal-body">
                  {{ csrf_field() }}
                  <div class="row">
                      <div class="col-sm-2"></div>
                      <div class="col-sm-8 text-center">                          
                        <select class="novo-post-form-item form-control" name="novaboard" required>
                            @foreach($boards as $board)
                            <option value="{{ $board->sigla }}"> {{ $board->sigla }} - {{ $board->nome }}</option>
                            @endforeach
                        </select>
                      </div>
                      <div class="col-sm-2"></div>
                  </div>
                  <input type="hidden" class="idPostMover" name="idpost" value="">

            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                  <input type="submit" value="Mover post" class="btn btn-primary">
            </div>
        </form>
    </div>

  </div>
</div>