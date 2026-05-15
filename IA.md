# Uso de IA en este proyecto

## 1. Herramientas utilizadas

| Herramienta | Version / Modelo | Modo de uso | Aprox. % del trabajo |
|---|---|---|---|
| Claude Code | Opus 4.6 (1M context) | Terminal integrada en VS Code | 70% |
| Ninguna | — | Yo mismo, sin IA | 30% |

## 2. Configuracion del proyecto

### CLAUDE.md / AGENTS.md

Si, se utilizo un archivo CLAUDE.md a nivel proyecto. Se puede consultar en la raiz del repositorio: [`CLAUDE.md`](CLAUDE.md).

Su contenido:

```
# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

PrestaShop 1.7.8.11 module ("Product Badges") that lets administrators create styled badges and assign them to products. Technical test for Blinders Group. Author: Cristian Regueiro Martínez.

## Development Environment

# Start PrestaShop + MySQL via Docker
docker-compose up -d

# PrestaShop accessible at http://localhost:8085 (configurable via APP_PORT in .env)
# MySQL on port 3307

The module directory `./productbadges` is volume-mounted into the container at `/var/www/html/modules/productbadges`. Changes to module files are reflected immediately.

No build step, test framework, or linter is configured. Testing is manual through the PrestaShop admin panel.

## Architecture

This is a standard PrestaShop 1.7 module following the ObjectModel + AdminController + Hook pattern:

- `productbadges/productbadges.php` — Module entry point. Handles install/uninstall (DB tables, hooks, admin tab). Registers two hooks:
  - `displayAdminProductsMainStepRightColumnBottom` — renders badge assignment UI on the product edit page
  - `actionProductSave` — intended to persist product-badge associations (currently empty)

- `productbadges/classes/ProductBadges.php` — `Badge` ObjectModel mapped to `ps_badge` table. Fields: name, background_color, text_color, position (left/right), active.

- `productbadges/controllers/admin/AdminProductBadgesController.php` — CRUD admin controller for badge management (list + form views).

- `productbadges/sql/Install.php / Uninstall.php` — Raw SQL for creating/dropping `ps_badge` and `ps_product_badge` (junction table linking products to badges).

- `productbadges/views/templates/admin/product_badges.tpl` — Smarty template for the badge assignment widget on the product edit page. UI is in Spanish.

## Known Issues

- Template (`product_badges.tpl`) references `$badge.color_bg` and `$badge.color_text` but the ObjectModel defines `background_color` and `text_color` — variable name mismatch.
- `hookActionProductSave()` is empty — product-badge association saving is not yet implemented.
- No client-side JS files exist yet for the badge add/remove UI interactions in the template.
```

### settings.json u otra configuracion equivalente

No se modificaron permisos, herramientas habilitadas/bloqueadas ni configuracion especial. El modelo utilizado fue Claude Opus 4.6 con 1M de contexto, que es el que viene por defecto. Los permisos se mantuvieron en su configuracion predeterminada.

## 3. Skills personalizadas

Ninguna.

## 4. Slash commands personalizados

Ninguno.

## 5. Sub-agentes invocados

No se utilizo Task tool, Plan Mode ni sub-agentes de forma explicita. El trabajo se realizo mediante conversacion directa con Claude Code en la terminal.

## 6. MCPs (Model Context Protocol)

No se conecto ningun MCP server durante el desarrollo. No se habria utilizado de todos modos porque el proyecto no lo requeria; no habia que realizar operaciones complejas que lo justificasen. Para entender los requisitos del proyecto bastaba con leer los ficheros locales directamente, algo que Claude Code ya hace de forma nativa sin necesidad de un MCP adicional.

## 7. Prompts importantes

> **Nota:** Se piden entre 5 y 10 prompts. Incluyo 4 porque han sido realmente los que dieron forma al proyecto. Podria añadir alguno mas de relleno, pero seria forzar la lista. La columna vertebral del trabajo con IA fueron estos 4 prompts; el resto de interacciones fueron correcciones menores o consultas puntuales que no merecen entrada propia.

### Prompt 1 — Asignacion de badges a productos via AJAX

