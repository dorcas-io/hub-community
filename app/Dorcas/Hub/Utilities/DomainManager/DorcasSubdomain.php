<?php


namespace App\Dorcas\Hub\Utilities\DomainManager;

class DorcasSubdomain
{
    /** @var string */
    protected $host;
    
    /** @var string */
    protected $subdomain;
    
    /** @var string|null */
    protected $service;
    
    /** @var bool  */
    public $secure;
    
    /** @var string */
    protected $edition;
    
    /**
     * DorcasSubdomain constructor.
     *
     * @param array $hostInfo
     * @param bool  $secure
     */
    public function __construct(array $hostInfo = [], bool $secure = true, string $edition = 'business')
    {
        $this->secure = app()->environment() === 'local' ? false : $secure;
        $this->edition = $edition;
        $subdomain = $hostInfo[0] ?? '';
        $service = count($hostInfo) > 2 ? $hostInfo[1] : null;
        $host = count($hostInfo) > 2 ? $hostInfo[2] : ($hostInfo[1] ?? '');
        $this->setHost($host)->setService($service)->setSubdomain($subdomain);
    }
    
    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }
    
    /**
     * @return string
     */
    public function getSubdomain(): string
    {
        return $this->subdomain;
    }
    
    /**
     * @return string|null
     */
    public function getService(): ?string
    {
        return $this->service;
    }
    
    /**
     * @return string
     */
    public function getDomain(): string
    {
        $scheme = $this->secure ? 'https' : 'http';
        return $scheme . '://' . $this->subdomain . '.' . ($this->service !== null ? $this->service . '.' : '') .
            $this->host;
    }
    
    /**
     * @param string $host
     *
     * @return \App\Dorcas\Hub\Utilities\DomainManager\DorcasSubdomain
     */
    public function setHost(string $host): DorcasSubdomain
    {
        $this->host = $host;
        return $this;
    }
    
    /**
     * @param string $subdomain
     *
     * @return \App\Dorcas\Hub\Utilities\DomainManager\DorcasSubdomain
     */
    public function setSubdomain(string $subdomain): DorcasSubdomain
    {
        $this->subdomain = $subdomain;
        return $this;
    }
    
    /**
     * @param string|null $service
     *
     * @return \App\Dorcas\Hub\Utilities\DomainManager\DorcasSubdomain
     */
    public function setService(string $service = null): DorcasSubdomain
    {
        $this->service = $service;
        return $this;
    }

    /**
     * @return string
     */
    public function getEdition(): string
    {
        return $this->edition;
    }


    /**
     * @param string $edition
     *
     * @return \App\Dorcas\Hub\Utilities\DomainManager\DorcasSubdomain
     */
    public function setEdition(string $edition): DorcasSubdomain
    {
        $this->edition = $edition;
        return $this;
    }


}
