<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    <h1>Hello</h1>
    
    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
    @csrf
    <button type="submit" 
        style="
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s;
        "
        onmouseover="this.style.backgroundColor='#c82333'"
        onmouseout="this.style.backgroundColor='#dc3545'">
        Logout
    </button>
</form>


</body>

</html>
