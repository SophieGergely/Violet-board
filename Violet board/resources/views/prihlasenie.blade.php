<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Prihlásenie zákazníka</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
</head>

<body>
    <nav class="navbar navbar-light fixed-top py-2 header">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="/" class="navbar-home-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                </svg>
            </a>
            <span class="fw-semibold fs-5" style="color:#ffffff">Prihlásenie zákazníka</span>
            <div style="width:38px"></div>
        </div>
    </nav>

    {{-- Centered vertically between navbar and footer --}}
    <div class="container" style="min-height: calc(100vh - var(--navbar-height) - var(--footer-height)); display: flex; align-items: center; justify-content: center; padding: 32px 16px;">
        <div class="col-md-5">
            <div class="bg-white rounded-xl shadow-lg p-5" style="border:1px solid var(--color-border);">
                <h2 class="text-center fw-semibold mb-4" style="color:var(--color-primary);font-size:1.75rem">Vitajte späť!</h2>

                @if ($errors->any())
                    <div class="alert alert-danger rounded-lg mb-3">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label fw-medium">Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                            placeholder="vas@email.com" required>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label fw-medium">Heslo</label>
                        <input type="password" class="form-control" id="password" name="password"
                            placeholder="••••••••" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Prihlásiť sa</button>
                </form>

                <p class="text-center mt-3 text-muted small">
                    Ešte nemáte účet?
                    <a href="/registracia" style="color:var(--color-primary)">Zaregistrovať sa</a>
                </p>

                <hr class="my-4">

                <a href="{{ url('/') }}" class="btn w-100" style="background:var(--color-primary-light);color:var(--color-primary);border-radius:var(--radius-full);font-weight:500;">
                    Nakupovať ako hosť
                </a>
            </div>
        </div>
    </div>

    @include('partials.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <script src="{{ asset('js/kontrola.js') }}"></script>
</body>
</html>
