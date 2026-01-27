<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExecuteJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // auth:sanctum ya lo controla
    }

    protected function prepareForValidation(): void
    {
        $portal = strtolower(trim((string) $this->input('portal', '')));

        // ✅ Alias tolerantes
        if ($portal === 'afp') {
            $portal = 'afpnet';
        }

        // si algún día te llega "sunafil_casilla" etc, aquí lo normalizas también.

        $this->merge([
            'portal' => $portal,
        ]);
    }

    public function rules(): array
    {
        return [
            'company_id' => ['required','integer','exists:companies,id'],
            // ✅ ya normalizado, pero igual validamos
            'portal'     => ['required','in:sunat,sunafil,afpnet'],
            'action'     => ['required','string','max:80'],
            'mode'       => ['nullable','in:sync,async'],
            'meta'       => ['nullable','array'],
            'captcha'    => ['nullable','string','max:16'],
        ];
    }
}
