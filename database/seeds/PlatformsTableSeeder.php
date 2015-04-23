<?php

use Illuminate\Database\Seeder;
use App\Models\Platform;

class PlatformsTableSeeder extends Seeder {

    public function run()
    {
        $now  = date("Y-m-d h:i:s");
        $platform = new Platform;

        // Create xbox live
        $platform->name = "Xbox Live";
        $platform->description  = "Xbox Live (trademarked as Xbox LIVE) is an online multiplayer gaming and digital media delivery service created and operated by Microsoft. It was first made available to the Xbox system in November 2002. An updated version of the service became available for the Xbox 360 console at the system's launch in November 2005, and a further enhanced version was released in 2013 with the Xbox One.";
        $platform->logo_url     = "http://upload.wikimedia.org/wikipedia/en/1/16/Xbox_Live_Logo.png";
        $platform->created_at = $now;
        $platform->updated_at = $now;

        $platform->save();

        $now  = date("Y-m-d h:i:s");
        $platform = new Platform;

        // Create psn
        $platform->name = "PlayStation Network";
        $platform->description  = "PlayStation Network, officially abbreviated PSN, is an entertainment service provided by Sony Computer Entertainment for use with the PlayStation family of video game consoles, Sony tablet and smartphones.[3] The PlayStation Network encompasses online gaming, music, television and movie streaming services.";
        $platform->logo_url     = "http://all3games.com/wp-content/uploads/2013/11/psn.png";
        $platform->created_at = $now;
        $platform->updated_at = $now;

        $platform->save();
    }

}