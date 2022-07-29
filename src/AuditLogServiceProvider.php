<?php

namespace Sdon2\AuditLog;

use Illuminate\Auth\AuthManager;
use Illuminate\Config\Repository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Sdon2\AuditLog\Models\AuditLog;
use Sdon2\AuditLog\Exceptions\InvalidConfiguration;

class AuditLogServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $this->publishMigrationsAndConfig();
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->bind('command.auditlog:clean', CleanAuditLogCommand::class);

        $this->commands([
            'command.auditlog:clean',
        ]);

        $this->app->singleton(AuditLogger::class, function () {
            $defaultLogName = config('laravel-audit-log.default_log_name');
            return (new AuditLogger(auth(), config()))->useLog($logName ?? $defaultLogName);
        });
    }

    public static function determineAuditLogModel(): string
    {
        $activityModel = config('laravel-audit-log.audit_log_model') ?? AuditLog::class;

        if (!is_a($activityModel, AuditLog::class, true)) {
            throw InvalidConfiguration::modelIsNotValid($activityModel);
        }

        return $activityModel;
    }

    public static function getAuditLogModelInstance(): Model
    {
        $activityModelClassName = self::determineAuditLogModel();

        return new $activityModelClassName();
    }

    protected function publishMigrationsAndConfig()
    {
        $this->publishes([
            __DIR__ . '/../config/laravel-audit-log.php' => config_path('laravel-audit-log.php'),
        ], 'config');

        $this->mergeConfigFrom(__DIR__ . '/../config/laravel-audit-log.php', 'laravel-audit-log');

        if (!class_exists('CreateAuditLogTable')) {
            $timestamp = date('Y_m_d_His', time());

            $this->publishes([
                __DIR__ . '/../migrations/create_audit_log_table.php' => database_path("/migrations/{$timestamp}_create_audit_log_table.php"),
            ], 'migrations');
        }
    }
}
