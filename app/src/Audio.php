<?php

declare(ticks = 1);

namespace Game;

class Audio
{
    /** @var array<string, int> */
    static private array $pids;

    static public function startMusic(string $name): void
    {
        self::$pids[$name] = pcntl_fork();

        if(self::$pids[$name] > 0) {
            $command = "play ./resources/sounds/$name";
            $playProcess = proc_open($command, [
                ['pipe' ,'r'],
                ['pipe', 'w'],
                ['pipe', 'w']
            ], $pipes);

            pcntl_signal(SIGTERM, function() use ($playProcess) {
                if (is_resource($playProcess)) {
                    $status = proc_get_status($playProcess);
                    posix_kill($status['pid'], SIGTERM);
                    proc_close($playProcess);
                }
                exit();
            });

            if (is_resource($playProcess)) {
                while (true) {
                    sleep(1);
                }
            }
        }
    }

    static public function stopMusic(string $name): void
    {
        posix_kill(self::$pids[$name], SIGTERM);
        pcntl_wait($status);
    }
}
