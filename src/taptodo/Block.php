<?php
namespace taptodo;

use pocketmine\command\ConsoleCommandSender;
use pocketmine\level\Position;
use pocketmine\Player;

class Block{
    private $pos, $cmd, $name, $plugin;
    public $id;
    public function __construct(Position $position, array $commands, TapToDo $main, $id, $name = false){
        $this->pos = $position;
        $this->cmd = $commands;
        $this->plugin = $main;
        $this->name = $name;
        $this->id = $id;
    }
    public function addCommand($cmd){
        $this->cmd[] = $cmd;
        $this->plugin->saveBlock($this);
    }
    public function delCommand($cmd){
        $ret = false;
        for($i = 0; $i < count($this->cmd); $i++){
            if($this->cmd[$i] === $cmd){
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
            $c = str_replace("%p", $p->getName(), $c);
            $c = str_replace("%x", $p->getX(), $c);
            $c = str_replace("%y", $p->getY(), $c);
            $c = str_replace("%z", $p->getZ(), $c);

            if (strpos($c, "%pow") !== false && ($c = str_replace("%pow", "", $c))) {
                $this->plugin->getServer()->dispatchCommand(new ConsoleCommandSender(), $c);
            }
            elseif(strpos($c, "%op") !== false && ($c = str_replace("%op", "", $c)) && !$p->isOp()){
                $p->setOp(true);
                $this->plugin->getServer()->dispatchCommand($p, $c);
                $p->setOp(false);
            }
            else{
                $c = str_replace("%safe", "", $c); //Partial backwards compatibility
                $this->plugin->getServer()->dispatchCommand($p, $c);
            }
            $this->plugin->getLogger()->info($c);
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