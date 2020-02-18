<?php

declare(strict_types=1);

namespace cosmicpe\novpn;

use cosmoverse\antivpn\AntiVPN;
use pocketmine\plugin\PluginBase;

final class Main extends PluginBase{

	/** @var AntiVPN */
	private $api;

	public function onEnable() : void{
		$this->api = new AntiVPN($this, (string) $this->getConfig()->get("api-key"), (int) $this->getConfig()->get("thread-count"));
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
	}

	public function getApi() : AntiVPN{
		return $this->api;
	}

	public function onDisable() : void{
		$this->api->close();
	}
}