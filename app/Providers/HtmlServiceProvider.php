<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Html;
use Form;
class HtmlServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Form::component('bsText', 'components.text', ['name', 'title','value'=>null,'attr'=>[]]);
        Form::component('bsSelect', 'components.select', ['name','title','values'=>[1=>'Да',0=>'Нет'],'selected'=>null,'attr'=>[]]);
        Form::component('bsTextarea', 'components.textarea', ['name', 'title','value'=>null,'attr'=>[]]);
        Form::component('bsFile', 'components.file', ['name', 'title','value'=>null,'attr'=>[]]);
        Form::component('bsCheckbox', 'components.checkbox', ['name', 'title','value'=>1,'attr'=>[]]);
        
        Html::component('deleteLink', 'components.delete', ['route','message'=>'Delete ?','attr'=>[]]);
    }
    

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
