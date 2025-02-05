<?php

namespace App\Console\Commands;

use App\Models\Menu;
use Illuminate\Console\Command;

class ResetDiscount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-discount';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $currentDate = now()->format('Y-m-d');

        $menus = Menu::whereNotNull('end_date')->get();

        foreach($menus as $menu){

            if($menu->end_date < $currentDate){
                $menu->update(['end_date'=>null,"start_date"=>null,"discount"=>0]);
                $this->info("Discount for menu ID {$menu->id} has been reset to zero.");
            }

        }

        $this->info('Discount reset process completed.');

    }
}
