    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        public function up(): void
        {
            Schema::create('kategori_proyeks', function (Blueprint $table) {
                $table->unsignedInteger('id_proyek');
                $table->unsignedInteger('id_kategori');
                $table->primary(['id_proyek', 'id_kategori']);
                $table->timestamps();

                $table->foreign('id_proyek')
                    ->references('id_proyek')->on('proyeks')
                    ->onDelete('cascade');

                $table->foreign('id_kategori')
                    ->references('id_kategori')->on('kategoris')
                    ->onDelete('cascade');
            });
        }

        public function down(): void
        {
            Schema::dropIfExists('kategori_proyeks');
        }
    };
