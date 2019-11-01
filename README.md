# TapToDo
Source of the TapToDo plugin for PocketMine

## Introduction

TapToDo is a plugin intended for server admins that want a better way for players to interact with their server. You can make any kind of block into a TapToDo block by using a few simple commands detailed below.

## Commands

There are 2 ways to add commands onto a block:

### Nearby Block Management:
Blocks can be specified by tapping them after executing the command

|     Command      |                    Description                      |
|------------------|-----------------------------------------------------|
| /t add <command> | Adds a command to the specified block               |
| /t del <command> | Deletes the command from the specified block        |
| /t delall        | Removes all commands from the specified block       |
| /t name <name>   | Allows you to name the block, for remote management |
| /t list          | Lists all commands assigned to the specified block  |

### Remote Block Management:
Blocks can be specified by inputting their name before the subcommand

|         Command          |                    Description                      |
|--------------------------|-----------------------------------------------------|
| /tr <name> add <command> | Adds a command to the specified block               |
| /tr <name> del <command> | Deletes the command from the specified block        |
| /tr <name> delall        | Removes all commands from the specified block       |
| /tr <name> name <name>   | Allows you to name the block, for remote management |
| /tr <name> list          | Lists all commands assigned to the specified block  |
