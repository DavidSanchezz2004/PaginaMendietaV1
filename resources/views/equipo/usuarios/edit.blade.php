@extends('layouts.app-equipo')

@section('title', 'Editar usuario')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/usuarios.css') }}?v={{ time() }}">
@endpush

@section('content')
<div class="card">
  <h2 style="margin-top:0;">Editar usuario</h2>

  <form method="POST" action="{{ route('equipo.usuarios.update', $user) }}">
    @csrf
    @method('PUT')

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
      <div>
        <label>Nombre</label>
        <input class="input" name="name" value="{{ old('name', $user->name) }}" required>
        @error('name') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div>
        <label>Email</label>
        <input class="input" name="email" type="email" value="{{ old('email', $user->email) }}" required>
        @error('email') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div>
        <label>Rol</label>
        <select class="input" name="rol" id="rol" required>
          @foreach($roles as $r)
            <option value="{{ $r }}" @selected(old('rol', $user->rol)===$r)>{{ $r }}</option>
          @endforeach
        </select>
        @error('rol') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div>
        <label>Password (opcional)</label>
        <input class="input" name="password" type="password" placeholder="Dejar vacío para no cambiar">
        @error('password') <div class="error">{{ $message }}</div> @enderror
      </div>
    </div>

    {{-- Empresa (solo para rol cliente) --}}
    <div id="companyBlock" style="margin-top:14px; display:none;">
      <label>Empresa (solo rol cliente)</label>

      <div style="display:flex; gap:10px; align-items:center; margin-top:6px; flex-wrap:wrap;">
        <input
          type="hidden"
          name="company_id"
          id="company_id"
          value="{{ old('company_id', $user->company_id) }}"
        >

        <input
          id="company_label"
          type="text"
          placeholder="Selecciona una empresa…"
          readonly
          style="width:min(520px, 100%);"
        >

        <button type="button" class="btn" onclick="openCompanyModal()">Elegir empresa</button>
        <button type="button" class="btn" onclick="clearCompany()">Quitar</button>
      </div>

      @error('company_id')
        <div style="color:#b00020; font-size:13px; margin-top:6px;">{{ $message }}</div>
      @enderror
    </div>

    <hr style="margin:16px 0;">

    <h3 style="margin:0 0 10px;">Perfil</h3>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
      <div><label>País</label><input class="input" name="country" value="{{ old('country', $profile->country ?? '') }}"></div>
      <div><label>Ciudad</label><input class="input" name="city" value="{{ old('city', $profile->city ?? '') }}"></div>
      <div><label>Código postal</label><input class="input" name="postal_code" value="{{ old('postal_code', $profile->postal_code ?? '') }}"></div>
      <div><label>Tipo doc</label><input class="input" name="document_type" value="{{ old('document_type', $profile->document_type ?? '') }}"></div>
      <div><label>Número doc</label><input class="input" name="document_number" value="{{ old('document_number', $profile->document_number ?? '') }}"></div>
      <div><label>Teléfono</label><input class="input" name="phone" value="{{ old('phone', $profile->phone ?? '') }}"></div>
      <div style="grid-column:1/3;"><label>Bio</label><input class="input" name="bio" value="{{ old('bio', $profile->bio ?? '') }}"></div>
    </div>

    <div style="margin-top:14px;display:flex;gap:10px;">
      <button class="btn btn-primary" type="submit">Guardar</button>
      <a class="btn" href="{{ route('equipo.usuarios.index') }}">Volver</a>
    </div>
  </form>
</div>

{{-- Modal Empresas --}}
<div id="companyModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:9999;">
  <div style="background:#fff; width:min(720px, 92vw); margin:7vh auto; border-radius:14px; padding:16px;">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:10px;">
      <h3 style="margin:0;">Seleccionar empresa</h3>
      <button type="button" class="btn" onclick="closeCompanyModal()">Cerrar</button>
    </div>

    <div style="margin-top:10px;">
      <input
        id="companySearch"
        type="text"
        placeholder="Buscar por RUC o razón social..."
        style="width:100%;"
        oninput="filterCompanies()"
      >
    </div>

    <div id="companyList" style="margin-top:12px; max-height:52vh; overflow:auto; border:1px solid #eee; border-radius:12px;">
      @foreach($companies as $c)
        <button
          type="button"
          class="company-row"
          data-id="{{ $c->id }}"
          data-razon="{{ $c->razon_social }}"
          data-ruc="{{ $c->ruc }}"
          data-text="{{ strtolower($c->razon_social.' '.$c->ruc) }}"
          onclick="pickFromRow(this)"
          style="width:100%; text-align:left; padding:12px; border:0; background:#fff; border-bottom:1px solid #f2f2f2; cursor:pointer;"
        >
          <b>{{ $c->razon_social }}</b>
          <div style="font-size:13px; opacity:.75;">RUC: {{ $c->ruc }}</div>
        </button>
      @endforeach

      @if(($companies ?? collect())->isEmpty())
        <div style="padding:14px; opacity:.7;">No hay empresas registradas.</div>
      @endif
    </div>
  </div>
</div>

<script>
  function openCompanyModal(){
    document.getElementById('companyModal').style.display = 'block';
    document.getElementById('companySearch').value = '';
    filterCompanies();
    setTimeout(() => document.getElementById('companySearch').focus(), 10);
  }

  function closeCompanyModal(){
    document.getElementById('companyModal').style.display = 'none';
  }

  function pickFromRow(btn){
    const id = btn.dataset.id;
    const razon = btn.dataset.razon;
    const ruc = btn.dataset.ruc;

    document.getElementById('company_id').value = id;
    document.getElementById('company_label').value = razon + ' (' + ruc + ')';
    closeCompanyModal();
  }

  function clearCompany(){
    document.getElementById('company_id').value = '';
    document.getElementById('company_label').value = '';
  }

  function filterCompanies(){
    const q = (document.getElementById('companySearch').value || '').toLowerCase().trim();
    document.querySelectorAll('.company-row').forEach(btn => {
      const txt = btn.dataset.text || '';
      btn.style.display = (q === '' || txt.includes(q)) ? 'block' : 'none';
    });
  }

  function isRolCliente(value){
    return (value || '').toLowerCase() === 'cliente';
  }

  function syncCompanyBlock(){
    const rol = document.getElementById('rol').value;
    const block = document.getElementById('companyBlock');

    if (isRolCliente(rol)) {
      block.style.display = 'block';
    } else {
      block.style.display = 'none';
      clearCompany();
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    syncCompanyBlock();
    document.getElementById('rol').addEventListener('change', syncCompanyBlock);

    // Pre-cargar label desde company_id
    const currentId = document.getElementById('company_id').value;
    if (currentId) {
      const btn = document.querySelector('.company-row[data-id="' + currentId + '"]');
      if (btn) {
        document.getElementById('company_label').value =
          btn.dataset.razon + ' (' + btn.dataset.ruc + ')';
      }
    }
  });

  document.getElementById('companyModal').addEventListener('click', e => {
    if (e.target.id === 'companyModal') closeCompanyModal();
  });

  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeCompanyModal();
  });
</script>
@endsection
