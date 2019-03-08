<?php

namespace App\Services\Bots;

use App\Console\Commands\Bots\Chrome;
use NunoMaduro\LaravelConsoleDusk\Manager;

class WebsiteEmulator
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


    public function emulate($command, $url, $commands = null)
    {
        try {
            $this->manager->browse($command, function ($browser) use ($url) {
//                $browser->visit($url)
//                    ->pause(500)
//                    ->element('div.product-wrapper')
//                ;

                $data = $browser->visit($url)
                    ->pause(500)
                    ->element('span.price')
                    ->getAttribute('innerHTML')
                ;

                dd($data);

                return $data;
            });
        } catch (\Exception $exception) {
            return '';
        }
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
