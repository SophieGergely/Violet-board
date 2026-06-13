<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Prihlásenie zákazníka</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
</head>



<script src="{{ asset('js/kontrola.js') }}"></script>
<body>

    <nav class="navbar navbar-light fixed-top py-2 header">
        <div class="container">
            <a class="navbar-brand" href="#"><img src="Pictures/logo.jpg" alt="Logo" class="logo"></a>
            <span class="mx-auto fs-4">Prihlásenie zákazníka</span>
            <button onclick="window.location.href='/'" class="btn btn-outline-dark">🏠</button>
        </div>
    </nav>

    <div class="container form-container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="bg-white p-4 rounded shadow">

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Zadajte váš email" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Heslo</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Zadajte vaše heslo" required>
                        </div>

                        <button type="submit" class="btn btn-dark w-100">Prihlásiť sa</button>
                    </form>

                    <div class="text-center mt-3">Ešte nemáš účet? <a href="/registracia" class="text-decoration-none">Zaregistrovať sa</a></div>
                </div>
            </div>
        </div>
    </div>

    @include('partials.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
