<div id="modalBan" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Banir usuário</h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" role="form" method="POST" action="{{ route('bans.userban') }}">
        {{ csrf_field() }}
        <input type="text" class="novo-post-form-item form-control" maxlength="255" placeholder="Motivo" name="motivo">
        Permaban: <input type="checkbox" class="novo-post-form-item" value="permaban" name="permaban">
    
        <input type="number" class="novo-post-form-item form-control" placeholder="Qtdade de horas" min="1" max="24" name="nro_horas">
        <input type="number" class="novo-post-form-item form-control" placeholder="Qtdade de dias" min="1" name="nro_dias">

        <select name="board" class="novo-post-form-item form-control" maxlength="10" required>
            <option value="{{ $siglaBoard }}">{{ $siglaBoard }}</option>
            <option value="todas">Todas as boards</option>
        </select>
        
        <input type="hidden" name="siglaboard" value="{{ $siglaBoard }}">
        <input type="hidden" id="idPostInput" name="idpost" value="">
        <input type="submit" class="btn btn-primary form-control" value="Banir">
    
        </form>
              
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
      </div>
    </div>

  </div>
</div>