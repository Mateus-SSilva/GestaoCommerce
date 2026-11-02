<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('operador')->after('email'); // gestor | operador
            $table->string('job_title')->nullable()->after('role');
            $table->string('color', 20)->default('#6366f1')->after('job_title');
        });

        Schema::create('boards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('color', 20)->default('#6366f1');
            $table->string('icon')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('board_columns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('board_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedInteger('position')->default(0);
            $table->unsignedInteger('wip_limit')->nullable();
            $table->boolean('is_done')->default(false);
            $table->timestamps();
        });

        Schema::create('labels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color', 20)->default('gray');
            $table->timestamps();
        });

        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('board_id')->constrained()->cascadeOnDelete();
            $table->foreignId('board_column_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('priority')->default('media'); // baixa | media | alta | urgente
            $table->date('due_date')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->foreignId('assignee_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('origin')->default('manual'); // manual | recorrencia | agente
            $table->foreignId('recurring_task_id')->nullable();
            $table->string('channel')->nullable(); // marketplace relacionado
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['board_column_id', 'position']);
            $table->index('due_date');
        });

        Schema::create('card_label', function (Blueprint $table) {
            $table->foreignId('card_id')->constrained()->cascadeOnDelete();
            $table->foreignId('label_id')->constrained()->cascadeOnDelete();
            $table->primary(['card_id', 'label_id']);
        });

        Schema::create('checklist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('card_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->boolean('is_done')->default(false);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('card_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('body');
            $table->timestamps();
        });

        Schema::create('recurring_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('board_id')->constrained()->cascadeOnDelete();
            $table->foreignId('board_column_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assignee_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('priority')->default('media');
            $table->string('frequency'); // diaria | semanal | mensal
            $table->json('weekdays')->nullable(); // 1=segunda ... 7=domingo
            $table->unsignedTinyInteger('day_of_month')->nullable();
            $table->unsignedSmallInteger('due_in_days')->default(0);
            $table->json('checklist')->nullable();
            $table->boolean('is_active')->default(true);
            $table->date('last_generated_on')->nullable();
            $table->timestamps();
        });

        Schema::create('calendar_events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->date('date');
            $table->string('type')->default('evento'); // evento | comercial
            $table->string('color', 20)->default('#0ea5e9');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('monitor_issues', function (Blueprint $table) {
            $table->id();
            $table->string('fingerprint')->unique(); // canal + anúncio + tipo (deduplicação)
            $table->string('channel');
            $table->string('type');
            $table->string('severity'); // critico | alto | medio | info
            $table->string('sku')->nullable();
            $table->string('listing_ref')->nullable();
            $table->string('product_name')->nullable();
            $table->text('message');
            $table->text('suggestion')->nullable();
            $table->string('status')->default('aberto'); // aberto | resolvido
            $table->foreignId('card_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('occurrences')->default(1);
            $table->timestamp('first_seen_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('monitor_runs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('checked_listings')->default(0);
            $table->unsignedInteger('new_issues')->default(0);
            $table->unsignedInteger('updated_issues')->default(0);
            $table->unsignedInteger('resolved_issues')->default(0);
            $table->unsignedInteger('cards_created')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['monitor_runs', 'monitor_issues', 'calendar_events', 'recurring_tasks', 'comments', 'checklist_items', 'card_label', 'cards', 'labels', 'board_columns', 'boards'] as $t) {
            Schema::dropIfExists($t);
        }
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn(['role', 'job_title', 'color']));
    }
};
