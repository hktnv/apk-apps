<x-layouts.admin title="Yönetici Girişi">
    <section class="card" style="max-width: 480px">
        <form method="post" action="{{ route('login.attempt') }}">
            @csrf
            <label for="email">E-posta</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>

            <label for="password">Parola</label>
            <input id="password" name="password" type="password" required>

            <label><input type="checkbox" name="remember" value="1" style="width: auto"> Beni hatırla</label>

            <button type="submit">Giriş yap</button>
        </form>
    </section>
</x-layouts.admin>
