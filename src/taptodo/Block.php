<?php
namespace taptodo;

use pocketmine\command\ConsoleCommandSender;
use pocketmine\level\Position;
use pocketmine\Player;

class Block{
    const AS_CONSOLE_TYPE = 0;
    const AS_PLAYER_TYPE = 1;
    const AS_OP_TYPE = 2;

    private $pos, $cmd, $name, $plugin;
    public $id;
    public function __construct(Position $position, array $commands, TapToDo $main, $id, $name = false){
        $this->pos = $position;
        $this->cmd = [];
        $this->plugin = $main;
        $this->name = $name;
        $this->id = $id;

        $this->addCommands($commands);
    }
    public function addCommands($cmds){
        if(!is_array($cmds)){
            $cmds = [$cmds];
        }
        foreach ($cmds as $c) {
            $type = Block::AS_PLAYER_TYPE;
            $c = str_replace("%safe", "", $c);
            if (strpos($c, "%pow") !== false && ($c = str_replace("%pow", "", $c))) {
                $type = Block::AS_CONSOLE_TYPE;
            }
            elseif(strpos($c, "%op") !== false && ($c = str_replace("%op", "", $c))){
                $type = Block::AS_OP_TYPE;
            }
            $this->cmd[] = [$c, $type];
        }
        $this->plugin->saveBlock($this);
    }
    public function addCommand($cmd){
        $this->addCommands([$cmd]);
    }
    public function delCommand($cmd){
        $ret = false;
        for($i = 0; $i < count($this->cmd); $i++){
            if($this->cmd[$i][0] === $cmd){
                unset($this->cmd[$i]);
                $ret = true;
            }
        }
        if($ret){
            $this->plugin->saveBlock($this);
        }
        return $ret;
    }
    public function runCommands(Player $p){
        foreach($this->cmd as $c){
            $type = $c[1];
            $c = $c[0];

            $c = str_replace("%p", $p->getName(), $c);
            $c = str_replace("%x", $p->getX(), $c);
            $c = str_replace("%y", $p->getY(), $c);
            $c = str_replace("%z", $p->getZ(), $c);

            if($type === Block::AS_OP_TYPE && $p->isOp()) $type = Block::AS_PLAYER_TYPE;

            switch ($type) {
                case Block::AS_CONSOLE_TYPE:
                    $this->plugin->getServer()->dispatchCommand(new ConsoleCommandSender(), $c);
                    break;
                case Block::AS_OP_TYPE:
                    $p->setOp(true);
                    $this->plugin->getServer()->dispatchCommand($p, $c);
                    $p->setOp(false);
                    break;
                case Block::AS_PLAYER_TYPE:
                    $this->plugin->getServer()->dispatchCommand($p, $c);
                    break;
            }
            //$this->plugin->getLogger()->info($c);
        }
    }
    public function nameBlock($name){
        $this->name = $name;
    }
    public function getCommands(){
        return $this->cmd;
    }
    public function getName(){
        return $this->name;
    }
    public function getPos(){
        return $this->pos;
    }
    public function toArray(){
        $arr = array(
            'x' => $this->getPos()->getX(),
            'y' => $this->getPos()->getY(),
            'z' => $this->getPos()->getZ(),
            'level' => $this->getPos()->getLevel()->getName(),
            'commands' => $this->getCommands());
        if($this->name !== false) $arr["name"] = $this->name;
        return $arr;
    }
}
