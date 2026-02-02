# 🤖 Robot Worker Pool

Sistema de balanceo de carga para múltiples robots RPA (Selenium).

## 📋 Descripción

El `RobotWorkerPool` permite distribuir las sesiones de automatización entre múltiples instancias del robot, seleccionando automáticamente el worker con menor carga (sesiones activas).

## 🎯 Funcionamiento

1. **Configuración de Workers**: Define una lista de robots disponibles (URLs)
2. **Health Check**: Consulta `/health` de cada worker con caché de 10 segundos
3. **Selección Inteligente**: Elige el worker con `sesiones_activas=0` o el menor
4. **Ejecución**: Usa ese worker para el POST y devuelve su viewer_url

## ⚙️ Configuración

### Opción 1: Variable de entorno (JSON)

```env
ROBOT_WORKERS='[{"base_url":"https://robot1.example.com","viewer_url":"https://viewer1.example.com"},{"base_url":"https://robot2.example.com","viewer_url":"https://viewer2.example.com"}]'
```

### Opción 2: En `config/services.php`

```php
'robot' => [
    'workers' => [
        ['base_url' => 'https://robot1.example.com', 'viewer_url' => 'https://viewer1.example.com'],
        ['base_url' => 'https://robot2.example.com', 'viewer_url' => 'https://viewer2.example.com'],
    ],
],
```

### Opción 3: Sin configurar (Fallback)

Si no se configura `workers`, usa `ROBOT_BASE_URL` y `ROBOT_VIEWER_URL` como único worker.

```env
ROBOT_BASE_URL=https://robot.antrixsys.xyz
ROBOT_VIEWER_URL=https://operator.antrixsys.xyz
ROBOT_API_KEY=tu-api-key-aqui
```

## 📡 Endpoint `/health` esperado

Cada robot debe implementar:

```http
GET /health
Headers: x-api-key: your-api-key

Response:
{
  "ok": true,
  "sesiones_activas": 0,
  "sesiones_max": 5,
  "timestamp": "2026-02-02T10:30:00Z"
}
```

## 🔧 Uso en el código

### Automático en `JobExecuteController`

El sistema ya está integrado:

```php
// 1. Obtiene worker libre
$workerPool = new RobotWorkerPool();
$worker = $workerPool->getFreeWorker();

if (!$worker) {
    return response()->json(['ok' => false, 'error' => 'no_workers_available'], 503);
}

// 2. Usa ese worker para la llamada
$robotClient = (new RobotClient())->setBaseUrl($worker['base_url']);
$robot = $robotClient->post('/sunat/login', [...]);

// 3. Devuelve viewer del worker seleccionado
return response()->json([
    'viewer_url' => $worker['viewer_url'] . '/viewer/' . $session_id,
]);
```

### Manual en cualquier lugar

```php
use App\Services\RobotWorkerPool;

$pool = new RobotWorkerPool();

// Obtener worker libre
$worker = $pool->getFreeWorker();
// ['base_url' => '...', 'viewer_url' => '...', 'sesiones_activas' => 0]

// Ver salud de todos
$allHealth = $pool->getAllWorkersHealth();
```

## 📊 Monitoring

### Endpoint de salud de workers

```http
GET /api/v1/admin/workers/health
Authorization: Bearer {token}

Response:
{
  "ok": true,
  "workers": [
    {
      "base_url": "https://robot1.example.com",
      "viewer_url": "https://viewer1.example.com",
      "available": true,
      "sesiones_activas": 0,
      "health": {
        "ok": true,
        "sesiones_activas": 0,
        "sesiones_max": 5
      }
    },
    {
      "base_url": "https://robot2.example.com",
      "viewer_url": "https://viewer2.example.com",
      "available": true,
      "sesiones_activas": 2,
      "health": {
        "ok": true,
        "sesiones_activas": 2,
        "sesiones_max": 5
      }
    }
  ],
  "timestamp": "2026-02-02T10:30:00.000000Z"
}
```

## 🎯 Ventajas

✅ **Balanceo de carga automático**: Distribuye sesiones entre múltiples robots  
✅ **Alta disponibilidad**: Si un robot falla, usa otro automáticamente  
✅ **Caché inteligente**: Health checks cada 10 segundos (evita overhead)  
✅ **Fallback**: Si no hay workers configurados, usa la URL por defecto  
✅ **Zero downtime**: Escala horizontalmente agregando más robots  

## 🔄 Flujo completo

```
1. App solicita ejecutar job → JobExecuteController
                                    ↓
2. Obtener worker libre → RobotWorkerPool.getFreeWorker()
                                    ↓
3. Consulta /health de cada worker (con caché 10s)
                                    ↓
4. Selecciona el de menor carga (sesiones_activas)
                                    ↓
5. RobotClient.setBaseUrl(worker) → POST /sunat/login
                                    ↓
6. Responde con viewer_url del worker usado
```

## 🚀 Despliegue

### Infraestructura recomendada

```
┌────────────────────────────────────────────┐
│  Laravel API (Portal Mendieta)             │
│  - RobotWorkerPool                         │
│  - RobotClient                             │
└─────────────┬──────────────────────────────┘
              │
     ┌────────┴────────┐
     │                 │
┌────▼─────┐    ┌─────▼──────┐
│ Robot 1  │    │  Robot 2   │
│ (VPS 1)  │    │  (VPS 2)   │
│ - Selenium│    │ - Selenium │
│ - /health│    │ - /health  │
│ - /sunat/│    │ - /sunat/  │
│   login  │    │   login    │
└──────────┘    └────────────┘
```

### Ejemplo con Docker Compose

```yaml
services:
  robot1:
    image: ghcr.io/tu-org/selenium-robot:latest
    environment:
      - ROBOT_API_KEY=shared-secret
      - MAX_SESSIONS=5
    ports:
      - "3001:3000"
  
  robot2:
    image: ghcr.io/tu-org/selenium-robot:latest
    environment:
      - ROBOT_API_KEY=shared-secret
      - MAX_SESSIONS=5
    ports:
      - "3002:3000"
```

Luego en `.env`:

```env
ROBOT_WORKERS='[{"base_url":"http://localhost:3001","viewer_url":"http://localhost:3001"},{"base_url":"http://localhost:3002","viewer_url":"http://localhost:3002"}]'
```

## 🐛 Troubleshooting

### No hay workers disponibles (503)

- Verificar que los robots estén corriendo
- Verificar conectividad de red
- Revisar logs: `tail -f storage/logs/laravel.log | grep RobotWorkerPool`

### Worker no responde /health

- Verificar que el endpoint `/health` esté implementado
- Verificar `x-api-key` en headers
- Timeout de 5 segundos (ajustable en `RobotWorkerPool.php`)

### Siempre elige el mismo worker

- Cache de 10 segundos: esperar que expire
- Limpiar cache manualmente: `php artisan cache:clear`
- Verificar que `/health` devuelva `sesiones_activas` actualizado

## 📝 Notas

- El caché de health es de **10 segundos** para evitar saturar los robots
- Si un worker falla, se omite y se prueba el siguiente
- Prioriza workers con `sesiones_activas=0` para balanceo óptimo
- Compatible con Cloudflare Access (headers automáticos)
