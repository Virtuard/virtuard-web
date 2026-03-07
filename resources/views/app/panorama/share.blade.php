<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Virtuard</title>
    <link href="{{ asset('libs/font-awesome/css/font-awesome.css') }}" rel="stylesheet">
    @include('partials.ipanorama.ipanorama-css')
    <style>
        #mypanorama {
            width: 100%;
            height: 100vh;
        }

        .back-btn {
            position: fixed;
            width: 25px;
            height: 25px;
            bottom: 35px;
            left: 5px;
            background-color: #fff;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        .back-btn i {
            font-size: 20px;
            color: #000;
        }
    </style>
</head>

<body>
    @include('partials.ipanorama.ipanorama-init')

    @include('partials.ipanorama.ipanorama-js')
    @include('partials.ipanorama.ipanorama-preview-js')

    @if ($listingUrl)
        <a class="back-btn" href="{{ $listingUrl }}">
            <i class="fa fa-arrow-left"></i>
        </a>
    @endif
</body>

</html>
