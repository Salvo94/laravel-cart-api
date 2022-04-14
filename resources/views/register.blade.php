<hmtl>
    <head>
        <title>Test register</title>
    </head>
    <body>
        <form method="Post" action="/register">
            @csrf
            <input type="text" name="name" placeholder="name">
            <input type="email" name="email" placeholder="email">
            <input type="password" name="password" placeholder="password">
            <input type="password" name="password_confirmation" placeholder="password confirmation">
            <input type="submit">
        </form>
    </body>
</hmtl>