- **Herramienta:** Claude Code
- **Prompt:** "Para esa parte de se asignan productos a etiquetas, tal y como lo tenemos en el tpl correspondiente, hay que hacer Ajax con JS. Fijate en donde debería estar el fichero JS segun lo que se pide en el enunciado pero creo que lo correcto sería seguir estos pasos tanto para actualizar, añadir y borrar etiquetas de productos. Para añadir productos si te fijas, las etiquetas disponibles van a aparecer en la div add-badges-section, dentro del TPL. Como estan dentro de un select multiple, puedes seleccionar una o varias. Cuando le des al boton add-badges, tendriamos que recoger el id de todos los badges y del producto. por ajax mandar esa informacion a un controller, ese controller recibe la informacion, guarda en BD y devuelve un JSON de respuesta que luego se pinta en la UI."
- **Que genero (resumen):** Genero el fichero `views/js/product_badges.js` con las funciones de AJAX para añadir y eliminar badges, los metodos `ajaxProcessAddBadges` y `ajaxProcessRemoveBadge` en el `AdminProductBadgesController`, la variable global con la URL del controller en el TPL, y la carga del JS desde el hook del modulo.
- **Que hice con el output:** Revise todo el codigo y lo acepte, pero al probar en el navegador el JS no se cargaba. El problema era que la IA uso `$this->context->controller->addJS()` dentro del hook, que en PrestaShop 1.7 se ejecuta demasiado tarde cuando el hook devuelve HTML inyectado. Tuve que modificarlo para cargar el JS directamente con una etiqueta `<script src="...">` en el TPL en lugar de usar `addJS()`.

### Prompt 2 — Busqueda de hooks para mostrar badges en el frontend

- **Herramienta:** Claude Code
- **Prompt:** "Mostrar las badges en frontend sobre la imagen del producto en: Listado de categoria, Resultados de busqueda y home (si el tema activo las soporta), Ficha del producto. Aqui, hay que debatir. Porque tengo claro las 2 vias para hacer esto. Primera, registro de hooks. No se si existe algun hook para poder registrar los badge en el listado de categoria en el front, resultado de motor de busqueda y home y en la ficha del producto. Puede que si pero no lo se."
- **Que genero (resumen):** Investigo los templates del tema classic de PrestaShop 1.7 dentro del contenedor Docker, identificando todos los hooks disponibles en la miniatura del producto y en la ficha de producto. Concluyo que `displayAfterProductThumbs` sirve para la ficha de producto (dentro del contenedor de imagenes) y `displayProductListReviews` es el hook mas cercano a la imagen en listados, aunque no esta exactamente sobre ella.
- **Que hice con el output:** Acepte el analisis y la conclusion. Inicialmente la IA propuso un enfoque mixto (hooks + template overrides), pero le indique que no se pueden hacer overrides de un tema desde un modulo en PrestaShop y que debia ser solo por hooks. Corrigio el planteamiento y se implemento unicamente con hooks.

### Prompt 3 — Pantalla de configuracion del modulo

- **Herramienta:** Claude Code
- **Prompt:** "Vamos con la siguiente parte: Pantalla de configuracion del modulo con: Activar/desactivar global, Mostrar en listados (si/no), Mostrar en ficha de producto (si/no), Numero maximo de badges visibles por producto. Aqui hay que hacer una pantalla de configuracion. Para ello, tendria que mirarse una UI al darle a configurar dicho modulo en el module manager. No se si prestashop nos da algun objeto tipo Helper algo para hacer eso."
- **Que genero (resumen):** Implemento el metodo `getContent()` con `HelperForm` para renderizar el formulario de configuracion con los 4 campos solicitados (switches para activar/desactivar global, listados y ficha, e input numerico para maximo de badges). Registro los valores con `Configuration::updateValue()` en el install y `Configuration::deleteByName()` en el uninstall. Aplico las comprobaciones de configuracion en los hooks del frontend.
- **Que hice con el output:** Lo acepte tal cual. El codigo generado seguia correctamente el patron estandar de PrestaShop para pantallas de configuracion de modulos.

### Prompt 4 — Soporte multilenguaje

- **Herramienta:** Claude Code
- **Prompt:** "Multilenguaje. No lo hice nunca y no se como se hace. Es decir, en la carpeta translations has de tocar algo? O como se hace para que sea multilenguaje?"
- **Que genero (resumen):** Explico paso a paso el sistema de ObjectModel multilang de PrestaShop: crear la tabla `ps_badge_lang`, mover el campo `name` a dicha tabla, marcar `'multilang' => true` en la definicion del ObjectModel, poner `'lang' => true` en el campo y en el controller, y actualizar todas las consultas SQL manuales para hacer JOIN con `badge_lang` filtrando por `id_lang`. Implemento todos los cambios.
- **Que hice con el output:** Revise y acepte los cambios, pero al probar la creacion de un badge daba error indicando que el campo `name` era requerido. Detecte que faltaba añadir `'lang' => true` en la definicion del campo `name` dentro del `renderForm()` del controller. La IA habia actualizado el ObjectModel y la tabla pero se olvido de ese detalle en el formulario. Lo corregi.

## 8. Errores de la IA que detecte

### Error 1 — Proponer template overrides desde el modulo

