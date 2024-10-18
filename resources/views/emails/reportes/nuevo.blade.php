<x-mail::message>
Estás recibiendo este correo porque se ha generado un nuevo reporte en el sistema. 

# Categoría: {{ $post->subcategory->category->name }}
## Subcategoría: {{ $post->subcategory->name }}

<hr>

## Regiones afectadas: 
<ul>
@foreach($regionInfo as $region)
    <li>{{$region}}</li>
@endforeach
</ul>

<x-mail::button :url="route('institution.index')">
Mis instituciones
</x-mail::button>

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>