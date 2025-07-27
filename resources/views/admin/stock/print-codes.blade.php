<!DOCTYPE html>
<html>
<head>
    <style>
        @media print {
            body { margin: 0; }
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            font-size: 18px;
            text-align: center;
            margin: 30px;
        }
        .code-box {
            border: 1px dashed #000;
            padding: 20px;
        }
    </style>
</head>
<body onload="window.print()">
    <div class="grid">
        @foreach($codes as $code)
            <div class="code-box">{{ $code->code }}</div>
        @endforeach
    </div>
</body>
</html>