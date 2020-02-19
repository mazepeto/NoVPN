<?php

declare(strict_types=1);

namespace cosmicpe\novpn;

use cosmoverse\antivpn\AntiVPN;
use cosmoverse\antivpn\api\client\AntiVPNClientResult;
use cosmoverse\antivpn\thread\AntiVPNException;
use pocketmine\plugin\PluginBase;

final class Main extends PluginBase{

	/** @var AntiVPN */
	private $api;

	public function onEnable() : void{
		$this->api = new AntiVPN($this, (string) $this->getConfig()->get("api-key"), (int) $this->getConfig()->get("thread-count"));
		$this->api->getClientData(
			function(AntiVPNClientResult $result) : void{
				$this->getLogger()->debug("Connected as " . $result);
			},
			function(AntiVPNException $exception) : void{
				$this->getLogger()->logException($exception);
				$this->getServer()->getPluginManager()->disablePlugin($this);
			}
		);

		$this->api->waitAll();

		if(!$this->isDisabled()){
			$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		}
	}

	public function getApi() : AntiVPN{
		return $this->api;
	}

	public function onDisable() : void{
		$this->api->close();
	}
}