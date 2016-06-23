<h2><small>image</small></h2>
<!--- Author Field --->


<div class="form-group-sm">
    {!! Form::label('translations['.$key.'][images][height]', '[images][height]:', ['class' => 'control-label']) !!}
    {!! Form::text('translations['.$key.'][images][height]', null, ['class' => 'form-control', 'readonly']) !!}
</div>

<!--- Intro Field --->
<div class="form-group-sm">
    {!! Form::label('translations['.$key.'][images][width]', '[images][width] '.$key.'', ['class' => 'control-label']) !!}
    {!! Form::text('translations['.$key.'][images][width]', null, ['class' => 'form-control', 'readonly']) !!}
</div>

<!--- orientation Field --->
<div class="form-group-sm">
    {!! Form::label('translations['.$key.'][images][orientation]', '[images][orientation] '.$key.'', ['class' => 'control-label']) !!}
    {!! Form::text('translations['.$key.'][images][orientation]', null, ['class' => 'form-control', 'readonly']) !!}
</div>

<!--- orientation Field --->
<div class="form-group-sm">
    {!! Form::label('translations['.$key.'][images][ratio]', '[images][ratio] '.$key.'', ['class' => 'control-label']) !!}
    {!! Form::text('translations['.$key.'][images][ratio]', null, ['class' => 'form-control', 'readonly']) !!}
</div>

<!--- orientation Field --->
<div class="form-group-sm">
    {!! Form::label('translations['.$key.'][images][provider]', '[images][provider] '.$key.'', ['class' => 'control-label']) !!}
    {!! Form::text('translations['.$key.'][images][provider]', null, ['class' => 'form-control', 'readonly']) !!}
</div>

<!--- orientation Field --->
<div class="form-group-sm">
    {!! Form::label('translations['.$key.'][images][contentDetails]', '[images][contentDetails] '.$key.'', ['class' => 'control-label']) !!}
    {!! Form::text('translations['.$key.'][images][contentDetails]', null, ['class' => 'form-control', 'readonly']) !!}
</div>

