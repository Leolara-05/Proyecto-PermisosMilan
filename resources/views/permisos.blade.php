<form method="POST" action="{{ url('/autorizar-permiso') }}">
    @csrf
    <label for="nombre">Nombre:</label>
    <input type="text" name="nombre" id="nombre" placeholder="Ingrese su nombre" required>

    <button type="submit" class="btn btn-primary">Autorizar Permiso</button>
</form>
