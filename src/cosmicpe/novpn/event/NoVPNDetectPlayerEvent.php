<?php

declare(strict_types=1);

namespace cosmicpe\novpn\event;

use cosmicpe\novpn\Main;
use cosmoverse\antivpn\api\ip\AntiVPNIPResult;
use pocketmine\event\Cancellable;
use pocketmine\event\plugin\PluginEvent;
use pocketmine\Player;

final class NoVPNDetectPlayerEvent extends PluginEvent implements Cancellable{

	/** @var Player */
	private $player;

	/** @var AntiVPNIPResult */
	private $result;

	public function __construct(Main $plugin, Player $player, AntiVPNIPResult $result){
		parent::__construct($plugin);
		$this->player = $player;
		$this->result = $result;
	}

	public function getPlayer() : Player{
		return $this->player;
	}

	public function getResult() : AntiVPNIPResult{
		return $this->result;
	}
}