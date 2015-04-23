<?php

use Illuminate\Database\Seeder;
use \App\Models\Console;

class ConsolesTableSeeder extends Seeder {

    public function run()
    {
        $now = new DateTime('now');
        $console = new Console;

        // Create xbox 360 console
        $console->name = "Xbox 360";
        $console->description  = "The Xbox 360 is a home video game console developed by Microsoft. As the successor to the original Xbox, it is the second console in the Xbox series. The Xbox 360 competes with Sony's PlayStation 3 and Nintendo's Wii as part of the seventh generation of video game consoles. The Xbox 360 was officially unveiled on MTV on May 12, 2005, with detailed launch and game information divulged later that month at the Electronic Entertainment Expo (E3).";
        $console->logo_url     = "http://upload.wikimedia.org/wikipedia/en/thumb/0/0c/Xbox_360_full_logo.svg/400px-Xbox_360_full_logo.svg.png";
        $console->release_date = new DateTime("2005-11-22");
        $console->platform_id = 1;
        $console->created_at = $now;
        $console->updated_at = $now;

        $console->save();

        $now = new DateTime('now');
        $console = new Console;

        // Create xbox one console
        $console->name = "Xbox One";
        $console->description  = "Xbox One is a home video game console developed and marketed by Microsoft. Announced on May 21, 2013, it is the successor to the Xbox 360 and is the third console in the Xbox family. It directly competes with Sony's PlayStation 4 and Nintendo's Wii U as part of the eighth generation of video game consoles. Xbox One was released across North America, several European markets, Australia, and New Zealand on November 22, 2013, and later in 26 other markets, including Japan, the remaining European markets, and the Middle East, in September 2014. It is also the first Xbox game console to be released in China, specifically in the Shanghai Free-Trade Zone. Microsoft and various publications have classified the device as an \"all-in-one entertainment system\", making it a competitor to other digital media players such as the Apple TV and the Google TV platforms.";
        $console->logo_url     = "http://upload.wikimedia.org/wikipedia/en/thumb/1/11/Xbox_One_logo.svg/500px-Xbox_One_logo.svg.png";
        $console->release_date = new DateTime("2013-11-22");
        $console->platform_id = 1;
        $console->created_at = $now;
        $console->updated_at = $now;

        $console->save();

        $now = new DateTime('now');
        $console = new Console;

        // Create ps3 console
        $console->name = "Playstation 3";
        $console->description  = "The PlayStation 3 (PS3) is a home video game console produced by Sony Computer Entertainment. It is the successor to PlayStation 2, as part of the PlayStation series. It competes with Microsoft's Xbox 360 and Nintendo's Wii as part of the seventh generation of video game consoles. It was first released on November 11, 2006, in Japan, with international markets following shortly thereafter.";
        $console->logo_url     = "http://upload.wikimedia.org/wikipedia/commons/6/68/PlayStation_3_Logo_neu.svg";
        $console->release_date = new DateTime("2006-11-17");
        $console->platform_id = 2;
        $console->created_at = $now;
        $console->updated_at = $now;

        $console->save();

        $now = new DateTime('now');
        $console = new Console;

        // Create ps4 console
        $console->name = "Playstation 4";
        $console->description  = "The PlayStation 4 (officially abbreviated as PS4) is a home video game console from Sony Computer Entertainment. Announced as the successor to the PlayStation 3 during a press conference on February 20, 2013, it was launched on November 15, 2013 in North America, and November 29, 2013 in Europe and Australia. It competes with Nintendo's Wii U and Microsoft's Xbox One, as one of the eighth generation of video game consoles.";
        $console->logo_url     = "http://upload.wikimedia.org/wikipedia/commons/thumb/8/87/PlayStation_4_logo_and_wordmark.svg/500px-PlayStation_4_logo_and_wordmark.svg.png";
        $console->release_date = new DateTime("2013-11-15");
        $console->platform_id = 2;
        $console->created_at = $now;
        $console->updated_at = $now;

        $console->save();
    }

}