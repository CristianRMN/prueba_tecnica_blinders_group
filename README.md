# Prueba Tecnica Blinders Group (Product Badges - Etiquetas para productos)

## Indice

1. [Stack utilizado](#1-stack-utilizado)
2. [Como instalar el modulo](#2-como-instalar-el-modulo)
3. [Decisiones tecnicas](#3-decisiones-tecnicas)
4. [Que se dejo fuera y por que](#4-que-se-dejo-fuera-y-por-que)

---

## 1. Stack utilizado

| Tecnologia | Version |
|---|---|
| PrestaShop | 1.7.8.11 |
| PHP | 7.4 |
| MySQL | 5.7 |
| Docker | Requerido para la instalacion |

---

## 2. Como instalar el modulo

El proyecto esta montado sobre Docker, por lo que la via recomendada es usarlo directamente. La otra opcion seria instalar PrestaShop en local, pero se desaconseja por la complejidad que implica.

### Via Docker (recomendada)

**Requisitos previos:** tener Docker y Docker Compose instalados.

1. Clonar el repositorio:

```bash
git clone https://github.com/CristianRMN/prueba_tecnica_blinders_group.git
```

2. Entrar al directorio del proyecto:

```bash
cd prueba_tecnica_blinders_group
```

3. Copiar el fichero de variables de entorno y rellenarlo con los datos que se consideren oportunos:

```bash
cp .env.example .env
```

Las variables a configurar en el `.env` son:

- `MYSQL_DATABASE` — nombre de la base de datos
- `MYSQL_ROOT_PASSWORD` — contraseña del usuario root de MySQL
- `MYSQL_USER` — usuario de MySQL
- `MYSQL_PASSWORD` — contraseña del usuario de MySQL
- `APP_PORT` — puerto en el que se expondra PrestaShop (por defecto 8085)
- `EMAIL_SHOP` — email del administrador de la tienda
- `EMAIL_PASSWORD` — contraseña del administrador de la tienda

4. Levantar los contenedores:

```bash
docker compose up -d
```

Si se quieren ver los logs en tiempo real, omitir el flag `-d`:

```bash
docker compose up
```

5. Acceder a `http://localhost:8085` (o el puerto configurado en `APP_PORT`). La primera vez, PrestaShop mostrara el asistente de instalacion. Hay que completar los pasos que pide: idioma, aceptar licencia, datos de la tienda, y en el paso de configuracion de base de datos introducir los siguientes datos:

   - **Servidor de base de datos:** `mysql` (es el nombre del servicio en docker-compose, no `localhost`)
   - **Nombre de la base de datos:** el valor que se haya puesto en `MYSQL_DATABASE`
   - **Usuario de la base de datos:** el valor de `MYSQL_USER`
   - **Contraseña de la base de datos:** el valor de `MYSQL_PASSWORD`

   El resto de campos del asistente (nombre de la tienda, email y contraseña del administrador, etc.) se rellenan con lo que se considere oportuno.

6. Una vez finalizada la instalacion, eliminar la carpeta `install/` del contenedor si PrestaShop lo solicita (esto es un requisito de seguridad de PrestaShop, no del modulo):

```bash
docker exec container_prestashop_blinders_group rm -rf /var/www/html/install
```

7. Acceder al back office e instalar el modulo desde **Modulos > Module Manager**, buscando "productbadges".

### Via instalacion local (no recomendada)

Tambien es posible instalar PrestaShop directamente en local sin Docker. Para ello habria que:

1. Instalar MySQL 5.7 y PHP 7.4 manualmente.
2. Descargar e instalar PrestaShop 1.7.8.11 siguiendo la documentacion oficial.
3. Durante el proceso de instalacion de PrestaShop, configurar los datos de conexion a la base de datos (host, nombre de BD, usuario y contraseña).
4. Una vez instalado, copiar la carpeta `modules/productbadges` del repositorio dentro de la carpeta `modules/` de la instalacion de PrestaShop.
5. Instalar el modulo desde el back office.

Esta via es considerablemente mas compleja porque requiere instalar y configurar cada componente por separado, por lo que se recomienda la via Docker.

---

## 3. Decisiones tecnicas

### Simplicidad y respeto al core

No se hizo sobreingenieria. Se opto por hacer el modulo lo mas simple posible, respetando el core de PrestaShop y sin realizar overrides sobre themes. Toda la funcionalidad se implementa mediante hooks, ObjectModel, HelperForm, HelperList y ModuleAdminController, siguiendo las APIs propias del framework.

### Posicionamiento de badges en el frontend

Despues de investigar los hooks disponibles en el tema classic de PrestaShop 1.7, no se encontro un hook que permitiese posicionar la etiqueta exactamente encima de la imagen del producto en todas las secciones. En concreto, para el listado por categorias y el home, el hook mas cercano a la zona de la imagen es `displayProductListReviews`, que se renderiza debajo de la imagen y no sobre ella.

Se podria intentar reposicionar la etiqueta sobre la imagen mediante CSS (position absolute, margin negativo, etc.), pero en responsive se veria mal y el resultado dependeria de la estructura del tema activo, lo cual no es fiable.

Para la ficha de producto si se encontro el hook `displayAfterProductThumbs`, que se renderiza dentro del contenedor de imagenes y permite un posicionamiento correcto sobre la imagen principal mediante CSS.

### Por que no se uso un child theme

Otra opcion para resolver el posicionamiento en listados habria sido crear un child theme del tema classic. En ese child se podria sobreescribir el template de la miniatura del producto (`catalog/_partials/miniatures/product.tpl`) e introducir las etiquetas directamente dentro del contenedor de la imagen, junto a las flags nativas. Sin embargo, se descarto esta opcion porque la prueba pide un modulo, no un tema, y se prefirio respetar esa directriz usando unicamente hooks.

### Tres tablas en lugar de dos

Para el soporte multilenguaje se crearon tres tablas:

- `ps_badge` — datos de la etiqueta que no dependen del idioma (colores, posicion, estado activo)
- `ps_badge_lang` — campo `name` traducible, con una fila por cada combinacion de badge e idioma
- `ps_product_badge` — tabla de relacion muchos a muchos entre productos y etiquetas

La tabla `ps_badge_lang` es necesaria porque asi funciona el sistema de ObjectModel multilang de PrestaShop: los campos traducibles se almacenan en una tabla `_lang` separada, indexada por `id_badge` e `id_lang`.

### Asignacion de badges a productos via AJAX

La asignacion y desasignacion de etiquetas a productos se realiza mediante llamadas AJAX desde la ficha de producto en el back office. Se eligio este enfoque porque la UI de asignacion se renderiza dentro de un hook del formulario de producto, que no participa en el submit principal del formulario. Los metodos `ajaxProcessAddBadges` y `ajaxProcessRemoveBadge` del AdminController procesan las peticiones y devuelven JSON que el JavaScript usa para repintar la UI sin recargar la pagina.

---

## 4. Que se dejo fuera y por que

- **Posicionamiento exacto sobre la imagen en listados**: como se explica en la seccion anterior, no existe un hook en el tema classic que lo permita sin recurrir a overrides de templates o child themes.
- **Tests unitarios**: no se incluyen tests por no ser un requisito obligatorio de la prueba. En caso de anadirlos, los candidatos principales serian la logica de asignacion/desasignacion de badges y la consulta de badges por producto.
