<h2><small>image</small></h2>

<div class="form-group-sm">
    {!! Form::label('translations['.$key.'][files][orientation]', '[files][orientation]:', ['class' => 'control-label']) !!}
    {!! Form::text('translations['.$key.'][files][orientation]', null, ['class' => 'form-control', 'readonly']) !!}
</div>

<!--- Intro Field --->
<div class="form-group-sm">
    {!! Form::label('translations['.$key.'][files][application]', '[files][application] '.$key.'', ['class' => 'control-label']) !!}
    {!! Form::text('translations['.$key.'][files][application]', null, ['class' => 'form-control', 'readonly']) !!}
</div>

<!--- orientation Field --->
<div class="form-group-sm">
    {!! Form::label('translations['.$key.'][files][contentDetails]', '[files][contentDetails] '.$key.'', ['class' => 'control-label']) !!}
    {!! Form::text('translations['.$key.'][files][contentDetails]', null, ['class' => 'form-control', 'readonly']) !!}
</div>

