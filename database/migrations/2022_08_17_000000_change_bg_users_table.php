<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $tableName = $this->getTableName();

        Schema::table($tableName, function (Blueprint $table) use ($tableName) {
            $table->unsignedInteger('user_code')->autoIncrement()->change();
            $table->string('passwd')->change();

            if (! Schema::hasColumn($tableName, 'remember_token')) {
                $table->rememberToken();
            }

            if (! Schema::hasColumn($tableName, 'active_time')) {
                $table->dateTime('active_time')->useCurrent();
            }

            if (! Schema::hasColumn($tableName, 'create_date')) {
                $table->dateTime('create_date')->useCurrent();
            }

            if (! Schema::hasColumn($tableName, 'update_time')) {
                $table->dateTime('update_time')->useCurrent();
            }

            if (! Schema::hasColumn($tableName, 'email')) {
                $table->string('email')->unique();
            } else {
                $table->string('email')->change();
            }

            if (! Schema::hasColumn($tableName, 'email_verified_at')) {
                $table->dateTime('email_verified_at')->nullable();
            }

            $indexes = $this->getTableIndexes($tableName);

            if (! isset($indexes['bg_users_unique'])) {
                $table->unique('email');
            }
        });
    }

    protected function getTableName(): string
    {
        return config('lcframework.last_chaos.database.auth').'.bg_user';
    }

    protected function getTableIndexes(string $tableName): array
    {
        return Schema::getConnection()
            ->getDoctrineSchemaManager()
            ->listTableIndexes($tableName);
    }
};
