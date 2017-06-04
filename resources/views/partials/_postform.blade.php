{!! Form::open(['route' => 'posts.store', 'enctype' => 'multipart/form-data']) !!}
{{ csrf_field() }}
{{  Form::hidden('nomeboard', $nomeBoard) }}
{{  Form::hidden('insidepost', $insidePost) }}
{{  Form::text('assunto', null, array('class' => 'novo-post-form-item form-control', 'maxlength' => '255', 'placeholder' => 'Assunto' )) }}
{{  Form::file('arquivos[]', array('class' => 'novo-post-form-item', 'required'=>'','multiple' => '')) }}
{{  Form::textarea('conteudo', null, array('class' => 'novo-post-form-item form-control', 'placeholder' => 'Mensagem', 'rows'=>'5', 'maxlength' => '65535')) }}

<div class="row">
    <div class="col-sm-6">
        Sage {{ Form::checkbox('sage', 'sage', false,array('class'=>'novo-post-form-item')) }}
    </div>
    <div class="col-sm-6">
        {{ Form::submit('Postar', array('class' => 'btn btn-primary btn-block form-control') ) }}
    </div>
</div>

{!! Form::close() !!}