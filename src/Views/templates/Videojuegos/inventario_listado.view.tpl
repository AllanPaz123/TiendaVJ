<section class="depth-2 px-2 py-2">
  <h2>Mantenimiento de Inventario</h2>
</section>

<section class="WWList my-5">
  <form method="get" action="index.php">
    <input type="hidden" name="page" value="Videojuegos_inventariolist"/>
    <label for="videojuegocod">Filtrar por videojuego:</label>
    <select name="videojuegocod" id="videojuegocod" onchange="this.form.submit()">
      <option value="0">-- Todos --</option>
      {{foreach videojuegos}}
        <option value="{{videojuegocod}}" {{if ../filtro_videojuegocod == videojuegocod}}selected{{endif}}>{{titulo}}</option>
      {{endfor videojuegos}}
    </select>
  </form>
  <br/>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Título</th>
        <th>Formato</th>
        <th>Precio</th>
        <th>Stock</th>
        <th>Ubicación</th>
        <th>
          <a href="index.php?page=Videojuegos_inventarioform&mode=INS">
            Nuevo
          </a>
        </th>
      </tr>
    </thead>
    <tbody>
      {{foreach inventario}}
      <tr>
        <td>{{inventarioid}}</td>
        <td>{{titulo}}</td>
        <td>{{formato}}</td>
        <td>{{precio}}</td>
        <td>{{stock}}</td>
        <td>{{ubicacion}}</td>
        <td>
          <a href="index.php?page=Videojuegos_inventarioform&mode=UPD&inventarioid={{inventarioid}}">Editar</a> |
          <a href="index.php?page=Videojuegos_inventarioform&mode=DEL&inventarioid={{inventarioid}}">Eliminar</a>
        </td>
      </tr>
      {{endfor inventario}}
    </tbody>
  </table>
</section>
