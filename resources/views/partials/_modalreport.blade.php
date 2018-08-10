<div id="modalReport" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Reportar postagem</h4>
      </div>
      <div class="modal-body">
        {!! Form::open(['route' => 'posts.report']) !!}
        {{ csrf_field() }}
        <div class="row">
            <div class="col-sm-2"></div>
            <div class="col-sm-8 text-center">
                {{  Form::text('motivo', null, array('class' => 'novo-post-form-item form-control', 'maxlength' => '255', 'placeholder' => 'Motivo' )) }}
        
            </div>
            <div class="col-sm-2"></div>
        </div>
        
        {{  Form::hidden('siglaboard', $siglaBoard) }}
               
        {{  Form::hidden('idpost', '', ['id' => 'idPostReportInput']) }}
        
        <div class="row">
            <div class="col-sm-2">
        {{ Form::submit('Reportar', array('class' => 'btn btn-primary form-control') ) }}
            </div>
        </div>
        {!! Form::close() !!} 
              
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
      </div>
    </div>

  </div>
</div>