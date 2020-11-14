<?php

namespace App\Dorcas\Hub\Utilities\DomainManager;


use Hostville\Dorcas\Sdk;
use Illuminate\Support\Collection;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\UnreadableFileException;

class HostingManager
{
    /** @var Collection  */
    private $servers;
    
    /** @var Sdk  */
    private $sdk;
    
    /**
     * HostingManager constructor.
     *
     * @param Sdk|null $sdk
     *
     * @throws FileNotFoundException
     * @throws UnreadableFileException
     */
    public function __construct(Sdk $sdk = null)
    {
        $this->servers = $this->loadServerInfo();
        $this->sdk = $sdk ?: app(Sdk::class);
    }
    
    /**
     * @return Collection
     */
    public function getServers(): Collection
    {
        return $this->servers;
    }
    
    /**
     * @return Collection
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCurrentServerStatus(): Collection
    {
        $query = $this->sdk->createDomainResource()->send('GET', ['hosting-capacity']);
        # create the resource
        $resource = collect([]);
        # the resource object
        if ($query->isSuccessful()) {
            # it was successful
            $resource = collect($query->getData());
        }
        return $this->servers->map(function ($server) use ($resource) {
            $load = $resource->where('hosting_box_id', $server->id)->first();
            if (empty($load)) {
                $load = ['domains_count' => 0, 'hosting_box_id' => $server->id];
            }
            $server->current_load = $load['domains_count'];
            $server->remaining_spots = $server->capacity - $load['domains_count'];
            return $server;
        });
        # convert it to a collection
        
    }
    
    /**
     * Loads up the server configuration for Dorcas.
     *
     * @param string|null $filename
     *
     * @return Collection
     * @throws FileNotFoundException
     * @throws UnreadableFileException
     */
    private function loadServerInfo(string $filename = null): Collection
    {
        $filename = $filename ?: resource_path('hosting-servers.json');
        # path to the resource file
        if (!file_exists($filename)) {
            throw new FileNotFoundException('Could not find the servers configuration file.');
        }
        if (!is_readable($filename)) {
            throw new UnreadableFileException('The servers configuration file could not be read.');
        }
        $servers = json_decode(file_get_contents($filename), true);
        # read in the file content
        if ($servers === false) {
            throw new \RuntimeException('The server configuration file could not be parsed, kindly check the format of the file.');
        }
        return collect($servers)->map(function ($server) {
            return (object) $server;
        });
    }
}