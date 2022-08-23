<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
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
        $tableName = 'bg_user';

        $builder = Schema::connection('installer');

        $builder->table(
            $tableName,
            function (Blueprint $table) use ($builder, $tableName) {
                $indexes = $this->getTableIndexes($builder, $tableName);

                logger(var_export($indexes, true));

                $table->unsignedInteger('user_code')
                    ->autoIncrement()
                    ->change();

                $table->string('passwd')->change();

                if (! $builder->hasColumn($tableName, 'remember_token')) {
                    $table->rememberToken();
                }

                if (! $builder->hasColumn($tableName, 'active_time')) {
                    $table->dateTime('active_time')->useCurrent();
                }

                if (! $builder->hasColumn($tableName, 'create_date')) {
                    $table->dateTime('create_date')->useCurrent();
                }

                if (! $builder->hasColumn($tableName, 'update_time')) {
                    $table->dateTime('update_time')->useCurrent();
                }

                if (! $builder->hasColumn($tableName, 'email')) {
                    $table->string('email')->unique();
                } else {
                    $table->string('email')->change();

                    if (! isset($indexes['bg_users_unique'])) {
                        $table->unique('email');
                    }
                }

                if (! $builder->hasColumn($tableName, 'email_verified_at')) {
                    $table->dateTime('email_verified_at')->nullable();
                }
            }
        );
    }

    protected function getTableIndexes(Builder $builder, string $tableName): array
    {
        return $builder->getConnection()
            ->getDoctrineSchemaManager()
            ->listTableIndexes($tableName);
    }
};
