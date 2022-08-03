@extends('layouts.app')

@section('content')
    <div id="g-login" class="container-fluid py-16 d-flex flex-column align-center gag-container-bg">
        <div class="col-xs-12 col-sm-8 col-md-4" style="z-index: 2">
            <div style="max-width: 250px; margin: 0 auto 30px">
                <img style="width: 100%; height: auto" src="/images/logo-light.png" alt="Gaelan Medical">
            </div>
            <login-form></login-form>
        </div>
    </div>
@endsection
