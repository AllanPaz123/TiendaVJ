<section>
<h2>mensaje inyectado pue</h2>
<p>{{mensaje}}</p>  
 <table>
        <tr>
            <th>id</th>
            <th>nombre</th>
            <th>marca</th>
            <th>estado</th>
        </tr>
    </table>

    <ul>
        {{foreach carros}}
        
        <li>
            <strong>id:</strong> {{id}}<br>
            <strong>nombre:</strong> {{nombre}}<br>
            <strong>marca:</strong> {{marca}}<br>
            <strong>estado:</strong> {{estado}}
        </li>
        <li>
              {{id}} - {{nombre}} — {{marca}} — {{estado}}
        </li>

        {{endfor carros}}
    </ul>  
<section>
   