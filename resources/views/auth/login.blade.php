<x-guest-layout>
    <!-- Session Status -->
    @if (session('status'))
        <div class="neu-status success">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="neu-form-group">
            <label for="email">{{ __('Email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="your@email.com" />
            @error('email')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="neu-form-group">
            <label for="password">{{ __('Password') }}</label>
            <div style="position: relative;">
                <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" style="padding-right: 45px;" />
                <button type="button" onclick="togglePassword()" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; padding: 5px; color: #64748b; font-size: 18px;" title="Show/Hide Password">
                    <span id="toggleIcon">👁️</span>
                </button>
            </div>
            @error('password')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.textContent = '🔒';
            } else {
                passwordInput.type = 'password';
                toggleIcon.textContent = '👁️';
            }
        }
        </script>

        <!-- Remember Me -->
        <div class="neu-checkbox">
            <input id="remember_me" type="checkbox" name="remember">
            <label for="remember_me">{{ __('Remember me') }}</label>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="neu-button">
            {{ __('Log in') }}
        </button>

        <!-- Forgot Password Link -->
        @if (Route::has('password.request'))
            <div class="neu-divider">
                <span>{{ __('or') }}</span>
            </div>
            <div style="text-align: center;">
                <a href="{{ route('password.request') }}" class="neu-link">
                    {{ __('Forgot your password?') }}
                </a>
            </div>
        @endif
    </form>
</x-guest-layout>
