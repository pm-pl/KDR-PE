<?php

declare(strict_types=1);

namespace Terpz710\KDRPE\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

use Terpz710\KDRPE\Main;

class TopKillCommand extends Command {

    private $plugin;

    public function __construct($plugin) {
        parent::__construct('topkill', 'Display top kills', '/topkill');
        $this->setPermission('kdr.topkill');
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if ($sender instanceof Player && !$this->testPermission($sender)) {
            return;
        }

        $topKillList = $this->getTopKillList();

        if (empty($topKillList)) {
            $sender->sendMessage('No top kills available.');
            return;
        }

        $sender->sendMessage('Top Kills:');
        foreach ($topKillList as $position => $data) {
            $sender->sendMessage("[{$position}] {$data['player']} - Kills: {$data['kills']}");
        }
    }

    private function getTopKillList(): array {
        $topKillList = [];
        $playerData = $this->plugin->getPlayerData();

        uasort($playerData, function ($a, $b) {
            return $b['kills'] <=> $a['kills'];
        });

        $count = 0;
        foreach ($playerData as $playerName => $data) {
            $topKillList[++$count] = ['player' => $playerName, 'kills' => $data['kills']];
            if ($count >= 10) {
                break;
            }
        }

        return $topKillList;
    }
}
