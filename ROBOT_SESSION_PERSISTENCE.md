# 🔗 Persistencia de Workers por Sesión

Sistema de persistencia del worker usado para cada sesión robot, permitiendo que todas las operaciones de una sesión vayan al mismo worker.

## 📋 Descripción

Cuando un usuario hace login (`/jobs/execute`), Laravel:
1. Selecciona un worker libre del pool
2. Hace el POST al robot (login)
3. **Guarda en `portal_jobs`**:
   - `robot_worker_base_url`
   - `robot_worker_viewer_url`
   - `robot_session_id`

Luego, para operaciones posteriores (buzón, descarga, etc.), se busca el `PortalJob` asociado y se usa el **mismo worker** donde vive la sesión.

## 🗄️ Estructura de BD

### Tabla `portal_jobs` (nuevos campos)

```sql
ALTER TABLE portal_jobs ADD COLUMN robot_worker_base_url VARCHAR(255) NULL;
ALTER TABLE portal_jobs ADD COLUMN robot_worker_viewer_url VARCHAR(255) NULL;
ALTER TABLE portal_jobs ADD COLUMN robot_session_id VARCHAR(100) NULL;
ALTER TABLE portal_jobs ADD INDEX idx_session_id (robot_session_id);
```

## 🎯 Flujo completo

### 1. Login (crear sesión)

```http
POST /api/v1/app/jobs/execute
Content-Type: application/json
Authorization: Bearer {token}

{
  "company_id": 1,
  "portal": "sunat",
  "action": "sunat.menu_sol_login"
}
```

**Respuesta:**

```json
{
  "ok": true,
  "job_id": 123,
  "job_uid": "ABC123-1704153600",
  "session_id": "sess_abc123xyz",
  "viewer_url": "https://viewer1.example.com/viewer/sess_abc123xyz",
  "robot": {
    "url": "https://e-menu.sunat.gob.pe/...",
    "titulo": "Menú SOL",
    "worker": "https://robot1.example.com"
  }
}
```

**Qué hace Laravel:**

```php
// 1. Obtiene worker libre
$worker = (new RobotWorkerPool())->getFreeWorker();
// ['base_url' => 'https://robot1.example.com', 'viewer_url' => 'https://viewer1.example.com']

// 2. Crea PortalJob
$job = PortalJob::create([
    'company_id' => 1,
    'portal' => 'sunat',
    'status' => 'running',
    // ...
]);

// 3. Llama al robot (worker específico)
$robot = (new RobotClient())->setBaseUrl($worker['base_url'])->post('/sunat/login', [...]);

// 4. ✅ Guarda worker y session_id
$job->update([
    'robot_worker_base_url' => 'https://robot1.example.com',
    'robot_worker_viewer_url' => 'https://viewer1.example.com',
    'robot_session_id' => 'sess_abc123xyz',
    'status' => 'done',
]);
```

### 2. Operaciones posteriores (mismo worker)

```http
GET /api/v1/app/buzon/list?session_id=sess_abc123xyz&page=1
Authorization: Bearer {token}
```

**Qué hace Laravel:**

```php
// 1. Busca el worker asociado a esta sesión
$sessionService = new RobotSessionService();
$worker = $sessionService->getWorkerBySession('sess_abc123xyz');
// Resultado: ['base_url' => 'https://robot1.example.com', 'viewer_url' => '...']

// 2. Llama al MISMO worker donde vive la sesión
$robotClient = (new RobotClient())->setBaseUrl($worker['base_url']);
$response = $robotClient->get('/sunat/buzon/list', [
    'session_id' => 'sess_abc123xyz',
    'page' => 1,
]);

// ✅ La sesión está en robot1, así que la llamada va a robot1
```

## 🔧 Servicios disponibles

### `RobotSessionService`

Ubicación: `app/Services/RobotSessionService.php`

#### Métodos:

```php
// 1️⃣ Obtener worker por session_id
$worker = (new RobotSessionService())->getWorkerBySession('sess_abc123xyz');
// ['base_url' => '...', 'viewer_url' => '...', 'job_id' => 123, 'company_id' => 1, 'portal' => 'sunat']

// 2️⃣ Obtener worker por job_id
$workerData = (new RobotSessionService())->getWorkerByJobId(123);
// ['base_url' => '...', 'viewer_url' => '...', 'session_id' => '...', 'company_id' => 1, 'portal' => 'sunat']

// 3️⃣ Verificar sesión activa (evitar duplicados)
$activeSession = (new RobotSessionService())->getActiveSession(companyId: 1, portal: 'sunat');
// ['session_id' => '...', 'job_id' => 123, 'worker' => [...], 'started_at' => '...']

// 4️⃣ Cerrar sesión manualmente
$success = (new RobotSessionService())->closeSession('sess_abc123xyz');
// true/false
```

## 📡 Ejemplos de uso

### Endpoint: Listar buzón

```php
// app/Http/Controllers/Api/App/BuzonController.php

public function list(Request $request)
{
    $validated = $request->validate([
        'session_id' => ['required', 'string'],
        'page' => ['nullable', 'integer'],
    ]);

    // 1. Obtener worker de la sesión
    $sessionService = new RobotSessionService();
    $worker = $sessionService->getWorkerBySession($validated['session_id']);

    if (!$worker) {
        return response()->json(['ok' => false, 'error' => 'session_not_found'], 404);
    }

    // 2. Llamar al mismo worker
    $robotClient = (new RobotClient())->setBaseUrl($worker['base_url']);
    $response = $robotClient->get('/sunat/buzon/list', [
        'session_id' => $validated['session_id'],
        'page' => $validated['page'] ?? 1,
    ]);

    return response()->json($response->json());
}
```

