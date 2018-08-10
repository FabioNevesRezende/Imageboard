<div id="modalBan" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Banir usuário</h4>
      </div>
      <div class="modal-body">
        {!! Form::open(['route' => 'bans.userban']) !!}
        {{ csrf_field() }}
        {{  Form::text('motivo', null, array('class' => 'novo-post-form-item form-control', 'maxlength' => '255', 'placeholder' => 'Motivo' )) }}
        Permaban: {{ Form::checkbox('permaban', 'permaban', false,array('class'=>'novo-post-form-item')) }}
    
        {{  Form::number('nro_horas', null, array('class' => 'novo-post-form-item form-control', 'placeholder' => 'Qtdade de horas', 'min' => '1', 'max'=>'24')) }}
        {{  Form::number('nro_dias', null, array('class' => 'novo-post-form-item form-control', 'placeholder' => 'Qtdade de dias', 'min' => '1' )) }}

        {{ Form::select('board', [
            $siglaBoard => $siglaBoard,
            'todas' => 'Todas as boards'],  null, array('class' => 'novo-post-form-item form-control', 'required', 'maxlength' => '10')) 
        }}
        
        {{  Form::hidden('siglaboard', $siglaBoard) }}
        {{  Form::hidden('idpost', '', ['id' => 'idPostInput']) }}
        {{ Form::submit('Banir', array('class' => 'btn btn-primary form-control') ) }}
    
        {!! Form::close() !!} 
              
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
      </div>
    </div>

  </div>
</div>