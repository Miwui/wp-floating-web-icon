# Aviso UMAG – v1.0.0

Plugin WordPress para fijar un ícono o imagen en cualquiera de las 4 esquinas de la pantalla.

## Instalación

1. Sube la carpeta `aviso-umag/` a `/wp-content/plugins/`.
2. Activa el plugin desde **Plugins → Plugins instalados**.
3. Ve a **Ajustes → Aviso UMAG** para configurarlo.

## Opciones disponibles (v1.0)

| Opción | Descripción |
|---|---|
| Estado | Activa o desactiva el aviso sin desinstalar el plugin |
| Posición | Esquina donde aparece (Superior/Inferior × Izquierda/Derecha) |
| Imagen | Sube o selecciona una imagen desde la Biblioteca de medios |
| Ancho | Ancho en píxeles (la altura es proporcional automáticamente) |
| Enlace | URL destino al hacer clic (opcional) |
| Nueva pestaña | Abre el enlace en `_blank` |
| Separación H/V | Distancia al borde horizontal y vertical en píxeles |

## Estructura de archivos

```
aviso-umag/
├── aviso-umag.php      ← Plugin principal
├── admin/
│   ├── admin.css       ← Estilos del panel
│   └── admin.js        ← Media uploader + corner picker
└── README.md
```

## Próximas mejoras sugeridas

- Animación de entrada (rebote, fade, deslizamiento)
- Programación horaria (mostrar solo en cierto rango de fechas/horas)
- Mensaje de texto emergente (tooltip o globo) al pasar el cursor
- Botón de cierre para que el usuario pueda ocultar el aviso
- Vista previa en tiempo real desde el panel de administración
