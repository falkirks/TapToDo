<?php
namespace taptodo;


use pocketmine\command\ConsoleCommandSender;
use pocketmine\Player;

class Command {
    const AS_CONSOLE_TYPE = 0;
    const AS_PLAYER_TYPE = 1;
    const AS_OP_TYPE = 2;

    private $originalCommand;
    private $compiledCommand;
    private $executionMode;
    /** @var TapToDo  */
    private $plugin;
    public function __construct($command, TapToDo $plugin){
        $this->originalCommand = $command;
        $this->compiledCommand = null;
        $this->plugin = $plugin;
        $this->compile();
    }
    public function compile(){
        $this->executionMode = Block::AS_PLAYER_TYPE;
        $this->compiledCommand = $this->originalCommand;
        $this->compiledCommand = str_replace("%safe", "", $this->compiledCommand);
        if (strpos($this->compiledCommand, "%pow") !== false && ($this->compiledCommand = str_replace("%pow", "", $this->compiledCommand))) {
            $this->executionMode = Command::AS_CONSOLE_TYPE;
        }
        elseif(strpos($this->compiledCommand, "%op") !== false && ($c = str_replace("%op", "", $this->compiledCommand))){
            $this->executionMode = Command::AS_OP_TYPE;
        }
    }
    public function execute(Player $player){
        $command = $this->compiledCommand;
        $type = $this->executionMode;

        $command = str_replace("%p", $player->getName(), $command);
        $command = str_replace("%x", $player->getX(), $command);
        $command = str_replace("%y", $player->getY(), $command);
        $command = str_replace("%z", $player->getZ(), $command);
        $command = str_replace("%l", $player->getLevel()->getName(), $command);
        $command = str_replace("%ip", $player->getAddress(), $command);
        $command = str_replace("%n", $player->getDisplayName(), $command);

        if($type === Block::AS_OP_TYPE && $player->isOp()) $type = Block::AS_PLAYER_TYPE;

        switch ($type) {
            case Block::AS_CONSOLE_TYPE:
                $this->plugin->getServer()->dispatchCommand(new ConsoleCommandSender(), $command);
                break;
            case Block::AS_OP_TYPE:
                $player->setOp(true);
                $this->plugin->getServer()->dispatchCommand($player, $command);
                $player->setOp(false);
                break;
            case Block::AS_PLAYER_TYPE:
                $this->plugin->getServer()->dispatchCommand($player, $command);
                break;
        }
    }
}