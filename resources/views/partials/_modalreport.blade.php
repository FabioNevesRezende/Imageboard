<div id="modalReport" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Reportar postagem</h4>
      </div>
        <form class="form-horizontal" role="form" method="POST" action="{{ route('posts.report') }}">
            <div class="modal-body">
                  {{ csrf_field() }}
                  <div class="row">
                      <div class="col-sm-2"></div>
                      <div class="col-sm-8 text-center">
                          <input class="novo-post-form-item form-control" maxlength="255" placeholder="Motivo" name="motivo" required>
                      </div>
                      <div class="col-sm-2"></div>
                  </div>

                  <input type="hidden" name="siglaboard" value="{{ $siglaBoard }}">
                  <input type="hidden" id="idPostReportInput" name="idpost" value="">

            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                  <input type="submit" value="Reportar" class="btn btn-primary">
            </div>
        </form>
    </div>

  </div>
</div>