### Endpoint: Descargar archivo

```php
public function download(Request $request)
{
    $validated = $request->validate([
        'session_id' => ['required', 'string'],
        'file_token' => ['required', 'string'],
    ]);

    $sessionService = new RobotSessionService();
    $worker = $sessionService->getWorkerBySession($validated['session_id']);

    if (!$worker) {
        return response()->json(['ok' => false, 'error' => 'session_not_found'], 404);
    }

    // GET /files/{token} al mismo worker
    $robotClient = (new RobotClient())->setBaseUrl($worker['base_url']);
    $response = $robotClient->get('/files/' . $validated['file_token']);

    // Proxy del archivo
    return response($response->body())
        ->header('Content-Type', $response->header('Content-Type'))
        ->header('Content-Disposition', $response->header('Content-Disposition'));
}
```

## 🚀 Ventajas

✅ **Sticky sessions**: Todas las operaciones van al worker correcto  
✅ **Sin estado en API**: El worker se obtiene dinámicamente de BD  
✅ **Auditoría**: Cada job registra qué worker lo procesó  
✅ **Debugging**: Fácil rastrear qué worker tiene qué sesión  
✅ **Escalabilidad**: Workers independientes, sin necesidad de session storage compartido  

## 🔄 Diagrama de flujo

```
┌─────────────┐
│   Cliente   │
└──────┬──────┘
       │ 1. POST /jobs/execute
       ▼
┌──────────────────────────┐
│  Laravel API             │
│  ┌────────────────────┐  │
│  │ RobotWorkerPool    │  │ 2. getFreeWorker()
│  │ - checkHealth()    │  │    → robot1 (0 sesiones)
│  └────────────────────┘  │
│           │              │
│           ▼              │
│  ┌────────────────────┐  │
│  │ RobotClient        │  │ 3. POST robot1/sunat/login
│  │ setBaseUrl(robot1) │  │
│  └────────────────────┘  │
│           │              │
│           ▼              │
│  ┌────────────────────┐  │
│  │ PortalJob::create  │  │ 4. Guarda:
│  │ - session_id       │  │    - robot_worker_base_url: robot1
│  │ - worker_base_url  │  │    - robot_session_id: sess_123
│  └────────────────────┘  │
└──────────┬───────────────┘
           │
           ▼
    Response: {session_id, viewer_url, job_id}
    
─────────────────────────────────────────────

┌─────────────┐
│   Cliente   │
└──────┬──────┘
       │ 5. GET /buzon/list?session_id=sess_123
       ▼
┌──────────────────────────┐
│  Laravel API             │
│  ┌────────────────────┐  │
│  │ RobotSessionService│  │ 6. getWorkerBySession('sess_123')
│  │ Query: portal_jobs │  │    → robot_worker_base_url: robot1
│  └────────────────────┘  │
│           │              │
│           ▼              │
│  ┌────────────────────┐  │
│  │ RobotClient        │  │ 7. GET robot1/sunat/buzon/list
│  │ setBaseUrl(robot1) │  │    (MISMO worker donde está la sesión)
│  └────────────────────┘  │
└──────────┬───────────────┘
           │
           ▼
    Response: {documentos: [...]}
```

## 🐛 Troubleshooting

### Session not found (404)

- Verificar que el `session_id` existe en `portal_jobs.robot_session_id`
- Revisar que `robot_worker_base_url` no sea NULL
- Query manual: `SELECT * FROM portal_jobs WHERE robot_session_id = 'sess_abc123'`

### Worker diferente responde con error

- No usar `/jobs/execute` directo para operaciones posteriores
- Siempre obtener el worker con `RobotSessionService::getWorkerBySession()`
- Verificar que la sesión sigue activa en el robot

### Sesión expirada en el robot

- El robot puede limpiar sesiones antiguas (timeout)
- Laravel debe manejar error 404 del robot y marcar job como `failed`
- Solicitar nuevo login al usuario

## 📝 Migrations aplicadas

```bash
php artisan migrate

# Migración: 2026_02_02_163247_add_robot_worker_fields_to_portal_jobs_table
# - robot_worker_base_url (VARCHAR 255)
# - robot_worker_viewer_url (VARCHAR 255)
# - robot_session_id (VARCHAR 100) + INDEX
```

## 🎯 Casos de uso

### 1. Usuario hace login → quiere ver buzón

```
1. POST /jobs/execute → session_id: sess_123, worker: robot1
2. GET /buzon/list?session_id=sess_123 → query BD → worker: robot1 → OK
```

### 2. Múltiples usuarios en diferentes robots

```
User A → robot1 (sess_aaa)
User B → robot2 (sess_bbb)
User C → robot1 (sess_ccc)

GET /buzon/list?session_id=sess_aaa → robot1 ✅
GET /buzon/list?session_id=sess_bbb → robot2 ✅
GET /buzon/list?session_id=sess_ccc → robot1 ✅
```

### 3. Robot1 cae → User A migra a Robot2

```
1. Detectar que robot1 no responde (health check)
2. Crear nueva sesión en robot2
3. Actualizar portal_jobs:
   - robot_worker_base_url = robot2
   - robot_session_id = sess_nuevo
```

## 🔐 Seguridad

- ✅ Validar que `session_id` pertenece al usuario autenticado (company_id match)
- ✅ No exponer `robot_worker_base_url` en respuestas API (solo internamente)
- ✅ Rate limiting en endpoints que usan sesiones existentes
- ✅ Timeout de sesiones antiguas (> 1 hora sin actividad)

---

Con esta implementación, **todos los endpoints que usan una sesión activa irán automáticamente al worker correcto**, sin necesidad de configuración adicional. 🚀
