{{-- @extends('layout') 
@section('content')
<div class="row doruk-content">
    <h4 style="color:#999"><i class="fab fa-wpforms"> </i> Kullanıcı Formu</h4>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="form-horizontal"  method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <input type="text" class="form-control input-radius" name="name" id="name" placeholder="AD SOYAD">
                    </div>
                    <div class="mb-3">
                        <input type="email" class="form-control input-radius" name="email" id="email" placeholder="E-MAIL">
                    </div>
                    <div class="mb-3">
                        <input type="password" class="form-control input-radius" name="password" id="password" placeholder="ŞİFRE">
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-10">
                            <select class="form-control input-radius" aria-label="Default select example">
                                <option disabled selected>Roller</option>
                                <option>Yönetici</option>
                                <option>Muhasebeci</option>
                                <option>Mühendis</option>
                                <option>Pazarlamacı</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-5">
                        <button class="btn btn-secondary text-white" type="submit">Kaydet</button>
                    </div>
                   
                </form>
            </div>
        </div>
    </div>
    <!-- end col -->
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <h4 class="card-title">Kullanacı Listesi</h4>              

                <div class="table-responsive">
                    <table class="table table-editable table-nowrap align-middle table-edits">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Ad Soyad</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Şifre</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr data-id="1">
                                <td data-field="id" style="width: 80px">1</td>
                                <td data-field="name">David McHenry</td>
                                <td data-field="age">24</td>
                                <td data-field="gender">Male</td>
                                <td data-field="gender">Male</td>
                                <td style="width: 100px">
                                    <a class="btn btn-outline-secondary btn-sm edit" title="Edit">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                </td>
                            </tr>                            
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div> <!-- end col -->
</div>
@endsection --}}

<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div>
                <x-label for="name" :value="__('Name')" />

                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
            </div>

            <!-- Email Address -->
            <div class="mt-4">
                <x-label for="email" :value="__('Email')" />

                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-label for="password" :value="__('Password')" />

                <x-input id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                required autocomplete="new-password" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-label for="password_confirmation" :value="__('Confirm Password')" />

                <x-input id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation" required />
            </div>

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-button class="ml-4">
                    {{ __('Register') }}
                </x-button>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
