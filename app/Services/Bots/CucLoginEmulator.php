<?php

namespace App\Services\Bots;

use App\Console\Commands\Bots\Chrome;
use NunoMaduro\LaravelConsoleDusk\Manager;

class CucLoginEmulator
{
    private $proxyList;

    private $selectedProxy;

    private $manager;


    public function getSelectedProxy()
    {
        return $this->selectedProxy;
    }

    public function setProxyList(): void
    {
        $this->selectedProxy = [
          'ip' => '123.136.62.162',
          'port' => '8080'
        ];
    }

    private $data;


    public function emulate($command, $url, $commands = null)
    {
        $this->data = ['', ''];
        $self = $this;
        try {
            $this->manager->browse($command, static function ($browser) use ($url, $self) {
                try {

                    $browser->visit($url)
                        ->type('UserID', 'yogeshmordani@icloud.com')
                        ->type('Password', 'india')
                        ->press('Login');

                } catch (Exception $exception) {
                    $self->data = false;
                }
            });
        } catch (\Exception $exception) {
            $self->data = false;
        }

        return $this->data;
    }

    public function prepare(): void
    {
        $driver = new Chrome($this->getSelectedProxy());


        $this->manager = new Manager(
            $driver
        );
    }


    public function getProxyList(): \Illuminate\Support\Collection
    {
        return $this->proxyList;
    }
}
