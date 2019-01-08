@php( $menu = __('common.build_type') )
@php( $menu[] = 'Result' )


 {{ Html::bsTabs($menu,$active_tab) }}