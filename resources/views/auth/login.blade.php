@extends('layouts.app')

@section('content')
    <div id="g-login" class="container-fluid py-16 d-flex flex-column align-center gag-container-bg">
        <div class="col-xs-12 col-sm-8 col-md-6 mt-10" style="z-index: 2">
            {{-- <div class="logo-wrapper" style="max-width: 200px; margin: 0 auto 30px">
                <img style="width: 100%; height: auto" src="/images/logo-primary.png" alt="Gaelan Medical">
            </div> --}}
            <v-card id="loginform" class="px-5 pt-8 pb-5 rounded-lg glass" style="background-color: rgba(255, 255, 255, 0.9)">
                <div class="logo-wrapper" style="max-width: 175px; margin: 0 auto">
                    <img style="width: 100%; height: auto" src="/images/logo-primary.png" alt="Gaelan Medical">
                </div>
                <v-card-title class="px-5 pb-0">Login</v-card-title>
                <v-card-text class="py-5">
                    <v-form autocomplete="off" method="POST" action="{{ route('login') }}" ref="form"
                        v-model="loginValid" lazy-validation>
                        @csrf
                        <v-text-field outlined required autocomplete="off" id="username" type="username" name="username"
                            label="{{ __('Username') }}" v-model="loginEmail" :rules="loginEmailrules" autofocus
                            @error('username') value="{{ old('username') }}" error
                        error-messages="{{ old('username') }} do not match in our records" @enderror>
                        </v-text-field>
                        <v-text-field outlined required autocomplete="off" id="password" label="{{ __('Password') }}"
                            type="password" name="password" v-model="loginPassword" :rules="loginPasswordrules"
                            @error('password') error error-messages="{{ $message }}" @enderror>
                        </v-text-field>
                        <v-btn width="100%" height="55" large grey lighten-1
                            :class="`mb-3 ${loginValid == true ? 'primary' : 'grey lighten-2'}`" type="submit"
                            :disabled="!loginValid" @click="loginValidate">{{ __('LOGIN') }}</v-btn>
                        <div class="d-flex justify-space-between align-center my-3">
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}">{{ __('Reset Password') }}</a>
                            @endif
                        </div>
                        @if ($errors->has('username_error'))
                            <v-alert type="error">
                                {{ $errors->first() }}
                            </v-alert>
                        @endif
                    </v-form>
                </v-card-text>
            </v-card>
        </div>
    </div>
@endsection
