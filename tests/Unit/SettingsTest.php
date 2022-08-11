<?php

it('returns default value if setting does not exist', function () {
    $settings = $this->app->make('lcframework.settings');

    $value = $settings->get('::setting-key::', '::default-value::');

    expect($value)->toEqual('::default-value::');
});

it('returns value after putting', function () {
    $settings = $this->app->make('lcframework.settings');

    $settings->put('::setting-key::', '::value-exists::');

    expect($settings->get('::setting-key::'))->toEqual('::value-exists::');
});
