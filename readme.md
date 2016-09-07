# SUNAT-PHP
   
## Instalación

Instalar usando composer:

```bash
   composer require tecactus/sunat-php
```

O agregar la siguiente línea a tu archivo composer.json:

```json
   "require": {
       ...
       "tecactus/sunat-php": "1.*"
       ...
   }
```

## Uso

### Para consultar datos RUC

```php
   // incluir el autoloader de vendor
   require 'vendor/autoload.php';

   // crear un nuevo objeto de la clase RUC
   $sunatRuc = new Tecactus\Sunat\RUC('tu-token-de-acceso-personal');
   
   // para consultar los datos usando el número de RUC
   print_r( $sunatRuc->getByRuc('12345678901') );

   // para consultar los datos usando el númer de DNI
   print_r( $sunatRuc->getByDni('12345678') );
   
   // para devolver el resultado como un array pasar 'true' como segundo argumento.
   print_r( $sunatRuc->getByRuc('12345678901', true) );
```

### Para consultar Tipo de Cambio

```php
   // incluir el autoloader de vendor
   require 'vendor/autoload.php';

   // crear un nuevo objeto de la clase ExchangeRate
   $sunatTipoCambio = new Tecactus\Sunat\ExchangeRate('tu-token-de-acceso-personal');
   
   // para consultar los tipos de cambio de un mes por ejemplo:
   // Enero del 2016
   print_r( $sunatTipoCambio->get(2016, 1) );
   
   // para consultar los tipos de cambio de un día en específico por ejemplo:
   // Enero 13 del 2016
   print_r( $sunatTipoCambio->get(2016, 1, 13) );
   
   // Hay días en donde no se establece un tipo de cambio en particular
   // en ese caso la SUNAT especifica el uso del tipo de cambio del
   // día anterior, por ejemplo:

   // El día Enero 10 de 2016 nos devuelve que no hay resultados:
   print_r( $sunatTipoCambio->get(2016, 1, 10) );  // retorna un mensaje que no se encontraron datos para ese día.

   // Pero podemos obtener el resultado el día anterior más cercado pasando 'true'
   // como cuarto argumento
   print_r( $sunatTipoCambio->get(2016, 1, 10, true) ); // esto nos devuelve el tipo de cambio del día 9 ya que el 10 no existe.

   
   // para devolver el resultado como un array pasar 'true' como quinto argumento.
   print_r( $sunatTipoCambio->get(2016, 1, null, false, true) );
   
```

## Token de Acceso Personal

Para crear tokens de acceso personal debes de iniciar sesión en Tecactus:

[https://tecactus.com/auth/login](https://tecactus.com/auth/login)

Si no estas registrado aún, puedes hacerlo en:

[https://tecactus.com/auth/register](https://tecactus.com/auth/register)

Debes de activar tu cuenta si aún no lo has hecho.
Luego ver el panel de gestión de Tokens de acceso en:

[https://tecactus.com/developers/configuracion/tokens](https://tecactus.com/developers/configuracion/tokens)