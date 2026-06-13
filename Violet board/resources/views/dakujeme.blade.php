<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ďakujeme za Vás nákup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
</head>


<body>
    <nav class="navbar navbar-light fixed-top py-2 header">
        <div class="container d-flex flex-wrap">
            <div class="col-2 d-flex justify-content-start">
                <a class="navbar-brand" href="#"><img src="Pictures/logo.jpg" alt="Logo" class="logo"></a>
            </div>
            <div class="col-8 d-flex justify-content-start">
                <span class="fs-4">{{ $message }}</span>
            </div>
        </div>
    </nav>
    <div class="container form-container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="bg-white p-4 rounded shadow">
                    <form>
                        <button type="button" onclick="window.location.href='/'" class="btn btn-dark w-100">Prejsť na domovskú stránku</button>
                        <button type="button" onclick="window.location.href='/shop'" class="btn btn-dark w-100 btn-spacing">Prejsť na zoznam produktov</button>
                    </form>
                </div>
            </div>
        </div>

        @include('partials.footer')

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
