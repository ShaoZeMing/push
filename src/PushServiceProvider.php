<?php
namespace Shaozeming\Push;

use Illuminate\Support\ServiceProvider;

// use GeTui\App\Console\Commands\MessagePush;

class PushServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        $this->mergeConfigFrom(
            __DIR__.'/config/push.php', 'push'
        );
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //

        $this->app->singleton('GeTuiService', function ($app){
            return new GeTuiService();
        });

        $this->app->singleton('PushMsgService', function ($app){
            return new PushMsgService();
        });

        $this->app->singleton('PushManager', function ($app){
            return new PushManager();
        });

        $this->app->singleton('MerGeTuiService', function ($app){
            // var_export($app['config']['getui']['tag']);
            $obj = new GeTuiService();
            return $obj->getMerInstance();
        });
    }
}
