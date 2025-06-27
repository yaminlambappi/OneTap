@extends('layouts.base')

@section('title', 'Register - OneTap')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-900 px-4">
    <div class="w-full max-w-md bg-gray-800 text-gray-100 rounded-xl shadow-xl p-8 space-y-6">
        <h2 class="text-2xl font-bold text-center text-white">🎯 OneTap – Create Account</h2>

        @if ($errors->any())
            <div class="bg-red-600/20 border border-red-600 text-red-300 p-3 rounded-md text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register.post') }}" class="space-y-5">
            @csrf

            <div>
                <label for="username" class="block text-sm font-medium text-gray-300">Username</label>
                <input id="username" type="text" name="username" required autofocus
                    class="w-full px-4 py-2 mt-1 bg-gray-700 border border-gray-600 rounded-lg placeholder-gray-400 text-white focus:ring-2 focus:ring-cyan-500 focus:outline-none transition">
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-300">Phone Number</label>
                <input id="phone" type="text" name="phone" required
                    class="w-full px-4 py-2 mt-1 bg-gray-700 border border-gray-600 rounded-lg placeholder-gray-400 text-white focus:ring-2 focus:ring-cyan-500 focus:outline-none transition">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-300">Password</label>
                <input id="password" type="password" name="password" required
                    class="w-full px-4 py-2 mt-1 bg-gray-700 border border-gray-600 rounded-lg placeholder-gray-400 text-white focus:ring-2 focus:ring-cyan-500 focus:outline-none transition">
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-300">Confirm Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                    class="w-full px-4 py-2 mt-1 bg-gray-700 border border-gray-600 rounded-lg placeholder-gray-400 text-white focus:ring-2 focus:ring-cyan-500 focus:outline-none transition">
            </div>

            <div>
                <button type="submit"
                    class="w-full bg-cyan-500 hover:bg-cyan-600 text-white font-bold py-2 px-4 rounded-lg transition-all duration-300">
                    🚀 Register
                </button>
            </div>
        </form>

        <p class="text-center text-sm text-gray-400">
            Already have an account?
            <a href="{{ route('login') }}" class="text-cyan-400 hover:underline">Log in</a>
        </p>
    </div>
</div>
@endsection