- **Que genero la IA (mal):** Al implementar la visualizacion de badges en el frontend, la IA propuso un enfoque mixto que incluia hacer overrides de templates del tema classic directamente desde el modulo.
- **Por que estaba mal:** En PrestaShop 1.7, un modulo no debe hacer override de templates de un tema. Es una mala practica porque acopla el modulo a un tema concreto, rompe la portabilidad y puede generar conflictos con otros modulos o actualizaciones del tema. La via correcta es usar exclusivamente hooks.
- **Como lo corregi:** Le indique que el enfoque era incorrecto y que debia implementarse unicamente mediante hooks registrados. La IA corrigio el planteamiento y se implemento solo con hooks.

### Error 2 — Inyeccion incorrecta del JS en el hook

- **Que genero la IA (mal):** Para cargar el fichero `product_badges.js` en la pagina de edicion de producto, la IA uso `$this->context->controller->addJS()` dentro del hook `displayAdminProductsMainStepRightColumnBottom`.
- **Por que estaba mal:** En PrestaShop 1.7, cuando un hook devuelve HTML inyectado, el metodo `addJS()` se ejecuta demasiado tarde en el ciclo de vida de la pagina y el JS no se carga. El asset simplemente no aparecia en el navegador.
- **Como lo corregi:** Sustituí la llamada a `addJS()` por una etiqueta `<script src="...">` directamente en el template TPL, asegurando que el JS se cargase junto con el contenido del hook.

### Error 3 — Olvidar `'lang' => true` en el renderForm del controller

- **Que genero la IA (mal):** Al implementar el soporte multilenguaje, la IA actualizo correctamente el ObjectModel (tabla `ps_badge_lang`, campo con `'multilang' => true`) y las consultas SQL, pero se olvido de marcar el campo `name` con `'lang' => true` en la definicion del `renderForm()` del `AdminProductBadgesController`.
- **Por que estaba mal:** Sin esa propiedad, el HelperForm renderiza un input de texto simple en lugar del widget multilenguaje con pestañas por idioma. Al guardar, PrestaShop esperaba un array de valores por idioma y daba error de campo requerido.
- **Como lo corregi:** Añadi `'lang' => true` en la definicion del campo `name` dentro del array de campos del `renderForm()`.

## 9. Partes que NO use IA

- **Instalacion de PrestaShop y MySQL con Docker:** Configure el `docker-compose.yml` y el entorno de desarrollo completo sin IA. Es una tarea de infraestructura que no requiere escribir mucho codigo y que domino suficientemente como para hacerla rapido sin asistencia.

- **Fichero inicial del modulo con install/uninstall:** Cree el archivo principal `productbadges.php` con la logica de instalacion y desinstalacion (creacion y eliminacion de tablas `ps_badge` y `ps_product_badge`, registro de hooks y pestaña admin) sin IA. Sabia exactamente que tablas necesitaba y que hooks registrar.

- **CRUD de badges con AdminController:** Implemente el `AdminProductBadgesController` completo con `HelperList` y `HelperForm` sin IA. Es el patron estandar de PrestaShop para controllers de back office y no habia mucho codigo que escribir.

**Por que decidi no usar IA en estas partes:** Eran tareas que podia resolver relativamente rapido por mi cuenta. No requerian investigacion ni codigo complejo. Mas adelante, cuando llegue a secciones que desconocia o que requerían mas codigo (AJAX, hooks de frontend, multilenguaje, configuracion del modulo), decidi incorporar Claude Code para acelerar el proceso, consultar como funcionaban ciertas partes de PrestaShop y entender por que se hacian de determinada manera.

## 10. Reflexion final

### Que me ahorro la IA

La IA me ahorro mucho tiempo, investigacion y trabajo de codificacion manual. En varias secciones ya sabia lo que tenia que hacer conceptualmente, pero la IA hizo que lo que me habria llevado unas 2 horas lo pudiese completar en 20 minutos. Es un acelerador: no sustituye el criterio tecnico, pero reduce drasticamente el tiempo de implementacion cuando sabes lo que quieres conseguir.

### En que me entorpecio o me llevo por mal camino

En algunos resultados como los mencionados en la seccion 8: proponer template overrides en lugar de hooks, inyectar JS de forma incorrecta, u olvidar configuraciones necesarias para el multilenguaje. Tuve que corregir estos errores a mano y, en algun caso, explicarle a la IA por que ciertas practicas eran incorrectas o no se ajustaban a lo que pedia la prueba. No fue un problema grave, pero si requirio estar atento y validar todo lo que generaba.

### Que cambiaria de mi flujo con IA si lo repitiera

Personalmente no cambiaria mucho. El ejercicio no es lo suficientemente complejo como para que varíe significativamente el enfoque. Podria haber usado skills, Spec-Driven Development, MCPs u otras herramientas avanzadas del ecosistema de Claude Code, pero no era necesario. Con indicarle lo que tenia que hacer y corregirle cuando se equivocaba, el flujo funcionaba bien. A veces lo simple es lo mas efectivo.
