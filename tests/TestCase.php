<?php

abstract class TestCase extends Laravel\Lumen\Testing\TestCase
{
    protected $jsonApiStructure = [
        'jsonapi',
        'data' => ['type', 'id', 'attributes'],
    ];
    protected $jsonApiErrorStructure = [
        'jsonapi',
        'errors' => [['source' => ['parameter'], 'title']],
    ];

    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }
}
