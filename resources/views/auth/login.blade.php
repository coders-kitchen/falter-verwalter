<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Falter Verwalter</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-base-200">
    <div class="min-h-screen flex items-center justify-center">
        <div class="card w-full max-w-sm shadow-xl bg-base-100">
            <div class="card-body">
                <h2 class="card-title mb-6">🦋 Admin Login</h2>

                @if($errors->any())
                    <div class="alert alert-error mb-4">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
                    @csrf

                    <!-- Email Input -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">E-Mail</span>
                        </label>
                        <input
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            placeholder="admin@test.de"
                            class="input input-bordered @error('email') input-error @enderror"
                            required
                        />
                        @error('email')
                            <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Password Input -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Passwort</span>
                        </label>
                        <input
                            name="password"
                            type="password"
                            placeholder="••••••••"
                            class="input input-bordered @error('password') input-error @enderror"
                            required
                        />
                        @error('password')
                            <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary w-full">
                        Anmelden
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
