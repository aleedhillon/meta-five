

## Meta Five

Esta es una biblioteca envoltura (wrapper) de paquetes para Laravel 8.x + para la API Web de Metatrader 5

- [Documentación oficial de la API Web MT5](https://support.metaquotes.net/en/docs/mt5/api/webapi).

## Solución CRM lista para usar para MetaTrader 5

¿Buscas una solución CRM integral y lista para usar para la plataforma MetaTrader 5? ¡No busques más! Ofrecemos un sistema CRM MT5 robusto diseñado para gestionar eficientemente los datos, interacciones y servicios de los clientes.

### Características del CRM MT5:

- **Incorporación de Clientes:** Optimiza el proceso de registro y gestión de nuevos clientes.
- **Seguimiento de Comunicaciones:** Mantén registros detallados de las comunicaciones con los clientes para mejorar el servicio y la satisfacción.
- **Herramientas de Informes:** Genera informes detallados para aumentar la productividad y la satisfacción del cliente.
- **Transferencias Inter-cuentas:** Facilita transferencias sin interrupciones entre cuentas.
- **Pasarelas de Pago Manuales y Automáticas:** Gestiona depósitos y retiros de las cuentas comerciales de MetaTrader sin esfuerzo.
- **Recargas y Bonificaciones:** Gestiona fácilmente recargas y bonificaciones para tus clientes.
- **Gestión del Sitio Web Público:** Personaliza y gestiona tu sitio web público a través del panel de administración.
- **Web Trader:** Ofrece una plataforma de trading basada en la web para tus clientes.
- **Características de Corredor Introductor (IB):** Implementa diferentes niveles de comisión para corredores introductores.
- **Gestión de Cuentas de Usuario y Comerciales:** Herramientas integrales para gestionar todas las cuentas de usuario y comerciales.

Nuestro CRM MT5 está diseñado para mejorar la eficiencia de tu correduría y las relaciones con los clientes. Si estás interesado en una solución CRM integral, podemos proporcionar una demostración al contactarnos.

### Información de Contacto:

- **Correo electrónico:** [aleedhx@gmail.com]

¡Para más detalles o para comprar el CRM listo para usar, contáctanos!

## Documentación

### Packagist

[https://packagist.org/packages/aleedhillon/meta-five](https://packagist.org/packages/aleedhillon/meta-five).

### Instalación

Para instalar el paquete, en la terminal:

```
composer require aleedhillon/meta-five
```

### Configuración

Si no utilizas la auto-detección (auto-discovery), agrega el ServiceProvider al array de providers en config/app.php

```
AleeDhillon\MetaFive\MetaFiveProvider::class,
```

#### Copia la configuración del paquete a tu configuración local con el comando publish:

```bash
php artisan vendor:publish --tag=meta-five-config
```

y luego puedes configurar la información de conexión a MT5 con este valor en `.env`

```dotenv
MT5_SERVER_IP=
MT5_SERVER_PORT=
MT5_SERVER_WEB_LOGIN=
MT5_SERVER_WEB_PASSWORD=
```

## Uso

### Crear Depósito

Puedes retirar dinero proporcionando un número negativo al mismo método `trade`.

```php
use AleeDhillon\MetaFive\Entities\Trade;
use AleeDhillon\MetaFive\Facades\Client;

$trade = new Trade();
$trade->setLogin(6000189);
$trade->setAmount(100);
$trade->setComment("Deposit");
$trade->setType(Trade::DEAL_BALANCE);
$result = Client::trade($trade);
```

La variable de resultado devolverá la clase Trade con la información del ticket, puedes obtener el número de ticket llamando a `$result->getTicket()`

### Crear Usuario

```php
use AleeDhillon\MetaFive\Entities\User;
use AleeDhillon\MetaFive\Facades\Client;

$user = new User();
$user->setName("John Doe");
$user->setEmail("johndoe@example.com");
$user->setGroup("demo\demoforex");
$user->setLeverage("50");
$user->setPhone("0123456789");
$user->setAddress("Lahore");
$user->setCity("Lahore");
$user->setState("Punjab");
$user->setCountry("Pakistan");
$user->setZipCode(1470);
$user->setMainPassword("secret");
$user->setInvestorPassword("secret");
$user->setPhonePassword("secret");

$result = Client::createUser($user);
```

### Obtener Información de la Cuenta Comercial

```php
use AleeDhillon\MetaFive\Facades\Client;

$user = Client::getTradingAccounts($login);

$balance = $user->Balance;
$equity = $user->Equity;
$freeMargin = $user->MarginFree;
```

### Obtener Historial de Comercio por Número de Login

```php
use AleeDhillon\MetaFive\Facades\Client;

// Get Closed Order Total and pagination
$total = Client::getOrderHistoryTotal($exampleLogin, $timestampfrom, $timestampto);
$trades = Client::getOrderHistoryPagination($exampleLogin, $timestampfrom, $timestampto, 0, $total);
foreach ($trades as $trade) {
    // see class MTOrder
    echo "LOGIN : ".$trade->Login.PHP_EOL;
    echo "TICKET : ".$trade->Order.PHP_EOL;
}
```

### Abrir Orden

```php
use AleeDhillon\MetaFive\Facades\Client;
Client::dealerSend([
    'Login' => 8113,
    'Symbol' => 'XAUUSD',
    'Volume' => 100,
    'Type' => 0
]);
```

La variable de resultado devolverá la clase User con la información de login, puedes obtener el número de login llamando a `$result->getLogin()`

### Tareas Pendientes (Todo)

- [x] Depósito o Retiro
- [x] Crear Cuenta
- [x] Abrir Orden
- [x] Obtener Información de la Cuenta Comercial
- [ ] Cambiar Contraseña
- [ ] Crear Grupo
- [ ] Eliminar Grupo
- [ ] Obtener Cuentas
- [ ] Eliminar Cuenta
- [ ] Obtener Operaciones
- [ ] Obtener Grupo

Esto es un trabajo en progreso, es posible que mejore este paquete o reescriba todo él con soporte para Laravel 9 y PHP 8.
En esta revisión no he modificado mucho el núcleo, en la próxima intento reescribir el núcleo.

## Créditos

Gracias a [Tarikh Agustia](https://github.com/tarikhagustia) quien escribió los siguientes dos paquetes, a partir de los cuales he reescrito este paquete actual con mejoras como el patrón singleton de Laravel para mayor velocidad y reducir las llamadas a la API de MT5.

- [https://github.com/tarikhagustia/php-mt5](https://github.com/tarikhagustia/php-mt5)
- [https://github.com/tarikhagustia/laravel-mt5](https://github.com/tarikhagustia/laravel-mt5)

## Contribución

¡Gracias por considerar contribuir a MetaFive! Puedes hacer fork de este repositorio y enviar un pull request.

## Vulnerabilidades de Seguridad

Si descubres una vulnerabilidad de seguridad en MetaFive, por favor envía un correo electrónico a Ali A. Dhillon a través de [aleedhx@gmail.com](aleedhx@gmail.com). Todas las vulnerabilidades de seguridad serán abordadas de inmediato.

## Licencia

El paquete MetaFive es un software de código abierto licenciado bajo la [licencia MIT](https://opensource.org/licenses/MIT).
