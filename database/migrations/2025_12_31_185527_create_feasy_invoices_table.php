<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up(): void
{
    Schema::create('feasy_invoices', function (Blueprint $table) {
        $table->id();

        // Multi-empresa (por ahora nullable si aún no lo tienes)
        $table->unsignedBigInteger('company_id')->nullable()->index();

        // Documento
        $table->string('codigo_tipo_documento', 2)->default('01'); // factura
        $table->string('serie', 4)->index();
        $table->string('numero', 8)->index(); // guardamos ya padded 8

        // Cliente (receptor)
        $table->string('cliente_tipo_doc', 1)->default('6'); // RUC
        $table->string('cliente_numero_doc', 15)->index();
        $table->string('cliente_nombre', 200);
        $table->string('cliente_correo', 200)->nullable();
        $table->string('cliente_direccion', 200)->nullable();

        // Totales
        $table->decimal('monto_gravado', 12, 2)->default(0);
        $table->decimal('monto_igv', 12, 2)->default(0);
        $table->decimal('monto_total', 12, 2)->default(0);

        // Estado FEASY/SUNAT
        $table->boolean('success')->default(false);
        $table->string('codigo_respuesta', 10)->nullable();      // "0", "2800", etc
        $table->string('mensaje_respuesta', 255)->nullable();
        $table->string('nombre_archivo_xml', 255)->nullable();

        // Rutas de descarga (se llenan cuando consultes)
        $table->text('ruta_xml')->nullable();
        $table->text('ruta_cdr')->nullable();
        $table->text('ruta_pdf')->nullable();
        $table->string('codigo_hash', 255)->nullable();
        $table->text('valor_qr')->nullable();

        // Guardar request/response (auditoría)
        $table->json('payload')->nullable();
        $table->json('response')->nullable();

        $table->timestamps();

        // Evitar duplicados por emisor+doc (si luego manejas multi-empresa, puedes incluir company_id)
        $table->unique(['codigo_tipo_documento', 'serie', 'numero'], 'uniq_doc');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feasy_invoices');
    }
};
