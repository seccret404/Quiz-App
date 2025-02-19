<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        .loader {
        width: 50px;
        padding: 8px;
        aspect-ratio: 1;
        border-radius: 50%;
        background: #25b09b;
        --_m:
            conic-gradient(#0000 10%,#000),
            linear-gradient(#000 0 0) content-box;
        -webkit-mask: var(--_m);
                mask: var(--_m);
        -webkit-mask-composite: source-out;
                mask-composite: subtract;
        animation: l3 1s infinite linear;
        }
        @keyframes l3 {to{transform: rotate(1turn)}}
    </style>
</head>
<body>
    <div
    id="loader"
    class="hidden fixed inset-0 z-50 flex items-center justify-center bg-[#36333364] bg-opacity-50">

        <div class="loader"></div>

    </div>

</body>
</html>
