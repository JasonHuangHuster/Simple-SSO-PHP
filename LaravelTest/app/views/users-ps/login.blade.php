@extends('master')
          
       
          
@section('styles')
{{ HTML::style('css/signin.css') }}
 
@section('content')
  <div class="loginBox" style="width:280px;margin:100px auto;">
  
    {{ Form::open(array('url' => 'login', 'class' => 'form-signin')) }}
<h2 class="form-signin-heading">请 登 录 ...</h2>
    <!-- Name -->
    <div class="control-group {{{ $errors->has('username') ? 'error' : '' }}}">
  
        <div class="control">
            {{ Form::text('username', Input::old('username'),array('class' => 'form-control','placeholder'=>'用 户 名','style'=>'width:250px')) }}
            {{ $errors->first('username') }}
        </div>
    </div>

    <!-- Password -->
    <div class="control-group {{{ $errors->has('password') ? 'error' : '' }}}">

        <div class="control">
            {{ Form::password('password',array('class' => 'form-control','placeholder'=>'密  码','style'=>'width:250px')) }}
            {{ $errors->first('password') }}
        </div>
    </div>

<!-- remmeber checkbox -->
    <div class='checkbox'>
    {{Form::checkbox('remember','remember-me')}}
    {{Form::label('remember','记住')}}
    </div>
    
    <!-- Login button -->
    <div class="control-group">
        <div class="controls" style="float:left">
            {{ Form::submit('登 录', array('class' => 'btn btn-lg btn-primary btn-block','style'=>'width:100px')) }}
        </div>
        <div class="regist" style="float:right">
        <p style="float:left">还没注册？</p>
           <a href='regist'>注册</a>
        </div>
    </div>

{{ Form::close() }}
</div>
@stop


