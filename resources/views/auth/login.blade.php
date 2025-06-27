@extends('layouts.base')

@section('title', 'Login - OneTap')
@section('header-title', 'Welcome Back!')
@section('header-subtitle', 'Log in to capture your daily moment')

@section('content')

<div class="flex-1 bg-gray-900 min-h-screen flex items-center justify-center px-6 ml-4 mr-4">
    <div class="w-full max-w-md bg-gray-800 rounded-2xl p-8 shadow-lg border border-gray-700">
        <h2 class="text-2xl font-bold text-white text-center mb-4">Sign in to OneTap</h2>

        @if (session('error'))
            <div class="p-3 text-red-700 bg-red-100 rounded mb-4 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="space-y-6">
            @csrf

            <div>
                <label for="username" class="block text-sm font-medium text-gray-300 mb-1">username</label>
                <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus
                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('username')
                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-300 mb-1">Password</label>
                <input type="password" id="password" name="password" required
                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('password')
                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center text-sm text-gray-400">
                    <input type="checkbox" name="remember" class="mr-2 rounded">
                    Remember me
                </label>
                <a href="" class="text-sm text-cyan-400 hover:underline">Forgot password?</a>
            </div>

            <div>
                <button type="submit"
                    class="w-full bg-cyan-500 hover:bg-cyan-700 text-white font-semibold text-lg py-3 px-6 rounded-lg shadow-md hover:shadow-lg transition duration-300 ease-in-out cursor-pointer">
                    🔐 Log In
                </button>
            </div>

        </form>

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-400">
                New here?
                <a href="{{ route('register') }}" class="text-cyan-400 hover:underline">Create an account</a>
            </p>
        </div>
    </div>
</div>

@endsection
