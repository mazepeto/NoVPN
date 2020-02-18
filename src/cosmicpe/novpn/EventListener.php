<?php

declare(strict_types=1);

namespace cosmicpe\novpn;

use cosmicpe\novpn\event\NoVPNDetectPlayerEvent;
use cosmoverse\antivpn\thread\AntiVPNException;
use cosmoverse\antivpn\thread\AntiVPNResult;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class EventListener implements Listener{

	/** @var Main */
	private $plugin;

	/** @var string */
	private $kick_message_player;

	/** @var string */
	private $kick_message_ops;

	public function __construct(Main $plugin){
		$this->plugin = $plugin;
		$this->kick_message_player = TextFormat::colorize($plugin->getConfig()->getNested("kick-message.player"));
		$this->kick_message_ops = TextFormat::colorize($plugin->getConfig()->getNested("kick-message.ops"));
	}

	/**
	 * @param PlayerJoinEvent $event
	 * @priority MONITOR
	 */
	public function onPlayerJoin(PlayerJoinEvent $event) : void{
		$player = $event->getPlayer();
		$this->plugin->getApi()->check(
			$player->getAddress(),
			function(AntiVPNResult $result) use ($player) : void{
				if($player->isOnline()){
					($ev = new NoVPNDetectPlayerEvent($this->plugin, $player, $result))->call();
					if(!$ev->isCancelled()){
						$result = $ev->getResult();
						if($result->isBehindVPN()){
							$player = $ev->getPlayer();

							$replacement_pairs = ["{PLAYER}" => $player->getName(), "{IP}" => $result->getIp(), "{ISP}" => $result->getMetadata()->getIsp()];

							$player->kick(strtr($this->kick_message_player, $replacement_pairs), false);
							if($this->kick_message_ops !== ""){
								$this->plugin->getServer()->broadcast(strtr($this->kick_message_ops, $replacement_pairs), Server::BROADCAST_CHANNEL_ADMINISTRATIVE);
							}
						}
					}
				}
			},
			function(AntiVPNException $exception) : void{ $this->plugin->getLogger()->logException($exception); }
		);
	}
}